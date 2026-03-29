@extends('layouts.no-header-footer')

@section('title', __('Payment') . ' — Votix')

@section('content')
    @php
        $locale = $locale ?? app()->getLocale();
        $amount = isset($intent['amount']) ? (float) $intent['amount'] : 0;
        $currency = $intent['currency'] ?? 'XOF';
        $displayCurrency = $currency === 'XOF' ? 'FCFA' : $currency;
        $expiredAt = $intent['expired_at'] ?? null;
        $status = $intent['status'] ?? '';

        $uiContext = is_array($uiContext ?? null) ? $uiContext : [];
        $userEmail = is_string($userEmail ?? null) ? $userEmail : null;
        $ticketsUi = is_array($uiContext['tickets'] ?? null) ? $uiContext['tickets'] : [];
        if (empty($ticketsUi) && is_array($intent['tickets'] ?? null)) {
            $ticketsUi = $intent['tickets'];
        }
        $eventTitle = is_string($uiContext['event_title'] ?? null) ? $uiContext['event_title'] : ($intent['event']['title'] ?? ($intent['event_title'] ?? null));
        $occStartDate = is_string($uiContext['occurrence_start_date'] ?? null) ? $uiContext['occurrence_start_date'] : ($intent['event_occurrence']['start_date'] ?? ($intent['occurrence']['start_date'] ?? ($intent['start_date'] ?? null)));

        $occStartFormatted = null;
        if (! empty($occStartDate)) {
            $occStartFormatted = \Carbon\Carbon::parse($occStartDate)->translatedFormat('l d M Y, H:i');
        }

        $backUrl = is_string($uiContext['return_url'] ?? null) ? $uiContext['return_url'] : null;
    @endphp

    @push('styles')
        <style>
            .vx-page {
                padding: 4rem 1rem 2rem 1rem;
            }
            .vx-container {
                max-width: 900px;
                margin: 0 auto;
            }
            /* Back button */
            .vx-back {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-size: 14px;
                font-weight: 500;
                color: #000;
                text-decoration: none;
                padding: 8px 18px;
                border-radius: 8px;
                cursor: pointer;
            }
            .vx-back:hover {
                color: #000 !important;
            } 
            .vx-back svg { flex-shrink: 0; }

            /* Error alert */
            .vx-alert {
                border: 1px solid #000;
                border-radius: 10px;
                padding: 1rem 1.25rem;
                margin-top: 1.25rem;
                font-size: 14px;
                background: #fff;
            }
            .vx-alert ul { padding-left: 1.25rem; }

            /* Grid */
            .vx-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
                margin-top: 2rem;
            }
            @media (max-width: 640px) {
                .vx-grid { grid-template-columns: 1fr; }
            }

            /* Card */
            .vx-card {
                border: 1px solid #000;
                border-radius: 14px;
                background: #fff;
                padding: 1.5rem;
            }

            .vx-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.25rem;
            }

            .vx-card-title {
                font-size: 16px;
                font-weight: 500;
                color: #000;
            }

            /* Countdown badge */
            .vx-countdown {
                font-size: 12px;
                color: #555;
                background: #f5f5f5;
                border-radius: 20px;
                padding: 4px 12px;
                white-space: nowrap;
                transition: color .3s;
            }
            .vx-countdown.is-urgent { color: #c00; }

            /* Section label */
            .vx-section-label {
                font-size: 11px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: .08em;
                color: #888;
                margin-bottom: .75rem;
            }

            /* Meta rows */
            .vx-meta-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }
            .vx-meta-row:last-of-type { border-bottom: none; }
            .vx-meta-label { font-size: 13px; color: #666; }
            .vx-meta-value { font-size: 13px; font-weight: 500; color: #000; }

            /* Divider */
            .vx-divider {
                border: none;
                border-top: 1px solid #000;
                margin: 1.25rem 0;
            }

            /* Ticket rows */
            .vx-ticket-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 6px 0;
            }
            .vx-ticket-name { font-size: 14px; color: #333; }
            .vx-ticket-price { font-size: 14px; font-weight: 500; color: #000; }

            /* Total */
            .vx-total-row {
                display: flex;
                justify-content: space-between;
                align-items: baseline;
            }
            .vx-total-label { font-size: 14px; font-weight: 500; }
            .vx-total-amount { font-size: 22px; font-weight: 500; letter-spacing: -.02em; }

            /* Payment method grid */
            .vx-pm-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 1.25rem;
            }

            .vx-pm-card {
                border: 1px solid #ccc;
                border-radius: 12px;
                padding: 14px;
                cursor: pointer;
                transition: border-color .15s, box-shadow .15s;
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .vx-pm-card--image {
                padding: 0;
                min-height: 86px;
                overflow: hidden;
                gap: 0;
            }
            .vx-pm-card:hover { border-color: #888; }
            .vx-pm-card input[type="radio"] { display: none; }
            .vx-pm-card.is-selected {
                border-color: #000;
                box-shadow: 0 0 0 2px #000;
            }
            .vx-pm-logo { font-size: 15px; font-weight: 500; color: #000; }
            .vx-pm-logo-img {
                display: block;
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
            }
            .vx-pm-sub { font-size: 11px; color: #888; }

            /* Terms */
            .vx-terms {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                margin-bottom: 1.25rem;
            }
            .vx-terms input[type="checkbox"] {
                width: 16px;
                height: 16px;
                accent-color: #000;
                flex-shrink: 0;
                margin-top: 3px;
                cursor: pointer;
            }
            .vx-terms label {
                font-size: 13px;
                color: #444;
                line-height: 1.5;
                cursor: pointer;
            }

            /* Buttons */
            .vx-btn-pay {
                display: block;
                width: 100%;
                padding: 13px;
                background: #000;
                color: #fff;
                border: none;
                border-radius: 10px;
                font-size: 15px;
                font-weight: 500;
                cursor: pointer;
                text-align: center;
                transition: opacity .15s;
                margin-bottom: 10px;
            }
            .vx-btn-pay:hover { opacity: .8; }
            .vx-btn-pay:active { transform: scale(.98); }

            .vx-btn-cancel {
                display: block;
                width: 100%;
                padding: 11px;
                background: #fff;
                color: #000;
                border: 1px solid #ccc;
                border-radius: 10px;
                font-size: 13px;
                cursor: pointer;
                text-align: center;
                transition: border-color .15s, background .15s;
            }
            .vx-btn-cancel:hover { border-color: #000; background: #fafafa; }

            /* Read-only state */
            .vx-readonly-msg { font-size: 14px; color: #444; margin-bottom: 1rem; }
            .vx-btn-events {
                display: inline-block;
                padding: 10px 20px;
                background: #000;
                color: #fff;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                transition: opacity .15s;
            }
            .vx-btn-events:hover { opacity: .8; color: #fff; }
        </style>
    @endpush

    <div class="vx-page">
        <div class="vx-container">

            {{-- Back button --}}
            <a href="{{ $backUrl ?: route('ticketing.events', ['locale' => $locale]) }}" class="vx-back">
                ← {{ __('Retour') }}
            </a>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="vx-alert">
                    <ul>
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="vx-grid">

                {{-- Left: Order summary --}}
                <div class="vx-card">
                    <div class="vx-card-header">
                        <span class="vx-card-title">{{ __('Order') }}</span>
                        @if (! empty($expiredAt))
                            <span class="vx-countdown" id="intent-countdown" data-expires-at="{{ e($expiredAt) }}"></span>
                        @endif
                    </div>

                    @if (! empty($eventTitle) || ! empty($occStartFormatted) || ! empty($userEmail))
                        <div class="vx-section-label">{{ __('Details') }}</div>
                        @if (! empty($eventTitle))
                            <div class="vx-meta-row">
                                <span class="vx-meta-label">{{ __('Event') }}</span>
                                <span class="vx-meta-value">{{ $eventTitle }}</span>
                            </div>
                        @endif
                        @if (! empty($occStartFormatted))
                            <div class="vx-meta-row">
                                <span class="vx-meta-label">{{ __('Date') }}</span>
                                <span class="vx-meta-value">{{ $occStartFormatted }}</span>
                            </div>
                        @endif
                        @if (! empty($userEmail))
                            <div class="vx-meta-row">
                                <span class="vx-meta-label">{{ __('Email') }}</span>
                                <span class="vx-meta-value">{{ $userEmail }}</span>
                            </div>
                        @endif
                    @endif

                    @if (! empty($ticketsUi))
                        <hr class="vx-divider">

                        <div class="vx-section-label">{{ __('Tickets') }}</div>

                        @foreach ($ticketsUi as $ticketTypeId => $t)
                            @php
                                $name = is_array($t) ? ($t['name'] ?? $ticketTypeId) : $ticketTypeId;
                                $qty = is_array($t) ? (int) ($t['qty'] ?? 0) : 0;
                                $unit = is_array($t) ? (float) ($t['unit_price'] ?? 0) : 0;
                                $lineTotal = $qty > 0 ? ($qty * $unit) : 0;
                            @endphp
                            @if ($qty > 0)
                                <div class="vx-ticket-row">
                                    <span class="vx-ticket-name">{{ $qty }} × {{ $name }}</span>
                                    <span class="vx-ticket-price">{{ number_format($lineTotal, 0, ',', ' ') }} {{ $displayCurrency }}</span>
                                </div>
                            @endif
                        @endforeach

                        <hr class="vx-divider">

                        <div class="vx-total-row">
                            <span class="vx-total-label">{{ __('Total') }}</span>
                            <span class="vx-total-amount">{{ number_format($amount, 0, ',', ' ') }} {{ $displayCurrency }}</span>
                        </div>
                    @endif
                </div>

                {{-- Right: Payment --}}
                <div class="vx-card">
                    @if (! empty($readOnly))
                        <div class="vx-card-title" style="margin-bottom:1rem;">{{ __('Payment method') }}</div>
                        <p class="vx-readonly-msg">{{ __('This order is no longer pending.') }}</p>
                        <a href="{{ route('ticketing.events', ['locale' => $locale]) }}" class="vx-btn-events">{{ __('Back to events') }}</a>
                    @else
                        <div class="vx-card-title" style="margin-bottom:1.25rem;">{{ __('Payment method') }}</div>

                        <form method="post" action="{{ route('ticketing.checkout.pay', ['locale' => $locale, 'key' => $checkoutKey]) }}">
                            @csrf

                            <div class="vx-pm-grid">
                                @foreach ($paymentMethods as $pm)
                                    @php
                                        $code = is_array($pm) ? ($pm['code'] ?? '') : '';
                                        $name = is_array($pm) ? ($pm['name'] ?? $code) : $code;
                                        if (str_contains((string) $code, 'deposit')) continue;
                                        if (! in_array($code, ['yass', 'flooz', 'free'], true)) continue;
                                        $isFree = $code === 'free';
                                        if ($isFree && $amount > 0.00001) continue;
                                        if (! $isFree && $amount <= 0.00001) continue;
                                        $logo = match($code) {
                                            'yass'  => 'Yass',
                                            'flooz' => 'Flooz',
                                            default => __('Free'),
                                        };
                                        $logoImage = match($code) {
                                            'yass' => asset('images/payment_methods/mixx.jpg'),
                                            'flooz' => asset('images/payment_methods/moov_money.png'),
                                            default => null,
                                        };
                                        $isChecked = old('payment_method') === $code;
                                    @endphp
                                    <label
                                        class="vx-pm-card{{ $logoImage ? ' vx-pm-card--image' : '' }}{{ $isChecked ? ' is-selected' : '' }}"
                                        for="pm-{{ $code }}"
                                    >
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            id="pm-{{ $code }}"
                                            value="{{ $code }}"
                                            {{ $isChecked ? 'checked' : '' }}
                                            required
                                        >
                                        @if ($logoImage)
                                            <img src="{{ $logoImage }}" alt="{{ $logo }}" class="vx-pm-logo-img">
                                        @else
                                            <span class="vx-pm-logo">{{ $logo }}</span>
                                        @endif
                                        @if (! $logoImage)
                                            <span class="vx-pm-sub">{{ $name }}</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>

                            <div class="vx-terms">
                                <input
                                    type="checkbox"
                                    name="accept_terms"
                                    id="accept_terms_pay"
                                    value="1"
                                    required
                                    {{ old('accept_terms') ? 'checked' : '' }}
                                >
                                <label for="accept_terms_pay">{{ __('I accept the terms of payment') }}</label>
                            </div>

                            <button type="submit" class="vx-btn-pay">{{ __('Pay') }}</button>
                        </form>

                        <form
                            method="post"
                            action="{{ route('ticketing.checkout.cancel', ['locale' => $locale, 'key' => $checkoutKey]) }}"
                            onsubmit="return confirm(@json(__('Cancel this order?')))"
                        >
                            @csrf
                            <button type="submit" class="vx-btn-cancel">{{ __('Cancel order') }}</button>
                        </form>
                    @endif
                </div>

            </div>{{-- /.vx-grid --}}
        </div>{{-- /.vx-container --}}
    </div>{{-- /.vx-page --}}

@endsection

@push('scripts')
<script>
    (function () {
        // Countdown timer
        const el = document.getElementById('intent-countdown');
        if (el && el.dataset.expiresAt) {
            const expires = Date.parse(el.dataset.expiresAt);
            if (!Number.isNaN(expires)) {
                function tick() {
                    const sec = Math.max(0, Math.floor((expires - Date.now()) / 1000));
                    const m = Math.floor(sec / 60);
                    const s = sec % 60;
                    el.textContent = '{{ __("Time left") }}: ' + m + ':' + String(s).padStart(2, '0');
                    el.classList.toggle('is-urgent', sec <= 60);
                    if (sec > 0) setTimeout(tick, 1000);
                }
                tick();
            }
        }

        // Payment method card selection
        document.querySelectorAll('.vx-pm-card').forEach(function (card) {
            const inp = card.querySelector('input[type="radio"]');
            if (!inp) return;
            inp.addEventListener('change', function () {
                document.querySelectorAll('.vx-pm-card').forEach(function (c) {
                    c.classList.remove('is-selected');
                });
                card.classList.add('is-selected');
            });
        });
    })();
</script>
@endpush