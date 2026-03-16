@php
    $cover   = $event['cover_url'] ?? asset('images/event-imgs/img-1.jpg');
    $occ     = $event['occurrences'][0] ?? null;
    $start   = $occ['start_date'] ?? null;
    $price   = $event['price_min'] ?? null;
    $city    = $event['city'] ?? null;
    $address = $event['address'] ?? null;
    $categoryName = $event['category']['name'] ?? null;
    $currency = $event['currency'] ?? '';
    $displayCurrency = $currency === 'XOF' ? 'FCFA' : $currency;
    $fullAddress = trim(implode(', ', array_filter([$city, $address])));
    $eventUrl = route('events.show', ['locale' => $locale ?? 'fr', 'slug' => $event['slug']]);
@endphp

<div class="main-card mt-4 position-relative">
    <div class="event-thumbnail">
        <a href="{{ $eventUrl }}" class="thumbnail-img">
            <img src="{{ $cover }}" alt="">
        </a>
    </div>
    <div class="event-content">
        <a href="{{ $eventUrl }}" class="event-title d-block">
            {{ $event['title'] ?? '—' }}
            @if($categoryName)
                <span class="text-muted small ms-1">
                    &middot;
                    <i class="fa-solid fa-tag ms-1 me-1"></i>{{ $categoryName }}
                </span>
            @endif
        </a>

        @if($fullAddress)
            <div class="mt-1">
                <span class="remaining d-block">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    {{ $fullAddress }}
                </span>
            </div>
        @endif

        <div class="duration-price-remaining mt-1">
            @if($price !== null)
                <span class="duration-price">
                    <i class="fa-solid fa-money-bill me-1"></i>
                    {{ __('À partir de') }}
                    {{ number_format((float) $price, 0, ',', ' ') }}
                    {{ $displayCurrency }}
                </span>
            @endif
        </div>
    </div>

    <a href="{{ $eventUrl }}" class="stretched-link" aria-label="{{ $event['title'] ?? 'Event' }}"></a>

    <div class="event-footer">
        <div class="event-timing">
            @if($start)
                <div class="publish-date">
                    <span>
                        <i class="fa-solid fa-calendar-day me-2"></i>
                        {{ \Carbon\Carbon::parse($start)->translatedFormat('d M') }}
                    </span>
                    <span class="dot"><i class="fa-solid fa-circle"></i></span>
                    <span>{{ \Carbon\Carbon::parse($start)->translatedFormat('D, H:i') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

