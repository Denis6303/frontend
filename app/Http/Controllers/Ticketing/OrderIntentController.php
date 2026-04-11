<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseHandler;
use App\Services\OrderIntentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OrderIntentController extends Controller
{
    use ApiResponseHandler;

    public function __construct(
        protected OrderIntentService $orderIntents
    ) {}

    protected function userEmail(): ?string
    {
        $user = Session::get(config('votix_api.session_user_key'));
        if (! is_array($user)) {
            return null;
        }

        $email = $user['email'] ?? ($user['user']['email'] ?? ($user['mail'] ?? null));
        if (! is_string($email) || $email === '') {
            return null;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    protected function customerId(): ?int
    {
        $user = Session::get(config('votix_api.session_user_key'));
        if (! is_array($user)) {
            return null;
        }
        $id = $user['id'] ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    /**
     * Stocke la sélection (séance + quantités) puis redirige vers le formulaire de création d'intention.
     */
    public function prepare(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_occurrence_id' => 'required|integer|min:1',
            'tickets_json' => 'required|string',
            'event_title' => 'nullable|string|max:255',
            'occurrence_start_date' => 'nullable|string|max:255',
            'tickets_ui_json' => 'nullable|string',
            'coupon_code' => 'nullable|string|max:64',
            'return_url' => 'nullable|string|max:2048',
        ]);

        $decoded = json_decode($validated['tickets_json'], true);
        if (! is_array($decoded)) {
            return redirect()->back()->withErrors(['form' => __('Invalid ticket selection.')]);
        }

        $tickets = [];
        foreach ($decoded as $typeId => $qty) {
            $q = (int) $qty;
            if ($q > 0) {
                $tickets[(string) $typeId] = $q;
            }
        }

        if ($tickets === []) {
            return redirect()->back()->withErrors(['form' => __('Please select at least one ticket.')]);
        }

        $customerId = $this->customerId();
        if ($customerId === null) {
            return redirect()
                ->route('login', ['locale' => $request->route('locale', app()->getLocale())])
                ->withErrors(['form' => __('Please sign in to continue.')]);
        }

        $email = $this->userEmail();
        if ($email === null) {
            return redirect()->back()->withErrors(['form' => __('Please sign in again.')]);
        }

        $uiTickets = [];
        if (! empty($validated['tickets_ui_json'])) {
            $decodedUi = json_decode($validated['tickets_ui_json'], true);
            if (is_array($decodedUi)) {
                $uiTickets = $decodedUi;
            }
        }

        $locale = $request->route('locale', app()->getLocale());

        $payload = [
            'type' => 'online',
            'event_occurrence_id' => (int) $validated['event_occurrence_id'],
            'tickets' => (object) $tickets,
            'delivery_method' => 'email',
            'customer_id' => $customerId,
            'email' => $email,
        ];
        if (! empty($validated['coupon_code'])) {
            $payload['coupon_code'] = $validated['coupon_code'];
        }

        $result = $this->orderIntents->create($payload);
        if (! $result['ok'] || ! is_array($result['data'])) {
            $errors = $this->extractErrorsFromApiResponse($result['raw']);

            return redirect()->back()->withErrors($errors);
        }

        $data = $result['data'];
        $key = $data['key'] ?? null;
        if (! is_string($key) || $key === '') {
            return redirect()->back()->withErrors(['form' => __('Could not create order. Please try again.')]);
        }

        $contextMap = Session::get('order_intent_context', []);
        if (! is_array($contextMap)) {
            $contextMap = [];
        }
        $contextMap[$key] = [
            'event_title' => is_string($validated['event_title'] ?? null) ? $validated['event_title'] : null,
            'occurrence_start_date' => is_string($validated['occurrence_start_date'] ?? null) ? $validated['occurrence_start_date'] : null,
            'tickets' => $uiTickets,
            'email' => $email,
            'return_url' => is_string($validated['return_url'] ?? null) ? $validated['return_url'] : null,
        ];
        Session::put('order_intent_context', $contextMap);

        Session::put('order_intent_key', $key);

        return redirect()
            ->route('ticketing.checkout.show', ['locale' => $locale, 'key' => $key]);
    }

    /**
     * Formulaire livraison + création d'intention (appel API create).
     */
    // L'étape intermédiaire de confirmation a été supprimée :
    // `prepare()` crée l'intention directement puis redirige vers `/checkout/{key}`.

    /**
     * Récap intention + paiement.
     */
    public function show(Request $request, string $locale, string $key): RedirectResponse|View
    {
        $customerId = $this->customerId();
        $result = $this->orderIntents->show($key, $customerId);
        if (! $result['ok'] || ! is_array($result['data'])) {
            $errors = $this->extractErrorsFromApiResponse($result['raw']);

            return redirect()
                ->route('ticketing.events', ['locale' => $locale])
                ->withErrors($errors);
        }

        $intent = $result['data'];
        $status = (string) ($intent['status'] ?? 'pending');
        $amount = (float) ($intent['amount'] ?? 0);

        $email = $this->userEmail();
        $contextMap = Session::get('order_intent_context', []);
        $uiContext = is_array($contextMap) && isset($contextMap[$key]) ? $contextMap[$key] : [];

        if ($status !== 'pending') {
            return view('ticketing.checkout.show', [
                'intent' => $intent,
                'paymentMethods' => [],
                'readOnly' => true,
                'locale' => $locale,
                'uiContext' => is_array($uiContext) ? $uiContext : [],
                'userEmail' => $email,
                'checkoutKey' => $key,
            ]);
        }

        $methods = $this->orderIntents->paymentMethods();
        $paymentMethods = $methods['ok'] ? $methods['data'] : $this->fallbackPaymentMethods();
        if ($amount <= 0.00001) {
            $hasFree = false;
            foreach ($paymentMethods as $m) {
                if (is_array($m) && ($m['code'] ?? '') === 'free') {
                    $hasFree = true;
                    break;
                }
            }
            if (! $hasFree) {
                $paymentMethods[] = ['code' => 'free', 'name' => __('Free')];
            }
        }

        return view('ticketing.checkout.show', [
            'intent' => $intent,
            'paymentMethods' => $paymentMethods,
            'readOnly' => false,
            'locale' => $locale,
            'returnSuccessUrl' => $this->returnUrl($locale, $key, 'success'),
            'returnFailureUrl' => $this->returnUrl($locale, $key, 'failure'),
            'uiContext' => is_array($uiContext) ? $uiContext : [],
            'userEmail' => $email,
            'checkoutKey' => $key,
        ]);
    }

    /**
     * Traitement checkout (gratuit ou mobile money).
     */
    public function pay(Request $request, string $locale, string $key): RedirectResponse
    {
        $customerId = $this->customerId();
        $loaded = $this->orderIntents->show($key, $customerId);
        if (! $loaded['ok'] || ! is_array($loaded['data'])) {
            $errors = $this->extractErrorsFromApiResponse($loaded['raw']);

            return redirect()->back()->withErrors($errors);
        }

        $intent = $loaded['data'];
        $status = is_string($intent['status'] ?? null) ? $intent['status'] : '';
        if ($status !== 'pending') {
            return redirect()->back()->withErrors(['form' => __('This order can no longer be paid.')]);
        }

        $amount = (float) ($intent['amount'] ?? 0);

        $validated = $request->validate([
            'payment_method' => 'required|string|max:64',
            'accept_terms' => 'required|accepted',
            'country' => 'nullable|string|size:2',
            'operator' => 'nullable|string|max:32',
            'phone_number' => 'nullable|string|max:32',
        ]);

        $method = $validated['payment_method'];

        if ($method === 'free' && $amount > 0.00001) {
            return redirect()->back()->withErrors(['form' => __('Free payment is only available when the amount is zero.')]);
        }

        if (in_array($method, ['yass', 'flooz'], true)) {
            $wallet = $request->validate([
                'phone_number' => 'required|string|max:32',
            ]);
            $validated['phone_number'] = preg_replace('/\s+/', '', $wallet['phone_number']);
            $validated['country'] = strtoupper((string) ($validated['country'] ?? 'TG'));
            if (strlen($validated['country']) !== 2) {
                $validated['country'] = 'TG';
            }
            $validated['operator'] = $validated['operator'] ?? match ($method) {
                'flooz' => 'FLOOZ',
                default => 'YASS',
            };
        }

        $payload = [
            'payment_method' => $method,
            'accept_terms' => true,
        ];

        if ($method !== 'free') {
            if ($amount <= 0) {
                return redirect()->back()->withErrors(['form' => __('Invalid amount for this payment method.')]);
            }
            $payload['success_url'] = $this->returnUrl($locale, $key, 'success');
            $payload['failure_url'] = $this->returnUrl($locale, $key, 'failure');
            if (in_array($method, ['yass', 'flooz'], true)) {
                $payload['country'] = $validated['country'];
                $payload['operator'] = $validated['operator'];
                $payload['phone_number'] = $validated['phone_number'];
            }
        }

        $result = $this->orderIntents->checkout($key, $payload);
        if (! $result['ok'] || ! is_array($result['data'])) {
            $errors = $this->extractErrorsFromApiResponse($result['raw']);

            return redirect()->back()->withErrors($errors);
        }

        $data = $result['data'];
        $checkout = is_array($data['checkout'] ?? null) ? $data['checkout'] : [];
        $next = is_array($checkout['next_action'] ?? null) ? $checkout['next_action'] : [];
        $type = $next['type'] ?? null;

        if ($type === 'redirect' && ! empty($next['url']) && is_string($next['url'])) {
            return redirect()->away($next['url']);
        }

        return redirect()->route('ticketing.checkout.return', [
            'locale' => $locale,
            'key' => $key,
            'outcome' => 'success',
        ]);
    }

    /**
     * Retour PSP / page de vérification : appelle verify.
     */
    public function returnPage(Request $request, string $locale, string $key): View
    {
        $customerId = $this->customerId();
        $outcome = $request->query('outcome');

        $verify = $this->orderIntents->verify($key, $customerId);
        $paid = false;
        $intent = null;

        if ($verify['ok'] && is_array($verify['data'])) {
            $intent = $verify['data']['intent'] ?? $verify['data'];
            if (isset($verify['data']['paid'])) {
                $paid = (bool) $verify['data']['paid'];
            } elseif (is_array($intent)) {
                $paid = ($intent['status'] ?? '') === 'confirmed';
            }
        }

        return view('ticketing.checkout.return', [
            'intent' => is_array($intent) ? $intent : [],
            'paid' => $paid,
            'outcome' => is_string($outcome) ? $outcome : null,
            'verifyRaw' => $verify['raw'] ?? [],
            'verifyOk' => $verify['ok'],
            'locale' => $locale,
        ]);
    }

    public function cancel(Request $request, string $locale, string $key): RedirectResponse
    {
        $customerId = $this->customerId();
        $result = $this->orderIntents->cancel($key, $customerId);
        if (! $result['ok']) {
            $errors = $this->extractErrorsFromApiResponse($result['raw']);

            return redirect()->back()->withErrors($errors);
        }

        Session::forget('order_intent_key');

        return redirect()
            ->route('ticketing.events', ['locale' => $locale])
            ->with('success', $result['message'] ?? __('Order cancelled.'));
    }

    protected function returnUrl(string $locale, string $key, string $outcome): string
    {
        return route('ticketing.checkout.return', [
            'locale' => $locale,
            'key' => $key,
            'outcome' => $outcome,
        ], true);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fallbackPaymentMethods(): array
    {
        return [
            ['code' => 'yass', 'name' => 'Yass (Mixx)'],
            ['code' => 'flooz', 'name' => 'Flooz (Moov)'],
            ['code' => 'djam', 'name' => 'Djamo'],
            ['code' => 'visa', 'name' => 'Visa'],
            ['code' => 'free', 'name' => __('Free')],
        ];
    }
}
