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
        <link rel="stylesheet" href="{{ asset('css/checkout-show.css') }}">
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
                                        $isChecked = old('payment_method', 'yass') === $code;
                                        $labelText = match($code) {
                                            'yass' => __('Mixx by Yass'),
                                            'flooz' => __('Moov Money'),
                                            default => $name,
                                        };
                                    @endphp
                                    <div class="vx-pm-option">
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
                                        </label>
                                        <div class="vx-pm-label">{{ $labelText }}</div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($amount > 0.00001)
                                <div class="vx-wallet-fields" id="vx-wallet-fields" hidden>
                                    <label class="visually-hidden" for="phone_number_pay">{{ __('Phone number to debit') }}</label>
                                    <div class="vx-wallet-input-shell" id="vx-wallet-input-shell">
                                        <span class="vx-wallet-network-label" id="vx-wallet-network-label" role="presentation"></span>
                                        <input
                                            type="tel"
                                            name="phone_number"
                                            id="phone_number_pay"
                                            class="form-control vx-wallet-input"
                                            value="{{ old('phone_number') }}"
                                            autocomplete="tel"
                                            inputmode="tel"
                                            aria-describedby="vx-wallet-hint"
                                        >
                                    </div>
                                    <p class="vx-wallet-hint text-muted small mb-0 mt-1" id="vx-wallet-hint">{{ __('Enter the mobile money number that will be charged.') }}</p>
                                    <input type="hidden" name="country" id="vx-payment-country" value="{{ old('country', 'TG') }}">
                                    <input type="hidden" name="operator" id="vx-payment-operator" value="{{ old('operator', 'YASS') }}">
                                </div>
                            @endif

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
        var networkNames = {
            yass: @json(__('Mixx by Yass')),
            flooz: @json(__('Moov Money'))
        };

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

        // Payment method card selection + mobile money phone field
        function syncWalletFields() {
            const wrap = document.getElementById('vx-wallet-fields');
            if (!wrap) return;
            const checked = document.querySelector('input[name="payment_method"]:checked');
            const phone = document.getElementById('phone_number_pay');
            const op = document.getElementById('vx-payment-operator');
            const country = document.getElementById('vx-payment-country');
            if (!checked || !phone) return;
            const code = checked.value;
            const needsWallet = (code === 'yass' || code === 'flooz');
            wrap.hidden = !needsWallet;
            phone.required = needsWallet;
            if (op) {
                op.value = code === 'flooz' ? 'FLOOZ' : (code === 'yass' ? 'YASS' : (op.value || 'YASS'));
            }
            if (country && needsWallet && (!country.value || country.value.length !== 2)) {
                country.value = 'TG';
            }
            phone.placeholder = '';
            var netLbl = document.getElementById('vx-wallet-network-label');
            if (netLbl) {
                netLbl.textContent = needsWallet ? (code === 'flooz' ? networkNames.flooz : networkNames.yass) : '';
            }
        }
        function syncPaymentCardBorders() {
            var checked = document.querySelector('input[name="payment_method"]:checked');
            document.querySelectorAll('.vx-pm-card').forEach(function (c) {
                c.classList.remove('is-selected');
            });
            if (!checked) return;
            var card = checked.closest('.vx-pm-card');
            if (!card) return;
            card.classList.add('is-selected');
        }
        document.querySelectorAll('.vx-pm-card').forEach(function (card) {
            const inp = card.querySelector('input[type="radio"]');
            if (!inp) return;
            inp.addEventListener('change', function () {
                syncPaymentCardBorders();
                syncWalletFields();
            });
        });
        syncPaymentCardBorders();
        syncWalletFields();
    })();
</script>
@endpush