@php
    $ticket = $ticket ?? [];
    $bucket = $ticket['bucket'] ?? 'upcoming';
    $startRaw = $ticket['occurrence_start'] ?? null;
    $startAt = null;
    if (is_string($startRaw) && $startRaw !== '') {
        try {
            $startAt = \Carbon\Carbon::parse($startRaw);
        } catch (\Throwable) {
            $startAt = null;
        }
    }
    $isCancelled = $bucket === 'cancelled';
    $isUpcoming = $bucket === 'upcoming';
    $cover = ! empty($ticket['event_cover']) ? $ticket['event_cover'] : asset('images/event-imgs/img-1.jpg');
    $qrPayload = (string) ($ticket['qr_value'] ?? $ticket['id'] ?? 'votix');
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&margin=8&color=0f172a&data='.rawurlencode($qrPayload);

    $statusLabel = match ($bucket) {
        'past' => __('ticket_status_past'),
        'cancelled' => __('ticket_status_cancelled'),
        default => __('ticket_status_active'),
    };
    $statusMod = match ($bucket) {
        'past' => 'past',
        'cancelled' => 'cancelled',
        default => 'active',
    };
    $canTransferOrCancel = $isUpcoming;
@endphp

<div class="vtx-pass-shell h-100">
<article class="vtx-pass @if($isCancelled) vtx-pass--cancelled @endif position-relative h-100 d-flex flex-column">
    <div class="vtx-pass__body d-flex flex-column flex-md-row align-items-stretch flex-grow-1">
        {{-- Recto --}}
        <div class="vtx-pass__recto flex-grow-1 min-w-0 d-flex flex-column">
            <div class="vtx-pass__cover-wrap">
                <img src="{{ $cover }}" alt="">
                <span class="vtx-pass__status vtx-pass__status--{{ $statusMod }}">{{ $statusLabel }}</span>
            </div>
            <h3 class="vtx-pass__title">{{ $ticket['event_title'] ?? '—' }}</h3>
            <div class="vtx-pass__meta">
                @if($startAt)
                    <div class="vtx-pass__meta-row">
                        <i class="fa-regular fa-calendar"></i>
                        <span>{{ $startAt->translatedFormat('l j F Y · H:i') }}</span>
                    </div>
                @endif
                @if(!empty($ticket['event_city']) || !empty($ticket['event_venue']))
                    <div class="vtx-pass__meta-row">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>{{ trim(implode(' · ', array_filter([$ticket['event_venue'] ?? null, $ticket['event_city'] ?? null]))) }}</span>
                    </div>
                @endif
            </div>
            @if(!empty($ticket['order_reference']))
                <div class="vtx-pass__order">
                    {{ __('Order') }} <code>{{ $ticket['order_reference'] }}</code>
                </div>
            @endif

            <div class="vtx-pass__actions">
                <button type="button" class="btn vtx-pass__btn vtx-pass__btn--transfer" @if(!$canTransferOrCancel) disabled title="{{ __('ticket_action_unavailable') }}" @endif>
                    <i class="fa-solid fa-arrow-right-arrow-left me-1" aria-hidden="true"></i>{{ __('Transfer ticket') }}
                </button>
                <button type="button" class="btn vtx-pass__btn vtx-pass__btn--cancel" @if(!$canTransferOrCancel) disabled title="{{ __('ticket_action_unavailable') }}" @endif>
                    <i class="fa-regular fa-circle-xmark me-1" aria-hidden="true"></i>{{ __('Cancel ticket') }}
                </button>
            </div>
        </div>

        {{-- Verso (perforation = bord zigzag uniquement, via ::before en CSS) --}}
        <div class="vtx-pass__verso flex-grow-1 min-w-0">
            <div class="vtx-pass__qr">
                <img src="{{ $qrUrl }}" width="132" height="132" alt="{{ __('QR code') }}">
            </div>
            <div class="vtx-pass__category">{{ $ticket['category_label'] ?? '—' }}</div>
            <div class="vtx-pass__price">
                {{ number_format((float) ($ticket['price_amount'] ?? 0), 0, ',', ' ') }}
                <small>{{ $ticket['display_currency'] ?? ($ticket['currency_code'] ?? '') }}</small>
            </div>
        </div>
    </div>
</article>
</div>
