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
    @endphp

    @push('styles')
        <style>
            .payment-method-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
            .payment-method-card { cursor: pointer; border: 1px solid #e5e5e5; border-radius: 14px; padding: 14px; background: #fff; }
            .payment-method-card input { display: none; }
            .payment-method-card--checked { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.15); }
            .payment-method-logo { font-weight: 800; font-size: 14px; line-height: 1; }
            .payment-method-sub { color: #6c757d; font-size: 12px; margin-top: 6px; }

            @media (min-width: 992px) {
                .payment-method-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            }

            /* Make cards feel less glued to edges */
            .wrapper { background: #f7f7f7; min-height: 100vh; }
            .main-card { border-radius: 16px; }
        </style>
    @endpush

    @php
        $backUrl = is_string($uiContext['return_url'] ?? null) ? $uiContext['return_url'] : null;
    @endphp

    <div class="wrapper py-4">
        <div class="checkout-body py-4">
            <div class="container px-3 px-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <a href="{{ $backUrl ?: route('ticketing.events', ['locale' => $locale]) }}"
                       class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>{{ __('Retour') }}
                    </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-7 col-lg-7 col-md-12 mb-4">
                        <div class="main-card">
                            <div class="bp-title d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h4 class="mb-0">{{ __('Order') }}</h4>
                                @if (! empty($expiredAt))
                                    <span class="small text-muted" id="intent-countdown" data-expires-at="{{ e($expiredAt) }}"></span>
                                @endif
                            </div>

                            {{-- Status + Reference removed per UX --}}

                            <hr class="my-3">

                            @if (! empty($eventTitle))
                                <p class="mb-1"><strong>{{ __('Event') }}:</strong> {{ $eventTitle }}</p>
                            @endif
                            @if (! empty($occStartFormatted))
                                <p class="mb-1"><strong>{{ __('Date') }}:</strong> {{ $occStartFormatted }}</p>
                            @endif
                            @if (! empty($userEmail))
                                <p class="mb-1"><strong>{{ __('Email') }}:</strong> {{ $userEmail }}</p>
                            @endif

                            @if (! empty($ticketsUi))
                                <div class="mt-3">
                                    <div class="fw-semibold mb-2">{{ __('Tickets') }}</div>
                                    <div class="row g-2">
                                        @foreach ($ticketsUi as $ticketTypeId => $t)
                                            @php
                                                $name = is_array($t) ? ($t['name'] ?? $ticketTypeId) : $ticketTypeId;
                                                $qty = is_array($t) ? (int) ($t['qty'] ?? 0) : 0;
                                                $unit = is_array($t) ? (float) ($t['unit_price'] ?? 0) : 0;
                                                $lineTotal = $qty > 0 ? ($qty * $unit) : 0;
                                            @endphp
                                            @if ($qty > 0)
                                                <div class="col-12 d-flex justify-content-between gap-2">
                                                    <div>{{ $qty }} × {{ $name }}</div>
                                                    <div class="fw-semibold">{{ number_format($lineTotal, 0, ',', ' ') }} {{ $displayCurrency }}</div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <hr class="my-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-semibold">{{ __('Total') }}</div>
                                        <div class="fw-bold fs-5">{{ number_format($amount, 0, ',', ' ') }} {{ $displayCurrency }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xl-5 col-lg-5 col-md-12">
                        @if (! empty($readOnly))
                            <div class="main-card">
                                <p class="mb-0">{{ __('This order is no longer pending.') }}</p>
                                <a href="{{ route('ticketing.events', ['locale' => $locale]) }}" class="main-btn btn-hover mt-3">{{ __('Back to events') }}</a>
                            </div>
                        @else
                            <div class="main-card">
                                <div class="bp-title">
                                    <h4>{{ __('Payment method') }}</h4>
                                </div>

                                <form method="post" action="{{ route('ticketing.checkout.pay', ['locale' => $locale, 'key' => $checkoutKey]) }}">
                                    @csrf

                                    <div class="payment-method-grid mb-3">
                                        @foreach ($paymentMethods as $pm)
                                            @php
                                                $code = is_array($pm) ? ($pm['code'] ?? '') : '';
                                                $name = is_array($pm) ? ($pm['name'] ?? $code) : $code;
                                                if (str_contains((string) $code, 'deposit')) { $skip = true; } else { $skip = false; }
                                                $isFree = $code === 'free';
                                                // Ne garder que Yass, Flooz et éventuellement Free (si montant nul)
                                                if (! in_array($code, ['yass', 'flooz', 'free'], true)) continue;
                                                if ($skip) continue;
                                                if ($isFree && $amount > 0.00001) continue;
                                                if (! $isFree && $amount <= 0.00001) continue;
                                                $logo = $name;
                                                if ($code === 'yass') $logo = 'Yass';
                                                if ($code === 'flooz') $logo = 'Flooz';
                                                if ($isFree) $logo = __('Free');
                                            @endphp
                                            <label class="payment-method-card" for="pm-{{ $code }}" data-method-code="{{ $code }}">
                                                <input
                                                    type="radio"
                                                    name="payment_method"
                                                    id="pm-{{ $code }}"
                                                    value="{{ $code }}"
                                                    {{ old('payment_method') === $code ? 'checked' : '' }}
                                                    required
                                                >
                                                <div class="payment-method-logo">{{ $logo }}</div>
                                                <div class="payment-method-sub">{{ $name }}</div>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="accept_terms" id="accept_terms_pay" value="1" required
                                               {{ old('accept_terms') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="accept_terms_pay">{{ __('I accept the terms of payment') }}</label>
                                    </div>

                                    <button type="submit" class="main-btn btn-hover w-100 mb-2">{{ __('Pay') }}</button>
                                </form>

                                <form method="post" action="{{ route('ticketing.checkout.cancel', ['locale' => $locale, 'key' => $checkoutKey]) }}" onsubmit="return confirm(@json(__('Cancel this order?')));">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary w-100">{{ __('Cancel order') }}</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('intent-countdown');
        if (!el || !el.dataset.expiresAt) return;
        const expires = Date.parse(el.dataset.expiresAt);
        if (Number.isNaN(expires)) return;
        function tick() {
            const now = Date.now();
            const sec = Math.max(0, Math.floor((expires - now) / 1000));
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            el.textContent = '{{ __("Time left") }}: ' + m + ':' + String(s).padStart(2, '0');
            if (sec <= 0) {
                clearInterval(t);
                el.classList.add('text-danger');
            }
        }
        tick();
        const t = setInterval(tick, 1000);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.payment-method-card');
        if (!cards || cards.length === 0) return;

        function syncCard() {
            cards.forEach(card => card.classList.remove('payment-method-card--checked'));
            const checked = document.querySelector('input[name="payment_method"]:checked');
            if (!checked) return;
            const card = checked.closest('.payment-method-card');
            if (card) card.classList.add('payment-method-card--checked');
        }

        cards.forEach(card => {
            const inp = card.querySelector('input[type="radio"][name="payment_method"]');
            if (!inp) return;
            inp.addEventListener('change', syncCard);
        });

        syncCard();
    });
</script>
@endpush
