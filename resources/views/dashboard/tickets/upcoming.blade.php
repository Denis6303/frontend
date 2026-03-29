<div class="tab-pane fade @if(($activeTab ?? request('tab', 'upcoming')) === 'upcoming') show active @endif" id="tickets-upcoming-tab" role="tabpanel">
    <div class="row g-3 g-lg-4">
        @forelse($ticketPaginators['upcoming'] ?? [] as $ticket)
            <div class="col-12 col-lg-6">
                @include('dashboard.partials.ticket-pass-card', ['ticket' => $ticket])
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted p-4 mb-0">{{ __('No upcoming tickets.') }}</p>
            </div>
        @endforelse
    </div>
    @if(isset($ticketPaginators['upcoming']) && $ticketPaginators['upcoming']->hasPages())
        <div class="d-flex justify-content-center mt-4 px-2">
            {{ $ticketPaginators['upcoming']->withQueryString()->links('pagination.dashboard-events') }}
        </div>
    @endif
</div>
