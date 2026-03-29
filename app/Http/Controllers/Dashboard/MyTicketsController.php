<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
        $raw = $this->fetchTicketsFromApi($token);
        if ($raw === []) {
            $raw = $this->placeholderTickets();
        }

        $normalized = array_map(fn (array $t) => $this->normalizeTicket($t), $raw);

        $query = mb_strtolower(trim((string) $request->query('query', '')));
        if ($query !== '') {
            $normalized = array_values(array_filter($normalized, static function (array $t) use ($query) {
                $hay = mb_strtolower(
                    ($t['event_title'] ?? '')
                    .' '.($t['order_reference'] ?? '')
                    .' '.($t['category_label'] ?? '')
                    .' '.($t['qr_value'] ?? '')
                );

                return str_contains($hay, $query);
            }));
        }

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

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchTicketsFromApi(string $token): array
    {
        $response = $this->apiService->makeApiRequest(
            'GET',
            'users/me/tickets',
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
                'query' => [
                    'per_page' => 500,
                ],
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
        $start  = $t['occurrence_start'] ?? $t['event_date'] ?? data_get($t, 'occurrence.start_date') ?? data_get($t, 'event.start_date');

        $at = null;
        if (is_string($start) && $start !== '') {
            try {
                $at = Carbon::parse($start);
            } catch (\Throwable) {
                $at = null;
            }
        }

        $bucket = 'upcoming';
        if (in_array($status, ['cancelled', 'refunded', 'void'], true)) {
            $bucket = 'cancelled';
        } elseif ($at !== null && $at->isPast()) {
            $bucket = 'past';
        }

        $event = is_array($t['event'] ?? null) ? $t['event'] : [];
        $cover = $event['cover_url'] ?? $t['cover_url'] ?? null;

        $cur = strtoupper((string) ($t['currency'] ?? $event['currency'] ?? 'XOF'));

        return array_merge($t, [
            'bucket'            => $bucket,
            'occurrence_start'  => $start,
            'event_title'       => $event['title'] ?? $t['event_title'] ?? '—',
            'event_cover'       => $cover,
            'event_city'        => $event['city'] ?? $t['city'] ?? null,
            'event_venue'       => $event['venue'] ?? $event['address'] ?? $t['venue'] ?? null,
            'category_label'    => $t['category'] ?? data_get($t, 'ticket_type.name') ?? '—',
            'price_amount'      => (float) ($t['price'] ?? data_get($t, 'ticket_type.price') ?? 0),
            'currency_code'     => $cur,
            'display_currency'  => display_currency_label($cur),
            'seat_label'        => $t['seat'] ?? $t['seat_number'] ?? null,
            'gate_label'        => $t['gate'] ?? $t['entrance'] ?? null,
            'order_reference'   => $t['order_reference'] ?? $t['order_id'] ?? data_get($t, 'order.reference'),
            'qr_value'          => (string) ($t['code'] ?? $t['ticket_code'] ?? $t['id'] ?? ''),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function placeholderTickets(): array
    {
        $img = asset('images/event-imgs/img-1.jpg');

        return [
            [
                'id'               => 'demo-1',
                'status'           => 'valid',
                'order_reference'  => 'VTX-8F2K',
                'event'            => [
                    'title'      => 'Summer Vibes Festival',
                    'cover_url'  => $img,
                    'city'       => 'Lomé',
                    'venue'      => 'Palais des congrès',
                    'currency'   => 'XOF',
                ],
                'occurrence_start' => now()->addDays(14)->setTime(20, 0)->toDateTimeString(),
                'category'         => 'VIP',
                'price'            => 35000,
                'currency'         => 'XOF',
                'seat'             => 'F12',
                'gate'             => 'Entrée B',
                'code'             => 'VTX-DEMO-8F2K-001',
            ],
            [
                'id'               => 'demo-2',
                'status'           => 'valid',
                'order_reference'  => 'VTX-9QM1',
                'event'            => [
                    'title'      => 'Jazz sous les étoiles',
                    'cover_url'  => $img,
                    'city'       => 'Lomé',
                    'venue'      => 'Hôtel 2 Février',
                    'currency'   => 'XOF',
                ],
                'occurrence_start' => now()->addDays(3)->setTime(19, 30)->toDateTimeString(),
                'category'         => 'Placement libre',
                'price'            => 10000,
                'currency'         => 'XOF',
                'seat'             => null,
                'gate'             => 'Entrée principale',
                'code'             => 'VTX-DEMO-9QM1-002',
            ],
            [
                'id'               => 'demo-3',
                'status'           => 'valid',
                'order_reference'  => 'VTX-3LP9',
                'event'            => [
                    'title'      => 'Conférence Tech 2026',
                    'cover_url'  => $img,
                    'city'       => 'Lomé',
                    'venue'      => 'Centre de conventions',
                    'currency'   => 'XOF',
                ],
                'occurrence_start' => now()->subDays(10)->setTime(9, 0)->toDateTimeString(),
                'category'         => 'Standard',
                'price'            => 25000,
                'currency'         => 'XOF',
                'seat'             => 'A-4',
                'gate'             => null,
                'code'             => 'VTX-DEMO-3LP9-003',
            ],
            [
                'id'               => 'demo-4',
                'status'           => 'cancelled',
                'order_reference'  => 'VTX-1X00',
                'event'            => [
                    'title'      => 'Soirée Gala',
                    'cover_url'  => $img,
                    'city'       => 'Lomé',
                    'venue'      => 'Centre culturel',
                    'currency'   => 'XOF',
                ],
                'occurrence_start' => now()->addMonth()->setTime(21, 0)->toDateTimeString(),
                'category'         => 'Or',
                'price'            => 50000,
                'currency'         => 'XOF',
                'seat'             => 'P02',
                'gate'             => 'Entrée VIP',
                'code'             => 'VTX-DEMO-VOID-004',
            ],
        ];
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
