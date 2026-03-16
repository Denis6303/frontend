<div class="tab-pane fade" id="past-tab" role="tabpanel">
    @forelse($eventsByStatus['completed'] ?? [] as $event)
        @php
            $cover = $event['cover_url'] ?? asset('images/event-imgs/img-2.jpg');
            $occ   = $event['occurrences'][0] ?? null;
            $start = $occ['start_date'] ?? null;
        @endphp
        <div class="main-card mt-4">
            <div class="contact-list">
                <div class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between">
                    <div class="d-md-flex align-items-center event-top-info">
                        <div class="card-event-img">
                            <img src="{{ $cover }}" alt="">
                        </div>
                        <div class="card-event-dt">
                            <h5>{{ $event['title'] ?? '—' }}</h5>
                        </div>
                    </div>
                </div>
                <div class="bottom d-flex flex-wrap justify-content-between align-items-center p-4">
                    <div class="icon-box">
                        <span class="icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                        <p>Status</p>
                        <h6 class="coupon-status">{{ ucfirst($event['status'] ?? 'completed') }}</h6>
                    </div>
                    <div class="icon-box">
                        <span class="icon">
                            <i class="fa-solid fa-calendar-days"></i>
                        </span>
                        <p>Starts on</p>
                        <h6 class="coupon-status">
                            @if($start) {{ \Carbon\Carbon::parse($start)->translatedFormat('d M Y H:i') }} @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted p-4 mb-0">{{ __('No past events.') }}</p>
    @endforelse
</div>