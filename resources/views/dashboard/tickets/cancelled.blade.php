<div class="tab-pane fade @if(($activeTab ?? request('tab')) === 'cancelled') show active @endif" id="tickets-cancelled-tab" role="tabpanel">
    <div class="row g-3 g-lg-4">
        @forelse($ticketPaginators['cancelled'] ?? [] as $ticket)
            <div class="col-12 col-lg-6">
                @include('dashboard.partials.ticket-pass-card', ['ticket' => $ticket])
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted p-4 mb-0">{{ __('No cancelled tickets.') }}</p>
            </div>
        @endforelse
    </div>
    @if(isset($ticketPaginators['cancelled']) && $ticketPaginators['cancelled']->hasPages())
        <div class="d-flex justify-content-center mt-4 px-2">
            {{ $ticketPaginators['cancelled']->withQueryString()->links('pagination.dashboard-events') }}
        </div>
    @endif
</div>
