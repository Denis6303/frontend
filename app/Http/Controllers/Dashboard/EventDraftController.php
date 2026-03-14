<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseHandler;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EventDraftController extends Controller
{
    use ApiResponseHandler;

    public function __construct(
        protected ApiService $apiService
    ) {
    }

    /**
     * Étape 1 : création / mise à jour du brouillon.
     * Endpoint backend : POST /api/v1/event-drafts/step1
     */
    public function storeStep1(Request $request, string $locale)
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:50'],
            'category_id'     => ['required', 'integer'],
            'attendance_type' => ['required', 'in:in_person,online'],
            'description'     => ['nullable', 'string', 'max:5000'],
            'image'           => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ]);

        // draft_id facultatif (mise à jour)
        $draftId = $request->input('draft_id');

        $fields = [
            'draft_id'        => $draftId,
            'title'           => $validated['title'],
            'category_id'     => $validated['category_id'],
            'description'     => $validated['description'] ?? null,
            'attendance_type' => $validated['attendance_type'],
        ];

        $parts = ApiService::buildMultipart(
            $fields,
            ['image' => $request->file('image')]
        );

        $response = $this->apiService->makeMultipartRequest(
            'POST',
            'event-drafts/step1',
            $parts,
            false // utiliser le token utilisateur (auth:api)
        );

        $json  = $response->json() ?? [];
        $draft = $json['data'] ?? null;

        if ($response->successful() && ($json['success'] ?? false) && is_array($draft)) {
            $draftId = $draft['id'] ?? $draftId;
            if ($draftId) {
                Session::put('event_draft.current_id', $draftId);
            }

            return redirect()
                ->route('dashboard.events.draft.create.step2', ['locale' => $locale, 'draft_id' => $draftId])
                ->with('success', $json['message'] ?? __('Step 1 saved successfully.'));
        }

        return $this->handleApiResponse($response);
    }

    /**
     * Étape 2 : localisation, devise, dates.
     * Endpoint backend : POST /api/v1/event-drafts/{id}/step2
     */
    public function storeStep2(Request $request, string $locale)
    {
        $draftId = $request->input('draft_id') ?: Session::get('event_draft.current_id');

        $validated = $request->validate([
            'country_code'        => ['required', 'in:tg,other'],
            'currency'            => ['required', 'in:XOF,EUR,USD'],
            'address'             => ['nullable', 'string', 'max:100'],
            'city'                => ['nullable', 'string', 'max:50'],
            'start_dates'         => ['required', 'array', 'min:1'],
            'start_dates.*'       => ['required', 'string'],
            'end_dates'           => ['required', 'array', 'min:1'],
            'end_dates.*'         => ['required', 'string'],
        ]);

        $startDates = array_map(function ($v) {
            return strlen($v) === 16 ? $v . ':00' : $v;
        }, $validated['start_dates']);
        $endDates = array_map(function ($v) {
            return strlen($v) === 16 ? $v . ':00' : $v;
        }, $validated['end_dates']);

        $payload = [
            'country_code' => $validated['country_code'],
            'currency'     => $validated['currency'],
            'address'      => $validated['address'] ?? null,
            'city'         => $validated['city'] ?? null,
            'start_dates'  => $startDates,
            'end_dates'    => $endDates,
        ];

        $response = $this->apiService->makeApiRequest(
            'POST',
            "event-drafts/{$draftId}/step2",
            [
                'json'    => $payload,
                'headers' => ['Accept' => 'application/json'],
            ],
            false
        );

        $json = $response->json() ?? [];

        if ($response->successful() && ($json['success'] ?? false)) {
            return redirect()
                ->route('dashboard.events.draft.create.step3', ['locale' => $locale, 'draft_id' => $draftId])
                ->with('success', $json['message'] ?? __('Step 2 saved successfully.'));
        }

        return $this->handleApiResponse($response);
    }

    /**
     * Étape 3 : tickets & tarification.
     * Endpoint backend : POST /api/v1/event-drafts/{id}/step3
     */
    public function storeStep3(Request $request, string $locale)
    {
        $draftId = $request->input('draft_id') ?: Session::get('event_draft.current_id');

        $validated = $request->validate([
            'free_event'                       => ['required'],
            'tickets'                          => ['required', 'array', 'min:1'],
            'tickets.*.name'                   => ['required', 'string', 'max:50'],
            'tickets.*.price'                  => ['required'],
            'tickets.*.online_quantity'        => ['required', 'integer', 'min:1'],
            'tickets.*.print_quantity'         => ['nullable', 'integer', 'min:0'],
            'tickets.*.description'            => ['nullable', 'string', 'max:200'],
            'tickets.*.general_conditions'     => ['nullable', 'string', 'max:1000'],
        ]);

        $payload = [
            'free_event' => ApiService::boolToString($validated['free_event']),
            'tickets'    => $validated['tickets'],
        ];

        $response = $this->apiService->makeApiRequest(
            'POST',
            "event-drafts/{$draftId}/step3",
            [
                'json'    => $payload,
                'headers' => ['Accept' => 'application/json'],
            ],
            false
        );

        $json = $response->json() ?? [];

        if ($response->successful() && ($json['success'] ?? false)) {
            Session::put('event_draft.summary_data', $json['data'] ?? []);

            return redirect()
                ->route('dashboard.events.draft.create.step4', ['locale' => $locale, 'draft_id' => $draftId])
                ->with('success', $json['message'] ?? __('Step 3 saved successfully.'));
        }

        return $this->handleApiResponse($response);
    }

    /**
     * Afficher l'étape 4 (résumé + publication). Utilise les données en session (réponse du POST step3)
     * ou tente GET event-drafts/{id} en secours.
     */
    public function showStep4(Request $request, string $locale)
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $draftId = $request->input('draft_id') ?: Session::get('event_draft.current_id');
        if (! $draftId) {
            return redirect()->route('dashboard.events.draft.create.step1', ['locale' => $locale]);
        }

        $draft = Session::get('event_draft.summary_data', []);

        if (empty($draft)) {
            $response = $this->apiService->makeApiRequest(
                'GET',
                "event-drafts/{$draftId}",
                ['headers' => ['Accept' => 'application/json']],
                false
            );
            if ($response->successful()) {
                $json = $response->json() ?? [];
                if ($json['success'] ?? false) {
                    $draft = $json['data'] ?? [];
                }
            }
        }

        return view('dashboard.events.draft.create-step4', [
            'locale' => $locale,
            'draft'  => $draft,
        ]);
    }

    /**
     * Étape 4 : finalisation et publication.
     * Endpoint backend : POST /api/v1/event-drafts/{id}/finalize
     */
    public function finalize(Request $request, string $locale)
    {
        $draftId = $request->input('draft_id') ?: Session::get('event_draft.current_id');

        $validated = $request->validate([
            'publish_now' => ['required'],
            'scheduled_at'=> ['nullable', 'string'],
            'is_private'  => ['required'],
        ]);

        $payload = [
            'publish_now' => ApiService::boolToString($validated['publish_now']),
            'scheduled_at'=> $validated['scheduled_at'] ?? null,
            'is_private'  => ApiService::boolToString($validated['is_private']),
        ];

        $response = $this->apiService->makeApiRequest(
            'POST',
            "event-drafts/{$draftId}/finalize",
            [
                'json'    => $payload,
                'headers' => ['Accept' => 'application/json'],
            ],
            false
        );

        $json = $response->json() ?? [];

        if ($response->successful() && ($json['success'] ?? false)) {
            $eventId = $json['data']['event']['id'] ?? null;

            Session::forget(['event_draft.current_id', 'event_draft.summary_data']);

            $redirect = $eventId
                ? route('ticketing.events.show', ['locale' => $locale, 'id' => $eventId])
                : route('dashboard.events', ['locale' => $locale]);

            return redirect($redirect)
                ->with('success', $json['message'] ?? __('Event created successfully.'));
        }

        return $this->handleApiResponse($response);
    }
}

