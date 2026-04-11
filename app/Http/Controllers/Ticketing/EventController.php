<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Home : affiche une sélection d'événements à venir.
     */
    public function home(Request $request, string $locale): View
    {
        $searchQuery = $request->query('query', $request->query('q'));

        $params = [
            'statuses'    => ['upcoming'],
            'page'        => (int) $request->query('page', 1),
            // Pagination sur la home : 20 éléments par page
            'per_page'    => (int) $request->query('per_page', 16),
            'country_code'=> $request->query('country_code', 'tg'),
            'query'       => $searchQuery,
            'location'    => $request->query('location'),
        ];

        $catId = $request->query('category_id');
        if ($catId !== null && $catId !== '' && ctype_digit((string) $catId)) {
            $params['category_id'] = (int) $catId;
        }

        $events = $this->fetchPublicEvents($params, $meta);

        $this->rememberSlugMap($events);

        $paginator = new LengthAwarePaginator(
            $events,
            (int) ($meta['total'] ?? count($events)),
            (int) ($meta['per_page'] ?? $params['per_page']),
            (int) ($meta['current_page'] ?? $params['page']),
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        $paginator->appends($request->query());

        // Récupère les catégories pour les filtres de la home
        $categories = $this->apiService->getData('categories', [], true, 'items', true);

        return view('pages.home.index', [
            'locale'       => $locale,
            'events'       => $events,
            'meta'         => $meta,
            'paginator'    => $paginator,
            'categories'   => is_array($categories) ? $categories : [],
            'search_query' => $searchQuery,
        ]);
    }

    /**
     * Liste publique des événements (page Explore Events).
     */
    public function index(Request $request, string $locale): View
    {
        $searchQuery = $request->query('query', $request->query('q'));

        $params = [
            'page'        => $request->query('page', 1),
            'per_page'    => $request->query('per_page', 12),
            'query'       => $searchQuery,
            'location'    => $request->query('location'),
            'country_code'=> $request->query('country_code', 'tg'),
        ];

        // Par défaut, on montre les événements à venir
        $statuses = (array) $request->query('statuses', ['upcoming']);
        $params['statuses'] = $statuses;

        $catId = $request->query('category_id');
        if ($catId !== null && $catId !== '' && ctype_digit((string) $catId)) {
            $params['category_id'] = (int) $catId;
        }

        $events = $this->fetchPublicEvents($params, $meta);

        $this->rememberSlugMap($events);

        // Catégories réelles pour les filtres de la page liste
        $categories = $this->apiService->getData('categories', [], true, 'items', true);

        return view('pages.event.index', [
            'locale'       => $locale,
            'events'       => $events,
            'meta'         => $meta,
            'categories'   => is_array($categories) ? $categories : [],
            'search_query' => $searchQuery,
            'filters'=> [
                'query'        => $params['query'],
                'location'     => $params['location'],
                'country_code' => $params['country_code'],
                'statuses'     => $statuses,
                'category_id'  => $params['category_id'] ?? null,
            ],
        ]);
    }

    /**
     * Détail public d'un événement.
     *
     * URL : /{locale}/evenements/{slug}
     * Le backend expose le détail par ID, on résout donc d'abord le slug -> id via la liste,
     * puis on appelle GET /api/v1/events/{id}.
     */
    public function show(Request $request, string $locale, string $slug): View
    {
        $eventId = $this->resolveEventIdBySlug($slug);

        $response = $this->apiService->makeApiRequest(
            'GET',
            "events/{$eventId}",
            ['headers' => ['Accept' => 'application/json']],
            true
        );

        $event = $response->json('data') ?? [];

        return view('pages.event.show', [
            'locale' => $locale,
            'event'  => $event,
        ]);
    }

    /**
     * Compat: ancienne URL /ticketing/events/{id}
     */
    public function showLegacy(Request $request, string $locale, int $id): View
    {
        $response = $this->apiService->makeApiRequest(
            'GET',
            "events/{$id}",
            ['headers' => ['Accept' => 'application/json']],
            true
        );

        $event = $response->json('data') ?? [];

        return view('pages.event.show', [
            'locale' => $locale,
            'event'  => $event,
        ]);
    }

    protected function resolveEventIdBySlug(string $slug): int
    {
        $map = Session::get('public_events.slug_map', []);
        if (is_array($map) && isset($map[$slug])) {
            return (int) $map[$slug];
        }

        // Fallback : parcourir quelques pages sans query (le backend ne filtre pas sur slug).
        $page = 1;
        $maxPages = 10;
        $perPage = 50;
        $meta = null;

        while ($page <= $maxPages) {
            $events = $this->fetchPublicEvents([
                'page'     => $page,
                'per_page' => $perPage,
            ], $meta);

            $this->rememberSlugMap($events);

            foreach ($events as $event) {
                if (($event['slug'] ?? null) === $slug && ! empty($event['id'])) {
                    return (int) $event['id'];
                }
            }

            $lastPage = is_array($meta) ? (int) ($meta['last_page'] ?? 0) : 0;
            if ($lastPage > 0 && $page >= $lastPage) {
                break;
            }

            $page++;
        }

        // Fallback: si l'API accepte parfois le slug comme ID numérique
        if (ctype_digit($slug)) {
            return (int) $slug;
        }

        abort(404);
    }

    protected function rememberSlugMap(array $events): void
    {
        $map = Session::get('public_events.slug_map', []);
        if (! is_array($map)) {
            $map = [];
        }

        foreach ($events as $event) {
            $slug = $event['slug'] ?? null;
            $id   = $event['id'] ?? null;
            if (is_string($slug) && $slug !== '' && is_numeric($id)) {
                $map[$slug] = (int) $id;
            }
        }

        Session::put('public_events.slug_map', $map);
    }

    /**
     * Appel générique vers GET /api/v1/events.
     *
     * @param  array  $params  Query params à envoyer
     * @param  array|null  $meta  Réf. pour la pagination (remplie si fournie)
     * @return array
     */
    protected function fetchPublicEvents(array $params, ?array &$meta = null): array
    {
        $query = [];

        if (! empty($params['page'])) {
            $query['page'] = $params['page'];
        }
        if (! empty($params['per_page'])) {
            $query['per_page'] = $params['per_page'];
        }
        if (! empty($params['query'])) {
            $query['query'] = $params['query'];
        }
        if (! empty($params['location'])) {
            $query['location'] = $params['location'];
        }
        if (! empty($params['country_code'])) {
            $query['country_code'] = $params['country_code'];
        }
        if (! empty($params['statuses'])) {
            foreach ((array) $params['statuses'] as $status) {
                $query['statuses[]'][] = $status;
            }
        }
        if (! empty($params['category_id'])) {
            $query['category_id'] = (int) $params['category_id'];
        }

        $response = $this->apiService->makeApiRequest(
            'GET',
            'events',
            [
                'query'   => $query,
                'headers' => ['Accept' => 'application/json'],
            ],
            true
        );

        $json        = $response->json() ?? [];
        $events      = $json['data'] ?? [];
        $meta        = $json['meta'] ?? null;

        return is_array($events) ? $events : [];
    }
}

