<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MyTicketsController extends Controller
{
    private const PER_PAGE = 8;

    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Mes billets : onglets (à venir / passés / annulés), même logique que « Événements ».
     * Données : GET users/me/tickets si disponible, sinon placeholders (à retirer quand l’API est stable).
     */
    public function index(Request $request, string $locale): View|RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $token = $this->apiService->getUserToken();
        $searchRaw = $request->query('query');
        $searchForApi = is_string($searchRaw) ? $searchRaw : '';
        $raw = $this->fetchTicketsFromApi($token, $searchForApi);

        $normalized = array_map(fn (array $t) => $this->normalizeTicket($t), $raw);

        $activeTab = $request->query('tab', 'upcoming');
        if (! in_array($activeTab, ['upcoming', 'past', 'cancelled'], true)) {
            $activeTab = 'upcoming';
        }

        $upcoming  = array_values(array_filter($normalized, fn ($t) => ($t['bucket'] ?? '') === 'upcoming'));
        $past      = array_values(array_filter($normalized, fn ($t) => ($t['bucket'] ?? '') === 'past'));
        $cancelled = array_values(array_filter($normalized, fn ($t) => ($t['bucket'] ?? '') === 'cancelled'));

        $pageUpcoming  = max(1, (int) $request->query('page_upcoming', 1));
        $pagePast      = max(1, (int) $request->query('page_past', 1));
        $pageCancelled = max(1, (int) $request->query('page_cancelled', 1));

        $ticketPaginators = [
            'upcoming'  => $this->paginateArray($upcoming, self::PER_PAGE, $pageUpcoming, 'page_upcoming', $request),
            'past'      => $this->paginateArray($past, self::PER_PAGE, $pagePast, 'page_past', $request),
            'cancelled' => $this->paginateArray($cancelled, self::PER_PAGE, $pageCancelled, 'page_cancelled', $request),
        ];

        return view('dashboard.main.tickets', [
            'locale'           => $locale,
            'ticketPaginators' => $ticketPaginators,
            'activeTab'        => $activeTab,
        ]);
    }

    public function transfer(Request $request, string $locale, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $token = $this->apiService->getUserToken();
        if (!$token) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $response = $this->apiService->makeApiRequest(
            'POST',
            "users/me/tickets/{$id}/transfer",
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
                'json' => [
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ],
            ],
            false
        );

        if (!$response->successful()) {
            $message = data_get($response->json(), 'message', __('An error occurred.'));
            return back()->with('error', $message);
        }

        return back()->with('success', __('Ticket transferred successfully.'));
    }

    public function cancel(Request $request, string $locale, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $token = $this->apiService->getUserToken();
        if (!$token) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $response = $this->apiService->makeApiRequest(
            'POST',
            "users/me/tickets/{$id}/cancel",
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
                'json' => [
                    'reason' => $validated['reason'],
                    'password' => $validated['password'],
                ],
            ],
            false
        );

        if (!$response->successful()) {
            $message = data_get($response->json(), 'message', __('An error occurred.'));
            return back()->with('error', $message);
        }

        return back()->with('success', __('Ticket cancelled successfully.'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchTicketsFromApi(string $token, string $searchQuery = ''): array
    {
        $query = [
            'per_page' => 100,
        ];
        $q = trim($searchQuery);
        if ($q !== '') {
            $query['query'] = $q;
        }

        $response = $this->apiService->makeApiRequest(
            'GET',
            'users/me/tickets',
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
                'query' => $query,
            ],
            false
        );

        if (! $response->successful()) {
            return [];
        }

        $json = $response->json() ?? [];
        $data = $json['data'] ?? [];
        if (! is_array($data)) {
            return [];
        }
        if (isset($data['items']) && is_array($data['items'])) {
            return $data['items'];
        }
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }
        if ($data !== [] && array_key_exists(0, $data)) {
            return $data;
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $t
     * @return array<string, mixed>
     */
    private function normalizeTicket(array $t): array
    {
        $status = strtolower((string) ($t['status'] ?? 'valid'));
        $start  = $t['occurrence_start']
            ?? $t['event_date']
            ?? data_get($t, 'item_occurrence.start_date')
            ?? data_get($t, 'occurrence.start_date')
            ?? data_get($t, 'event.start_date');

        $at = null;
        if (is_string($start) && $start !== '') {
            try {
                $at = Carbon::parse($start);
            } catch (\Throwable) {
                $at = null;
            }
        }

        // Onglets : annulés (statut), passés (événement passé ou ticket validé / expiré), sinon à venir.
        $bucket = 'upcoming';
        if (in_array($status, ['cancelled', 'refunded', 'void'], true)) {
            $bucket = 'cancelled';
        } elseif (in_array($status, ['validated', 'expired'], true)) {
            $bucket = 'past';
        } elseif ($at !== null && $at->isPast()) {
            $bucket = 'past';
        }

        $event = is_array($t['event'] ?? null) ? $t['event'] : [];
        if ($event === []) {
            $event = is_array(data_get($t, 'item_occurrence.item')) ? data_get($t, 'item_occurrence.item') : [];
        }
        $cover = $event['cover_url'] ?? data_get($t, 'item_occurrence.item.cover_url') ?? $t['cover_url'] ?? null;
        $cover = is_string($cover) ? votix_media_url($cover) : null;

        $cur = strtoupper((string) ($t['currency'] ?? $event['currency'] ?? 'XOF'));

        return array_merge($t, [
            'bucket'            => $bucket,
            'occurrence_start'  => $start,
            'event_title'       => $event['title'] ?? $t['event_title'] ?? '—',
            'event_cover'       => $cover,
            'event_city'        => $event['city'] ?? data_get($t, 'item_occurrence.item.city') ?? $t['city'] ?? null,
            'event_venue'       => $event['venue'] ?? $event['address'] ?? data_get($t, 'item_occurrence.item.address') ?? $t['venue'] ?? null,
            'category_label'    => $t['category'] ?? data_get($t, 'ticket_type.name') ?? '—',
            'event_category_label' => data_get($t, 'item_occurrence.item.category_name') ?? data_get($t, 'event.category_name'),
            'customer_email'    => $t['email'] ?? null,
            'price_amount'      => (float) ($t['price'] ?? data_get($t, 'ticket_type.price') ?? 0),
            'currency_code'     => $cur,
            'display_currency'  => display_currency_label($cur),
            'seat_label'        => $t['seat'] ?? $t['seat_number'] ?? null,
            'gate_label'        => $t['gate'] ?? $t['entrance'] ?? null,
            'order_reference'   => $t['order_number'] ?? $t['order_reference'] ?? data_get($t, 'order.number') ?? $t['order_id'] ?? data_get($t, 'order.reference'),
            'qr_value'          => (string) ($t['qr_encoded_data'] ?? $t['code'] ?? $t['ticket_code'] ?? $t['id'] ?? ''),
        ]);
    }

    /**
     * @param  array<int, mixed>  $items
     */
    private function paginateArray(array $items, int $perPage, int $page, string $pageName, Request $request): LengthAwarePaginator
    {
        $total = count($items);
        $slice = array_slice($items, ($page - 1) * $perPage, $perPage);

        return (new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            [
                'path'     => $request->url(),
                'pageName' => $pageName,
            ]
        ))->withQueryString();
    }
}
