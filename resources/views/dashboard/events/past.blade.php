<div class="tab-pane fade @if(($activeTab ?? request('tab', 'upcoming')) === 'completed') show active @endif" id="past-tab" role="tabpanel">
    <div class="row g-3 g-lg-4">
        @forelse($eventsByStatus['completed'] ?? [] as $event)
            <div class="col-12 col-lg-6">
                @include('dashboard.partials.my-event-card', [
                    'event' => $event,
                    'defaultCover' => asset('images/event-imgs/img-2.jpg'),
                ])
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted p-4 mb-0">{{ __('No past events.') }}</p>
            </div>
        @endforelse
    </div>
</div>
