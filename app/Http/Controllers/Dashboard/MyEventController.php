<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyEventController extends Controller
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Liste "Mes événements" (brouillons, à venir, passés, annulés).
     */
    public function index(Request $request, string $locale): View
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $token = $this->apiService->getUserToken();

        $response = $this->apiService->makeApiRequest(
            'GET',
            'users/me/events',
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'query' => [
                    'page'     => $request->query('page', 1),
                    'per_page' => $request->query('per_page', 20),
                    'query'    => $request->query('query'),
                ],
            ],
            false
        );

        $json   = $response->json() ?? [];
        $events = is_array($json['data'] ?? null) ? $json['data'] : [];

        $grouped = [
            'saved'     => [],
            'upcoming'  => [],
            'completed' => [],
            'cancelled' => [],
        ];

        foreach ($events as $event) {
            $status = $event['status'] ?? 'saved';
            if (! isset($grouped[$status])) {
                $grouped[$status] = [];
            }
            $grouped[$status][] = $event;
        }

        $activeTab = $request->query('tab', 'upcoming');

        return view('dashboard.main.events', [
            'locale'     => $locale,
            'eventsByStatus' => $grouped,
            'activeTab'  => $activeTab,
        ]);
    }

    /**
     * Redirige vers l’édition du brouillon (même flux que la création).
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

        // TODO: appeler l’API pour publier l’événement $event
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

        // TODO: appeler l’API pour dépublier l’événement $event
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

        // TODO: appeler l’API pour annuler l’événement $event
        $tab = $request->input('tab', $request->query('tab', 'upcoming'));

        return redirect()
            ->route('dashboard.events.index', [
                'locale' => $locale,
                'tab'    => $tab,
            ])
            ->with('info', __('This feature is coming soon.'));
    }
}

