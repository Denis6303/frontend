<div class="tab-pane fade @if(($activeTab ?? request('tab', 'upcoming')) === 'saved') show active @endif" id="draft-tab" role="tabpanel">
    <div class="row g-3 g-lg-4">
        @forelse($eventPaginators['saved'] ?? [] as $event)
            <div class="col-12 col-lg-6">
                @include('dashboard.partials.my-event-card', [
                    'event' => $event,
                    'cardContext' => 'draft',
                    'defaultCover' => asset('images/event-imgs/img-7.jpg'),
                ])
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted p-4 mb-0">{{ __('No draft events.') }}</p>
            </div>
        @endforelse
    </div>
    @if(isset($eventPaginators['saved']) && $eventPaginators['saved']->hasPages())
        <div class="d-flex justify-content-center mt-4 px-2">
            {{ $eventPaginators['saved']->withQueryString()->links('pagination.dashboard-events') }}
        </div>
    @endif
</div>
