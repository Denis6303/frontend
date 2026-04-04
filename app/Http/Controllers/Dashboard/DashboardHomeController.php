<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardHomeController extends Controller
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    public function index(Request $request, string $locale): View|RedirectResponse
    {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $token = $this->apiService->getUserToken();
        if (! $token) {
            return redirect()->route('login', ['locale' => $locale]);
        }

        $now = Carbon::now();

        $startDate = $request->query('start_date', $now->copy()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', $now->copy()->endOfMonth()->format('Y-m-d'));
        $granularity = $request->query('granularity', 'weekly');
        $chartMetric = $request->query('chart_metric', 'revenue');

        if (! in_array($granularity, ['daily', 'weekly', 'monthly'], true)) {
            $granularity = 'weekly';
        }
        if (! in_array($chartMetric, ['revenue', 'orders', 'page_views', 'ticket_sales'], true)) {
            $chartMetric = 'revenue';
        }

        $selectedEventIds = array_values(array_filter(array_map('intval', (array) $request->query('event_ids', []))));

        $events = $this->fetchOrganizerEvents($token, $request);

        $query = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'granularity' => $granularity,
            'chart_metric' => $chartMetric,
            'locale' => $locale,
        ];
        if ($selectedEventIds !== []) {
            $query['event_ids'] = $selectedEventIds;
        }

        $response = $this->apiService->makeApiRequest(
            'GET',
            'users/me/events/dashboard-stats',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
                'query' => $query,
            ],
            false
        );

        $stats = null;
        $apiError = null;
        if ($response->successful()) {
            $json = $response->json() ?? [];
            if (isset($json['data']) && is_array($json['data'])) {
                $stats = $json['data'];
            } else {
                $apiError = is_array($json) ? ($json['message'] ?? __('Unable to load dashboard statistics.')) : __('Unable to load dashboard statistics.');
            }
        } else {
            $apiError = $response->json('message') ?? __('Unable to load dashboard statistics.');
        }

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();
        $periodDays = max(1, $startCarbon->diffInDays($endCarbon->copy()->startOfDay()) + 1);

        $prevEnd = $startCarbon->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($periodDays - 1);

        $nextStart = $endCarbon->copy()->addDay()->startOfDay();
        $nextEnd = $nextStart->copy()->addDays($periodDays - 1)->endOfDay();
        $today = Carbon::today();
        $canShiftNext = ! $nextStart->isAfter($today);

        $commonQuery = [
            'granularity' => $granularity,
            'chart_metric' => $chartMetric,
        ];
        if ($selectedEventIds !== []) {
            $commonQuery['event_ids'] = $selectedEventIds;
        }

        return view('dashboard.main.index', [
            'locale' => $locale,
            'stats' => $stats,
            'events' => $events,
            'selectedEventIds' => $selectedEventIds,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'granularity' => $granularity,
            'chartMetric' => $chartMetric,
            'apiError' => $apiError,
            'periodNav' => [
                'prev' => array_merge($commonQuery, [
                    'start_date' => $prevStart->format('Y-m-d'),
                    'end_date' => $prevEnd->format('Y-m-d'),
                ]),
                'next' => array_merge($commonQuery, [
                    'start_date' => $nextStart->format('Y-m-d'),
                    'end_date' => $nextEnd->format('Y-m-d'),
                ]),
                'can_shift_next' => $canShiftNext,
            ],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchOrganizerEvents(string $token, Request $request): array
    {
        $response = $this->apiService->makeApiRequest(
            'GET',
            'users/me/events',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
                'query' => [
                    'page' => 1,
                    'per_page' => 500,
                    'query' => $request->query('events_q'),
                ],
            ],
            false
        );

        if (! $response->successful()) {
            return [];
        }

        $json = $response->json() ?? [];

        return $this->extractEventsList($json);
    }

    /**
     * @return list<array<string, mixed>>
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
}
