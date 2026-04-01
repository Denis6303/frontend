<div class="tab-pane fade @if(($activeTab ?? request('tab')) === 'past') show active @endif" id="tickets-past-tab" role="tabpanel">
    <div class="row g-3 g-lg-4">
        @forelse($ticketPaginators['past'] ?? [] as $ticket)
            <div class="col-12 col-lg-4">
                @include('dashboard.partials.ticket-pass-card', ['ticket' => $ticket])
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted p-4 mb-0">{{ __('No past tickets.') }}</p>
            </div>
        @endforelse
    </div>
    @if(isset($ticketPaginators['past']) && $ticketPaginators['past']->hasPages())
        <div class="d-flex justify-content-center mt-4 px-2">
            {{ $ticketPaginators['past']->withQueryString()->links('pagination.dashboard-events') }}
        </div>
    @endif
</div>
