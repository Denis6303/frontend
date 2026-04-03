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
    $isUpcoming  = $bucket === 'upcoming';
    $cover       = ! empty($ticket['event_cover']) ? $ticket['event_cover'] : null;
    $qrPayload   = (string) ($ticket['qr_value'] ?? $ticket['id'] ?? 'votix');
    // Use a classic high-contrast QR style (closer to the expected ticket look).
    $qrUrl       = 'https://quickchart.io/qr?size=220&ecLevel=M&margin=1&text='.rawurlencode($qrPayload);

    $statusLabel = match ($bucket) {
        'past'      => __('ticket_status_past'),
        'cancelled' => __('ticket_status_cancelled'),
        default     => __('ticket_status_active'),
    };
    $statusMod = match ($bucket) {
        'past'      => 'past',
        'cancelled' => 'cancelled',
        default     => 'active',
    };
    $canTransferOrCancel = $isUpcoming;
@endphp

<div class="vtx-pass-shell h-100">
<article class="vtx-pass @if($isCancelled) vtx-pass--cancelled @endif h-100">
    <div class="vtx-pass__body">

        {{-- Recto : infos + actions --}}
        <div class="vtx-pass__recto">
            <div class="vtx-pass__top">
                <div class="vtx-pass__info">
                    <div class="vtx-pass__status-row">
                        <span class="vtx-pass__status vtx-pass__status--{{ $statusMod }}">{{ $statusLabel }}</span>
                        <div class="vtx-pass__status-right">
                            <span class="vtx-pass__category">{{ $ticket['category_label'] ?? '—' }}</span>
                            <span class="vtx-pass__price">
                                {{ number_format((float) ($ticket['price_amount'] ?? 0), 0, ',', ' ') }}
                                <small>{{ $ticket['display_currency'] ?? ($ticket['currency_code'] ?? '') }}</small>
                            </span>
                        </div>
                    </div>

                    <h3 class="vtx-pass__title">{{ $ticket['event_title'] ?? '—' }}</h3>
                    @if(!empty($ticket['event_category_label']))
                        <div class="d-inline-flex align-items-center gap-1">
                            <i class="fa-solid fa-tag vtx-pass__category" aria-hidden="true"></i>
                            <span class="vtx-pass__category">{{ $ticket['event_category_label'] }}</span>
                        </div>
                    @endif

                    <div class="vtx-pass__meta">
                        @if($startAt)
                            <div class="vtx-pass__meta-row">
                                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                <span>{{ $startAt->translatedFormat('l j F Y · H:i') }}</span>
                            </div>
                        @endif
                        @if(!empty($ticket['event_city']) || !empty($ticket['event_venue']))
                            <div class="vtx-pass__meta-row">
                                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                                <span>{{ trim(implode(' · ', array_filter([$ticket['event_venue'] ?? null, $ticket['event_city'] ?? null]))) }}</span>
                            </div>
                        @endif
                        @if(!empty($ticket['customer_email']))
                            <div class="vtx-pass__meta-row">
                                <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                                <span>{{ $ticket['customer_email'] }}</span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($ticket['order_reference']))
                        <div class="vtx-pass__order">
                            {{ __('Order') }} <code>{{ $ticket['order_reference'] }}</code>
                        </div>
                    @endif
                </div>

                @if($cover)
                    <div class="vtx-pass__cover-wrap" aria-hidden="true">
                        <img src="{{ $cover }}" alt="">
                    </div>
                @endif
            </div>

            <div class="vtx-pass__actions">
                <button type="button" class="btn vtx-pass__btn vtx-pass__btn--transfer"
                    data-ticket-transfer
                    data-ticket-id="{{ $ticket['id'] ?? '' }}"
                    data-ticket-title="{{ $ticket['event_title'] ?? '' }}"
                    @if(!$canTransferOrCancel) disabled title="{{ __('ticket_action_unavailable') }}" @endif>
                    <i class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></i>
                    {{ __('Transfer ticket') }}
                </button>
                <button type="button" class="btn vtx-pass__btn vtx-pass__btn--cancel"
                    data-ticket-cancel
                    data-ticket-id="{{ $ticket['id'] ?? '' }}"
                    data-ticket-title="{{ $ticket['event_title'] ?? '' }}"
                    @if(!$canTransferOrCancel) disabled title="{{ __('ticket_action_unavailable') }}" @endif>
                    <i class="fa-regular fa-circle-xmark" aria-hidden="true"></i>
                    {{ __('Cancel ticket') }}
                </button>
            </div>
        </div>

        {{-- Stub : QR uniquement --}}
        <div class="vtx-pass__stub">
            <div class="vtx-pass__qr">
                <img src="{{ $qrUrl }}" width="130" height="130" alt="{{ __('QR code') }}">
            </div>
        </div>

    </div>
</article>
</div>