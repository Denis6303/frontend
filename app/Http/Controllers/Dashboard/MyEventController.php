<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MyEventController extends Controller
{
    private const PER_PAGE = 8;

    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Liste "Mes événements" (brouillons, à venir, passés) avec pagination par onglet.
     */
    public function index(Request $request, string $locale): View|RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $token = $this->apiService->getUserToken();

        $searchQuery = trim((string) $request->query('query', ''));
        $eventsQuery = [
            'page'     => 1,
            'per_page' => 500,
        ];
        if ($searchQuery !== '') {
            $eventsQuery['query'] = $searchQuery;
        }

        $response = $this->apiService->makeApiRequest(
            'GET',
            'users/me/events',
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'query' => $eventsQuery,
            ],
            false
        );

        $json   = $response->json() ?? [];
        $events = $this->extractEventsList($json);

        $grouped = [
            'saved'     => [],
            'upcoming'  => [],
            'completed' => [],
            'cancelled' => [],
        ];

        foreach ($events as $event) {
            $status = $event['status'] ?? 'saved';
            // Les brouillons sont chargés via GET event-drafts (liste dédiée).
            if ($status === 'saved') {
                continue;
            }
            if (! isset($grouped[$status])) {
                $grouped[$status] = [];
            }
            $grouped[$status][] = $event;
        }

        $activeTab = $request->query('tab', 'upcoming');

        $pageUpcoming  = max(1, (int) $request->query('page_upcoming', 1));
        $pageCompleted = max(1, (int) $request->query('page_completed', 1));

        $categoryMap = $this->buildCategoryIdToNameMap();

        $eventPaginators = [
            'upcoming'  => $this->paginateGroup($grouped['upcoming'], self::PER_PAGE, $pageUpcoming, 'page_upcoming', $request),
            'completed' => $this->paginateGroup($grouped['completed'], self::PER_PAGE, $pageCompleted, 'page_completed', $request),
            'saved'     => $this->fetchDraftsPaginator($request, $categoryMap),
        ];

        return view('dashboard.main.events', [
            'locale'          => $locale,
            'eventPaginators' => $eventPaginators,
            'activeTab'       => $activeTab,
        ]);
    }

    /**
     * @param  array<string, mixed>  $json
     * @return array<int, array<string, mixed>>
     */
    private function extractEventsList(array $json): array
    {
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
        if ($data === [] || array_key_exists(0, $data)) {
            return $data;
        }

        return [];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function paginateGroup(array $items, int $perPage, int $page, string $pageName, Request $request): LengthAwarePaginator
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

    /**
     * id → libellé (nom) pour afficher la catégorie (GET categories).
     *
     * @return array<int, string>
     */
    private function buildCategoryIdToNameMap(): array
    {
        $items = $this->apiService->getData('categories', [], true, 'items', false);
        if (! is_array($items)) {
            return [];
        }

        $map = [];
        foreach ($items as $cat) {
            if (! is_array($cat)) {
                continue;
            }
            $id = $cat['id'] ?? null;
            if ($id === null) {
                continue;
            }
            $name = $cat['name'] ?? $cat['name_en'] ?? $cat['name_fr'] ?? null;
            if ($name !== null && $name !== '') {
                $map[(int) $id] = (string) $name;
            }
        }

        return $map;
    }

    /**
     * Adapte la réponse GET event-drafts au format attendu par la carte événement.
     *
     * @param  array<string, mixed>  $draft
     * @param  array<int, string>  $categoryMap
     * @return array<string, mixed>
     */
    private function normalizeDraftForCard(array $draft, array $categoryMap): array
    {
        $event = $draft['event'] ?? [];
        $data  = $draft['data'] ?? [];

        $categoryId = $draft['category_id'] ?? $event['category_id'] ?? null;
        $categoryName = null;
        if ($categoryId !== null && isset($categoryMap[(int) $categoryId])) {
            $categoryName = $categoryMap[(int) $categoryId];
        }

        $categoryPayload = $categoryName !== null
            ? ['name' => $categoryName, 'name_en' => $categoryName]
            : null;

        return [
            'id'           => $draft['id'] ?? null,
            'title'        => $event['title'] ?? '—',
            'city'         => $event['city'] ?? null,
            'address'      => $event['address'] ?? null,
            'cover_url'    => $draft['cover_url'] ?? null,
            'occurrences'  => $data['occurrences'] ?? [],
            'status'       => 'saved',
            'category'     => $categoryPayload,
            'categories'   => [],
            'nb_visites'   => null,
            'is_private'   => false,
            'current_step' => $draft['current_step'] ?? 1,
        ];
    }

    /**
     * Brouillons depuis l’API (pagination réelle côté serveur).
     *
     * @param  array<int, string>  $categoryMap
     */
    private function fetchDraftsPaginator(Request $request, array $categoryMap): LengthAwarePaginator
    {
        $page = max(1, (int) $request->query('page_saved', 1));

        $query = array_filter([
            'page'     => $page,
            'per_page' => self::PER_PAGE,
            'query'    => $request->query('query'),
        ], static fn ($v) => $v !== null && $v !== '');

        $response = $this->apiService->makeApiRequest(
            'GET',
            'event-drafts',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'query' => $query,
            ],
            false
        );

        $json    = $response->json() ?? [];
        $payload = $json['data'] ?? [];

        $items        = [];
        $total        = 0;
        $perPage      = self::PER_PAGE;
        $currentPage  = $page;

        if ($response->successful() && ($json['success'] ?? true) && is_array($payload)) {
            $rawItems = $payload['data'] ?? [];
            $items    = is_array($rawItems) ? array_values(array_filter($rawItems, 'is_array')) : [];
            $total    = (int) ($payload['total'] ?? count($items));
            $perPage  = (int) ($payload['per_page'] ?? self::PER_PAGE);
            if ($perPage < 1) {
                $perPage = self::PER_PAGE;
            }
            $currentPage = (int) ($payload['current_page'] ?? $page);
        }

        $mapped = array_map(fn (array $d) => $this->normalizeDraftForCard($d, $categoryMap), $items);

        return (new LengthAwarePaginator(
            $mapped,
            $total,
            $perPage,
            $currentPage,
            [
                'path'     => $request->url(),
                'pageName' => 'page_saved',
            ]
        ))->withQueryString();
    }

    /**
     * Reprendre un brouillon à l'étape enregistrée côté API.
     */
    public function resumeDraft(Request $request, string $locale, string $event): RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $response = $this->apiService->makeApiRequest(
            'GET',
            "event-drafts/{$event}",
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ],
            false
        );

        $step = 1;
        if ($response->successful()) {
            $json = $response->json() ?? [];
            $data = $json['data'] ?? [];
            if (is_array($data)) {
                $step = (int) ($data['current_step'] ?? $data['draft_step'] ?? $data['step'] ?? $data['resume_step'] ?? 1);
                if ($step < 1) {
                    $step = 1;
                }
                if ($step > 4) {
                    $step = 4;
                }
            }
        }

        $routes = [
            1 => 'dashboard.events.draft.create.step1',
            2 => 'dashboard.events.draft.create.step2',
            3 => 'dashboard.events.draft.create.step3',
            4 => 'dashboard.events.draft.create.step4',
        ];

        $routeName = $routes[$step] ?? 'dashboard.events.draft.create.step1';

        return redirect()->route($routeName, [
            'locale'   => $locale,
            'draft_id' => $event,
            'tab'      => $request->query('tab'),
        ]);
    }

    /**
     * Redirige vers l’édition du brouillon (étape 1).
     */
    public function edit(Request $request, string $locale, string $event): RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        return redirect()->route('dashboard.events.draft.create.step1', [
            'locale'   => $locale,
            'draft_id' => $event,
            'tab'      => $request->query('tab'),
        ]);
    }

    /**
     * Page recettes (à brancher sur l’API / reporting).
     */
    public function revenues(Request $request, string $locale, string $event): RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        return redirect()
            ->route('dashboard.events.index', [
                'locale' => $locale,
                'tab'    => $request->query('tab', $request->input('tab', 'upcoming')),
            ])
            ->with('info', __('This feature is coming soon.'));
    }

    public function publish(Request $request, string $locale, string $event): RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $tab = $request->input('tab', $request->query('tab', 'upcoming'));

        return redirect()
            ->route('dashboard.events.index', [
                'locale' => $locale,
                'tab'    => $tab,
            ])
            ->with('info', __('This feature is coming soon.'));
    }

    public function unpublish(Request $request, string $locale, string $event): RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $tab = $request->input('tab', $request->query('tab', 'upcoming'));

        return redirect()
            ->route('dashboard.events.index', [
                'locale' => $locale,
                'tab'    => $tab,
            ])
            ->with('info', __('This feature is coming soon.'));
    }

    public function cancel(Request $request, string $locale, string $event): RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $tab = $request->input('tab', $request->query('tab', 'upcoming'));

        return redirect()
            ->route('dashboard.events.index', [
                'locale' => $locale,
                'tab'    => $tab,
            ])
            ->with('info', __('This feature is coming soon.'));
    }
}
