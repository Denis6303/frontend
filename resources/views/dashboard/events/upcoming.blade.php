<div class="tab-pane fade @if(($activeTab ?? request('tab', 'upcoming')) === 'upcoming') show active @endif" id="upcoming-tab" role="tabpanel">
    <div class="row g-3 g-lg-4">
        @forelse($eventPaginators['upcoming'] ?? [] as $event)
            <div class="col-12 col-lg-6">
                @include('dashboard.partials.my-event-card', [
                    'event' => $event,
                    'cardContext' => 'upcoming',
                    'defaultCover' => asset('images/event-imgs/img-7.jpg'),
                ])
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted p-4 mb-0">{{ __('No upcoming events.') }}</p>
            </div>
        @endforelse
    </div>
    @if(isset($eventPaginators['upcoming']) && $eventPaginators['upcoming']->hasPages())
        <div class="d-flex justify-content-center mt-4 px-2">
            {{ $eventPaginators['upcoming']->withQueryString()->links('pagination.dashboard-events') }}
        </div>
    @endif
</div>
