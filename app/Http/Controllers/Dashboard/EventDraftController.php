<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseHandler;
use App\Services\ApiService;
use Illuminate\Http\RedirectResponse;
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
     * GET event-drafts/{id} — données complètes du brouillon pour préremplissage.
     */
    protected function fetchDraft(?string $draftId): ?array
    {
        if (! $draftId) {
            return null;
        }

        $response = $this->apiService->makeApiRequest(
            'GET',
            "event-drafts/{$draftId}",
            [
                'headers' => ['Accept' => 'application/json'],
            ],
            false
        );

        if (! $response->successful()) {
            return null;
        }

        $json = $response->json() ?? [];

        if (array_key_exists('success', $json) && $json['success'] === false) {
            return null;
        }

        $raw = $json['data'] ?? null;
        if (! is_array($raw)) {
            return null;
        }

        $raw = $this->unwrapDraftPayload($raw, $draftId);

        return $raw;
    }

    /**
     * Ramène toujours un tableau brouillon plat (event, cover_url, data, id, …).
     * Gère : objet seul, enveloppe { data: { event } }, liste paginée Laravel { current_page, data: [ {...} ] }.
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    protected function unwrapDraftPayload(array $raw, ?string $draftId): array
    {
        $pickDraftFromList = static function (array $items, ?string $id): ?array {
            if ($items === [] || ! array_is_list($items)) {
                return null;
            }
            $first = $items[0] ?? null;
            if (! is_array($first) || (! isset($first['event']) && ! isset($first['cover_url']))) {
                return null;
            }
            if ($id !== null && $id !== '') {
                foreach ($items as $item) {
                    if (is_array($item) && (string) ($item['id'] ?? '') === (string) $id) {
                        return $item;
                    }
                }
            }

            return $first;
        };

        // GET event-drafts : { current_page, data: [ { id, event, cover_url, data }, ... ], ... }
        if (isset($raw['current_page'], $raw['data']) && is_array($raw['data'])) {
            $picked = $pickDraftFromList($raw['data'], $draftId);
            if ($picked !== null) {
                return $picked;
            }
        }

        // Racine : data = liste de brouillons (sans métadonnées de pagination)
        if (isset($raw['data']) && is_array($raw['data']) && array_is_list($raw['data'])) {
            $picked = $pickDraftFromList($raw['data'], $draftId);
            if ($picked !== null) {
                return $picked;
            }
        }

        // Enveloppe : GET event-drafts/:id → { id, cover_url, category_id, data: { event, tickets, … } }
        // sans clé `event` à la racine : on « aplatit » vers l’objet interne mais on conserve les champs API racine.
        if (! isset($raw['event']) && isset($raw['data']) && is_array($raw['data']) && ! array_is_list($raw['data'])) {
            $inner = $raw['data'];
            if (isset($inner['event']) || isset($inner['cover_url']) || isset($inner['id'])) {
                foreach (['cover_url', 'category_id', 'id', 'current_step', 'event_id', 'user_id'] as $k) {
                    if (array_key_exists($k, $raw) && ! array_key_exists($k, $inner)) {
                        $inner[$k] = $raw[$k];
                    }
                }

                return $inner;
            }
        }

        return $raw;
    }

    /**
     * URL absolue pour afficher une ressource API (couverture, etc.).
     */
    protected function resolvePublicUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = trim($path);
        if (preg_match('#^https?://#i', $path)) {
            return votix_media_url($path);
        }

        if (str_starts_with($path, '//')) {
            return votix_media_url('https:'.$path);
        }

        $apiBase = rtrim((string) config('votix_api.base_url'), '/');

        // Médias Spatie / disque public du backend (toujours servis par l’API, pas le domaine du front).
        if (str_starts_with($path, 'storage/')) {
            return $apiBase.'/'.$path;
        }

        if (str_starts_with($path, '/storage/')) {
            return $apiBase.$path;
        }

        // Autres chemins absolus : site front (ex. assets).
        if (str_starts_with($path, '/')) {
            $appUrl = rtrim((string) config('app.url'), '/');

            return $appUrl !== '' ? ($appUrl.$path) : ($apiBase.$path);
        }

        return $apiBase.'/'.ltrim($path, '/');
    }

    /**
     * Liste brute de tickets / types de billets selon les formes possibles de l’API.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractTicketsFromDraft(?array $draft): array
    {
        if (! is_array($draft)) {
            return [];
        }

        $paths = [
            'data.tickets',
            'tickets',
            'data.ticket_types',
            'ticket_types',
            'event.ticket_types',
            'data.items',
        ];

        foreach ($paths as $path) {
            $normalized = $this->normalizeTicketsList(data_get($draft, $path));
            if ($normalized !== []) {
                return $normalized;
            }
        }

        $occurrences = data_get($draft, 'data.occurrences') ?? data_get($draft, 'occurrences');
        if (is_array($occurrences)) {
            foreach ($occurrences as $occ) {
                if (! is_array($occ)) {
                    continue;
                }
                foreach (['ticket_types', 'ticketTypes', 'tickets'] as $k) {
                    $normalized = $this->normalizeTicketsList($occ[$k] ?? null);
                    if ($normalized !== []) {
                        return $normalized;
                    }
                }
            }
        }

        return [];
    }

    /**
     * @param  mixed  $raw
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeTicketsList(mixed $raw): array
    {
        if (! is_array($raw) || $raw === []) {
            return [];
        }

        if (! array_is_list($raw)) {
            $raw = array_values($raw);
        }

        $out = [];
        foreach ($raw as $row) {
            if (is_array($row)) {
                $out[] = $this->normalizeTicketRow($row);
            }
        }

        return $out;
    }

    /**
     * @return array{name: string, price: string, online_quantity: int, print_quantity: int, description: string, general_conditions: string}
     */
    protected function normalizeTicketRow(array $t): array
    {
        return [
            'name'               => (string) ($t['name'] ?? $t['label'] ?? $t['title'] ?? $t['ticket_name'] ?? ''),
            'price'              => (string) ($t['price'] ?? $t['amount'] ?? $t['unit_price'] ?? '0'),
            'online_quantity'    => (int) ($t['online_quantity'] ?? $t['onlineQuantity'] ?? $t['total_quantity'] ?? $t['remaining_quantity'] ?? $t['quantity'] ?? $t['stock'] ?? 1),
            'print_quantity'     => (int) ($t['print_quantity'] ?? $t['printQuantity'] ?? $t['printed_quantity'] ?? 0),
            'description'        => (string) ($t['description'] ?? ''),
            'general_conditions' => (string) ($t['general_conditions'] ?? $t['generalConditions'] ?? $t['conditions'] ?? ''),
        ];
    }

    /**
     * Tickets au format attendu par le JS de l'étape 3.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function mapTicketsForJs(?array $draft): array
    {
        return $this->extractTicketsFromDraft($draft);
    }

    /**
     * URL brute de couverture (avant resolvePublicUrl).
     */
    protected function extractRawCoverFromDraft(?array $draft): ?string
    {
        if (! is_array($draft)) {
            return null;
        }

        $event = is_array($draft['event'] ?? null) ? $draft['event'] : [];
        if ($event === [] && is_array(data_get($draft, 'data.event'))) {
            $event = $draft['data']['event'];
        }
        $data = is_array($draft['data'] ?? null) ? $draft['data'] : [];

        $candidates = [
            data_get($draft, 'cover_url'),
            data_get($event, 'cover_url'),
            data_get($data, 'cover_url'),
            is_string(data_get($draft, 'cover')) ? data_get($draft, 'cover') : data_get($draft, 'cover.url'),
            is_string(data_get($event, 'cover')) ? data_get($event, 'cover') : data_get($event, 'cover.url'),
            data_get($draft, 'cover.path'),
            data_get($event, 'cover.path'),
            data_get($event, 'coverUrl'),
            data_get($draft, 'image_url'),
            data_get($event, 'image_url'),
            data_get($event, 'banner_url'),
            data_get($draft, 'media.0.url'),
            data_get($event, 'media.0.url'),
            data_get($draft, 'cover.full_url'),
            data_get($data, 'cover.full_url'),
        ];

        foreach ($candidates as $v) {
            if (is_string($v) && trim($v) !== '') {
                return trim($v);
            }
        }

        return null;
    }

    /**
     * Valeurs formulaire (étapes 1–3) avec chemins alternatifs selon la réponse API.
     *
     * @return array<string, mixed>
     */
    protected function draftFormPrefill(?array $draft): array
    {
        $empty = [
            'title'               => null,
            'description'         => null,
            'category_id'         => null,
            'attendance_type'     => 'in_person',
            'cover_url'           => null,
            'country_code'        => null,
            'currency'            => null,
            'city'                => null,
            'address'             => null,
            'free_event_default'  => 'false',
        ];

        if (! is_array($draft)) {
            return $empty;
        }

        $event = is_array($draft['event'] ?? null) ? $draft['event'] : [];
        if ($event === [] && is_array(data_get($draft, 'data.event'))) {
            $event = $draft['data']['event'];
        }
        $data = is_array($draft['data'] ?? null) ? $draft['data'] : [];

        $title = data_get($event, 'title')
            ?? data_get($event, 'name')
            ?? data_get($draft, 'title')
            ?? data_get($draft, 'name')
            ?? data_get($data, 'title')
            ?? data_get($data, 'event.title')
            ?? data_get($draft, 'event_name');
        if (is_string($title)) {
            $title = trim($title);
        }
        if ($title === '') {
            $title = null;
        }

        $coverUrl = $this->resolvePublicUrl($this->extractRawCoverFromDraft($draft));

        $ccRaw = $event['country_code'] ?? $data['country_code'] ?? null;
        $countryCode = ($ccRaw !== null && $ccRaw !== '') ? strtolower((string) $ccRaw) : null;

        $curRaw = $event['currency'] ?? $data['currency'] ?? null;
        $currencyCode = ($curRaw !== null && $curRaw !== '') ? strtoupper((string) $curRaw) : null;

        $freeDefault = 'false';
        $fv          = data_get($draft, 'data.free_event', data_get($draft, 'free_event'));
        if ($fv !== null && $fv !== '') {
            $freeDefault = filter_var($fv, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        } else {
            $tks = $this->extractTicketsFromDraft($draft);
            $freeDefault = (count($tks) && collect($tks)->every(fn ($t) => (float) ($t['price'] ?? 0) <= 0))
                ? 'true'
                : 'false';
        }

        return [
            'title' => $title,
            'description' => $event['description'] ?? $draft['description'] ?? null,
            'category_id' => $draft['category_id'] ?? $event['category_id'] ?? data_get($data, 'category_id'),
            'attendance_type' => $event['attendance_type'] ?? data_get($data, 'attendance_type') ?? 'in_person',
            'cover_url' => $coverUrl,
            'country_code' => $countryCode,
            'currency' => $currencyCode,
            'city' => $event['city'] ?? $data['city'] ?? data_get($data, 'location.city'),
            'address' => $event['address'] ?? $data['address'] ?? data_get($data, 'location.address'),
            'free_event_default' => $freeDefault,
        ];
    }

    public function showStep1(Request $request, string $locale)
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        // Nouveau formulaire : pas de ?draft_id= → ne pas réutiliser le brouillon en session (sinon données du dernier brouillon).
        if ($request->filled('draft_id')) {
            $draftId = (string) $request->query('draft_id');
            Session::put('event_draft.current_id', $draftId);
        } else {
            Session::forget('event_draft.current_id');
            $draftId = null;
        }

        $draft   = $draftId !== null ? $this->fetchDraft($draftId) : null;
        $prefill = $this->draftFormPrefill($draft);

        return view('dashboard.events.draft.create-step1', [
            'locale'  => $locale,
            'draft'   => $draft,
            'prefill' => $prefill,
            'draftId' => $draftId,
        ]);
    }

    public function showStep2(Request $request, string $locale)
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $draftId = $request->query('draft_id') ?: Session::get('event_draft.current_id');
        if ($draftId) {
            Session::put('event_draft.current_id', $draftId);
        }

        $draft   = $this->fetchDraft($draftId);
        $prefill = $this->draftFormPrefill($draft);

        return view('dashboard.events.draft.create-step2', [
            'locale'  => $locale,
            'draft'   => $draft,
            'prefill' => $prefill,
            'draftId' => $draftId,
        ]);
    }

    public function showStep3(Request $request, string $locale)
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $draftId = $request->query('draft_id') ?: Session::get('event_draft.current_id');
        if ($draftId) {
            Session::put('event_draft.current_id', $draftId);
        }

        $draft   = $this->fetchDraft($draftId);
        $prefill = $this->draftFormPrefill($draft);

        return view('dashboard.events.draft.create-step3', [
            'locale'           => $locale,
            'draft'            => $draft,
            'prefill'          => $prefill,
            'draftId'          => $draftId,
            'ticketsInitial'   => $this->mapTicketsForJs($draft),
        ]);
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

        Session::put('event_draft.current_id', $draftId);

        $draft = Session::get('event_draft.summary_data', []);

        if (empty($draft)) {
            $draft = $this->fetchDraft($draftId) ?? [];
        }

        $draftArray = is_array($draft) ? $draft : [];
        $prefill    = $this->draftFormPrefill($draftArray);

        return view('dashboard.events.draft.create-step4', [
            'locale'          => $locale,
            'draft'           => $draft,
            'draftId'         => $draftId,
            'coverDisplayUrl' => votix_media_url(isset($prefill['cover_url']) && is_string($prefill['cover_url']) ? $prefill['cover_url'] : null),
            'summaryTickets'  => $this->extractTicketsFromDraft($draftArray),
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
            Session::forget(['event_draft.current_id', 'event_draft.summary_data']);

            // Après publication, rediriger vers la liste "À venir"
            $redirect = route('dashboard.events.index', ['locale' => $locale, 'tab' => 'upcoming']);

            return redirect($redirect)
                ->with('success', $json['message'] ?? __('Event created successfully.'));
        }

        return $this->handleApiResponse($response);
    }

    /**
     * Supprime un brouillon (API DELETE event-drafts/{id}).
     */
    public function destroy(Request $request, string $locale, string $event): RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $response = $this->apiService->makeApiRequest(
            'DELETE',
            "event-drafts/{$event}",
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ],
            false
        );

        $pageSaved = max(1, (int) $request->input('page_saved', $request->query('page_saved', 1)));

        $indexQuery = [
            'locale'     => $locale,
            'tab'        => 'saved',
            'page_saved' => $pageSaved,
        ];
        if ($request->filled('query')) {
            $indexQuery['query'] = $request->query('query');
        }

        if ($response->successful()) {
            $json = $response->json() ?? [];
            if ($json['success'] ?? true) {
                Session::forget(['event_draft.current_id', 'event_draft.summary_data']);

                return redirect()->route('dashboard.events.index', $indexQuery)
                    ->with('success', $json['message'] ?? __('Draft deleted successfully.'));
            }
        }

        return redirect()->route('dashboard.events.index', $indexQuery)
            ->with('error', __('Draft could not be deleted.'));
    }
}

