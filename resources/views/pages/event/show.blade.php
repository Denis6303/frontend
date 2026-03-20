@extends('layouts.no-header-footer')

@section('title', ($event['title'] ?? 'Événement') . ' - Votix')

@push('styles')
<style>
    .event-toggle-link {
        color: #000 !important;
        text-decoration: none;
    }
    .event-toggle-link:hover {
        color: #000 !important;
        text-decoration: none;
    }

    /* Mobile: mettre "Select Tickets" avant "Event Details"
       et supprimer l'espace vertical entre les deux cartes */
    @media (max-width: 767.98px) {
        .main-event-dt {
            margin-bottom: 0 !important;
        }
        .event-right-dt {
            display: flex;
            flex-direction: column;
            margin-top: 0 !important;
        }
        .event-right-dt .select-tickets-block {
            order: 1;
            margin-top: 0;
        }
        .event-right-dt .bp-title,
        .event-right-dt .event-dt-right-group {
            order: 2;
        }
    }
</style>
@endpush

@section('content')
    <div class="wrapper event-details-page">

        @php
            $category    = $event['category'] ?? null;
            $occurrences = is_array($event['occurrences'] ?? null) ? $event['occurrences'] : [];
            $firstOcc    = $occurrences[0] ?? null;
            $ticketTypes = ($firstOcc && is_array($firstOcc['ticket_types'] ?? null)) ? $firstOcc['ticket_types'] : [];
            $currency    = $event['currency'] ?? '';
            $displayCurrency = $currency === 'XOF' ? 'FCFA' : $currency;
        @endphp

        <div class="event-dt-block">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="event-top-dts">
                            <div class="event-top-date">
                                @if($firstOcc && !empty($firstOcc['start_date']))
                                    <span class="event-month">{{ \Carbon\Carbon::parse($firstOcc['start_date'])->translatedFormat('M') }}</span>
                                    <span class="event-date">{{ \Carbon\Carbon::parse($firstOcc['start_date'])->translatedFormat('d') }}</span>
                                @else
                                    <span class="event-month">—</span>
                                    <span class="event-date">—</span>
                                @endif
                            </div>
                            <div class="event-top-dt">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="event-main-title mb-0">{{ $event['title'] ?? '—' }}</h3>
                                    </div>
                                    <div class="col-auto text-end">
                                        <a class="sidebar-register-link"
                                           href="{{ route('home', ['locale' => $locale ?? app()->getLocale()]) }}">
                                            <i class="fa-regular fa-circle-left me-2"></i>{{ __("Retour à l'accueil") }}
                                        </a>
                                    </div>
                                </div>
                                <div class="event-top-info-status mt-2">
                                    @if(!empty($event['city']) || !empty($event['address']))
                                        <span class="event-type-name">
                                            <i class="fa-solid fa-location-dot"></i>
                                            {{ trim(implode(', ', array_filter([$event['city'] ?? '', $event['address'] ?? '']))) }}
                                        </span>
                                    @endif
                                    @if($firstOcc && !empty($firstOcc['start_date']))
                                        <span class="event-type-name details-hr">Starts on
                                            <span class="ev-event-date">
                                                {{ \Carbon\Carbon::parse($firstOcc['start_date'])->translatedFormat('D, d M Y H:i') }}
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <div class="main-event-dt">
                            <div class="event-img">
                                <img src="{{ $event['cover_url'] ?? asset('images/logo.svg') }}" alt="Event image" style="max-height:320px;object-fit:cover;width:100%;">
                            </div>
                            <div class="main-event-content">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <h4 class="mb-0">{{ __('Description') }}</h4>
                                    {{-- Mobile only: fold/unfold --}}
                                    <a href="#eventDescriptionMobile"
                                       class="event-toggle-link d-inline-flex align-items-center fw-normal small d-md-none"
                                       data-bs-toggle="collapse"
                                       role="button"
                                       aria-expanded="false"
                                       aria-controls="eventDescriptionMobile">
                                        {{ __('Afficher') }}
                                        <i class="fa-solid fa-arrow-right ms-2"></i>
                                    </a>
                                </div>

                                {{-- Desktop always visible --}}
                                <div class="mt-3 d-none d-md-block">
                                    <p class="mb-0">{!! nl2br(e($event['description'] ?? '')) !!}</p>
                                </div>
                                {{-- Mobile: collapsed by default --}}
                                <div class="collapse mt-3 d-md-none" id="eventDescriptionMobile">
                                    <p class="mb-0">{!! nl2br(e($event['description'] ?? '')) !!}</p>
                                </div>

                                <div id="vote-root" class="mt-4"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-12">
                        {{-- Extra padding-bottom on mobile so sticky bar doesn't overlap content --}}
                        <div class="main-card event-right-dt pb-5 pb-md-0">
                            <div class="bp-title">
                                <h4>Event Details</h4>
                            </div>
                            <div class="event-dt-right-group pt-4">
                                <div class="event-dt-right-icon">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div class="event-dt-right-content">
                                    <h4>Date and Time</h4>
                                    <h5>
                                        @if($firstOcc && !empty($firstOcc['start_date']))
                                            {{ \Carbon\Carbon::parse($firstOcc['start_date'])->translatedFormat('D, d M Y H:i') }}
                                        @else
                                            —
                                        @endif
                                    </h5>
                                </div>
                            </div>
                            <div class="event-dt-right-group">
                                <div class="event-dt-right-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="event-dt-right-content">
                                    <h4>Location</h4>
                                    <h5 class="mb-0">
                                        {{ trim(implode(', ', array_filter([$event['city'] ?? '', $event['address'] ?? '']))) ?: '—' }}
                                    </h5>
                                </div>
                            </div>

                            @if($category && !empty($category['name']))
                                <div class="event-dt-right-group">
                                    <div class="event-dt-right-icon">
                                        <i class="fa-solid fa-tag"></i>
                                    </div>
                                    <div class="event-dt-right-content">
                                        <h4>{{ __('Catégorie') }}</h4>
                                        <h5 class="mb-0">{{ $category['name'] }}</h5>
                                    </div>
                                </div>
                            @endif

                            <div class="select-tickets-block">
                                <h6 class="mt-2">{{ __('Select Tickets') }}</h6>

                                @if(empty($ticketTypes))
                                    <p class="text-muted mb-0">{{ __('No tickets available.') }}</p>
                                @else
                                    @foreach($ticketTypes as $tt)
                                        @php
                                            $ttId        = $tt['id'] ?? null;
                                            $ttName      = $tt['name'] ?? '—';
                                            $ttPrice     = (float) ($tt['price'] ?? 0);
                                            $ttRemaining = (int) ($tt['remaining_quantity'] ?? 0);
                                            // ID unique pour le bloc "More details"
                                            $collapseId  = 'ticketMore' . ($ttId ?? $loop->index);
                                        @endphp
                                        <div class="border rounded-3 p-3 mb-2">
                                            <div class="fw-semibold" style="font-size: 1.05rem;">{{ $ttName }}</div>

                                            <div class="select-ticket-action mt-2">
                                                <div class="ticket-price">
                                                    {{ number_format($ttPrice, 0, ',', ' ') }} {{ $displayCurrency }}
                                                </div>
                                                <div class="quantity">
                                                    <div class="counter">
                                                        <span class="down" onClick="decreaseCount(event, this)">-</span>
                                                        <input type="text"
                                                               inputmode="numeric"
                                                               name="tickets[{{ $ttId }}][quantity]"
                                                               value="0"
                                                               data-max="{{ $ttRemaining }}"
                                                               data-ticket-id="{{ $ttId }}"
                                                               data-ticket-name="{{ $ttName }}"
                                                               data-ticket-price="{{ $ttPrice }}"
                                                               data-currency="{{ $displayCurrency }}"
                                                               class="ticket-qty-input">
                                                        <span class="up" onClick="increaseCount(event, this)">+</span>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(!empty($tt['description']) || !empty($tt['general_conditions']))
                                                <div class="mt-2">
                                                    <a href="#{{ $collapseId }}"
                                                       class="event-toggle-link d-inline-flex align-items-center fw-normal small"
                                                       data-bs-toggle="collapse"
                                                       role="button"
                                                       aria-expanded="false"
                                                       aria-controls="{{ $collapseId }}">
                                                        {{ __('More details') }}
                                                        <i class="fa-solid fa-arrow-right ms-2"></i>
                                                    </a>
                                                    <div class="collapse mt-2" id="{{ $collapseId }}">
                                                        <div class="fs-6 text-body-secondary lh-lg">
                                                            @if(!empty($tt['description']))
                                                                <div><strong>{{ __('Description') }}:</strong> {{ $tt['description'] }}</div>
                                                            @endif
                                                            @if(!empty($tt['general_conditions']))
                                                                <div><strong>{{ __("Conditions d'accès") }}:</strong> {{ $tt['general_conditions'] }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif

                                {{-- ── Order Summary ── --}}
                                <div id="order-summary" class="border rounded-3 p-3 mb-3 d-none">
                                    <h6 class="mb-2 fw-bold">{{ __('Récapitulatif') }}</h6>
                                    <ul id="summary-list" class="list-unstyled mb-2 fs-6"></ul>
                                    <div class="d-flex justify-content-between fw-semibold border-top pt-2 fs-6">
                                        <span>{{ __('Total') }}</span>
                                        <span id="summary-total"></span>
                                    </div>
                                </div>

                                {{-- Desktop button (hidden on mobile) --}}
                                <div class="booking-btn mt-4 d-none d-md-block">
                                    <a href="{{ route('ticketing.cart', ['locale' => $locale ?? app()->getLocale()]) }}"
                                       class="main-btn btn-hover w-100">
                                        {{ __('Acheter') }}
                                        <i class="fa-solid fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Sticky bottom bar – mobile only ── --}}
        <div class="d-md-none position-fixed bottom-0 start-0 end-0 bg-white border-top shadow p-3 z-3"
             id="sticky-book-bar"
             style="z-index:1050;">
            <div class="d-flex align-items-center gap-3">
                <div class="flex-grow-1">
                    <div id="sticky-summary-text" class="small text-muted">{{ __('Aucun billet sélectionné') }}</div>
                    <div id="sticky-total" class="fw-bold fs-5"></div>
                </div>
                <a href="{{ route('ticketing.cart', ['locale' => $locale ?? app()->getLocale()]) }}"
                   class="main-btn btn-hover flex-shrink-0 disabled"
                   id="sticky-book-btn"
                   aria-disabled="true">
                    {{ __('Acheter') }}
                    <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // ── Quantity helpers ──────────────────────────────────────────────
    function increaseCount(e, el) {
        e.preventDefault();
        const input = el?.previousElementSibling;
        if (!input) return;
        const max = Number(input.getAttribute('data-max'));
        let value = parseInt(input.value, 10);
        value = isNaN(value) ? 0 : value;
        value++;
        if (!Number.isNaN(max) && max >= 0) value = Math.min(value, max);
        input.value = value;
        updateSummary();
    }

    function decreaseCount(e, el) {
        e.preventDefault();
        const input = el?.nextElementSibling;
        if (!input) return;
        let value = parseInt(input.value, 10);
        value = isNaN(value) ? 0 : value;
        if (value > 0) value--;
        input.value = value;
        updateSummary();
    }

    // Sanitize manual edits
    document.addEventListener('input', function (e) {
        const input = e.target;
        if (!(input instanceof HTMLInputElement)) return;
        if (!input.closest('.select-tickets-block .counter')) return;
        const max = Number(input.getAttribute('data-max'));
        let value = parseInt(input.value, 10);
        value = isNaN(value) ? 0 : value;
        if (value < 0) value = 0;
        if (!Number.isNaN(max) && max >= 0) value = Math.min(value, max);
        input.value = String(value);
        updateSummary();
    });

    // ── Summary updater ──────────────────────────────────────────────
    function updateSummary() {
        const inputs    = document.querySelectorAll('.ticket-qty-input');
        const summaryEl = document.getElementById('order-summary');
        const listEl    = document.getElementById('summary-list');
        const totalEl   = document.getElementById('summary-total');

        // Sticky bar elements
        const stickyText  = document.getElementById('sticky-summary-text');
        const stickyTotal = document.getElementById('sticky-total');

        let grandTotal  = 0;
        let totalTickets = 0;
        let lines       = [];

        inputs.forEach(input => {
            const qty      = parseInt(input.value, 10) || 0;
            const price    = parseFloat(input.dataset.ticketPrice) || 0;
            const name     = input.dataset.ticketName || '—';
            const currency = input.dataset.currency || '';
            if (qty > 0) {
                const subtotal = qty * price;
                grandTotal += subtotal;
                totalTickets += qty;
                lines.push({ qty, name, subtotal, currency });
            }
        });

        // Get currency from first input (fallback)
        const anyCurrency = inputs.length ? (inputs[0].dataset.currency || '') : '';

        const stickyBtn = document.getElementById('sticky-book-btn');

        if (lines.length === 0) {
            summaryEl.classList.add('d-none');
            // Sticky bar: reset
            stickyText.textContent = '{{ __("Aucun billet sélectionné") }}';
            stickyTotal.textContent = '';
            if (stickyBtn) {
                stickyBtn.classList.add('disabled');
                stickyBtn.setAttribute('aria-disabled', 'true');
            }
            return;
        }

        // Build list
        summaryEl.classList.remove('d-none');
        listEl.innerHTML = lines.map(l =>
            `<li class="d-flex justify-content-between">
                <span>${l.qty} × ${escHtml(l.name)}</span>
                <span>${escHtml(l.currency)} ${formatNum(l.subtotal)}</span>
            </li>`
        ).join('');

        totalEl.textContent = `${anyCurrency} ${formatNum(grandTotal)}`;

        // Sticky bar
        stickyText.textContent = `${totalTickets} billet${totalTickets > 1 ? 's' : ''} sélectionné${totalTickets > 1 ? 's' : ''}`;
        stickyTotal.textContent = `${anyCurrency} ${formatNum(grandTotal)}`;
        if (stickyBtn) {
            stickyBtn.classList.remove('disabled');
            stickyBtn.setAttribute('aria-disabled', 'false');
        }
    }

    function formatNum(n) {
        return n.toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
</script>
@endpush