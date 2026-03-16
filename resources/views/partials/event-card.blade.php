@php
    $cover   = $event['cover_url'] ?? asset('images/event-imgs/img-1.jpg');
    $occ     = $event['occurrences'][0] ?? null;
    $start   = $occ['start_date'] ?? null;
    $price   = $event['price_min'] ?? null;
    $city    = $event['city'] ?? null;
    $address = $event['address'] ?? null;
    $categoryName = $event['category']['name'] ?? null;
    $status       = $event['status'] ?? null;
    $likes        = $event['likes_count'] ?? null;
    $views        = $event['nb_visites'] ?? null;
    $isVerified   = (bool) ($event['is_verified'] ?? false);
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
        {{-- Titre seul sur une ligne, coupé avec "..." si trop long --}}
        <a href="{{ $eventUrl }}"
           class="event-title d-block mb-0 fw-semibold text-truncate"
           title="{{ $event['title'] ?? '—' }}">
            {{ $event['title'] ?? '—' }}
        </a>

        {{-- Catégorie seule sur la 2ᵉ ligne --}}
        @if($categoryName)
            <div class="small text-muted mt-0">
                <i class="fa-solid fa-tag me-1"></i>{{ $categoryName }}
            </div>
        @endif

        @if($fullAddress)
            <div class="mt-0 small text-muted">
                <i class="fa-solid fa-location-dot me-1"></i>
                <span class="remaining d-inline">
                    {{ $fullAddress }}
                </span>
            </div>
        @endif

        <div class="duration-price-remaining mt-1">
            @if($price !== null)
                <span class="duration-price">
                    <i class="fa-solid fa-coins me-1"></i>
                    {{ __('Min.') }}
                    {{ number_format((float) $price, 0, ',', ' ') }}
                    {{ $displayCurrency }}
                </span>
            @endif
        </div>

        {{-- Ligne d'infos complémentaires : badge vérifié, likes, vues --}}
        <div class="d-flex align-items-center justify-content-between mt-2 small text-muted">
            <div class="d-flex align-items-center gap-2">
                @if($isVerified)
                    <span class="badge bg-success-subtle text-success border rounded-pill px-2 py-1">
                        <i class="fa-solid fa-check-circle me-1"></i>{{ __('Vérifié') }}
                    </span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(is_numeric($views) && $views > 0)
                    <span>
                        <i class="fa-regular fa-eye me-1"></i>{{ $views }}
                    </span>
                @endif
                @if(is_numeric($likes) && $likes > 0)
                    <span>
                        <i class="fa-regular fa-heart me-1"></i>{{ $likes }}
                    </span>
                @endif
            </div>
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

