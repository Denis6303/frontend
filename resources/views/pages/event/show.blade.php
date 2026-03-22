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

    .event-top-date--multi {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px 6px;
        line-height: 1.2;
    }
    .event-top-date--multi .event-dates-count-icon {
        color: #717171;
        font-size: 22px;
        margin-bottom: 6px;
    }
    .event-top-date--multi .event-dates-count-num {
        font-size: 26px;
        font-weight: 600;
        color: #000;
        line-height: 1;
    }
    .event-top-date--multi .event-dates-count-label {
        font-size: 10px;
        font-weight: 500;
        color: #717171;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-top: 6px;
        padding: 0 4px;
        text-align: center;
        line-height: 1.25;
    }

    .event-top-info-status {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0;
    }
    .event-top-info__location {
        padding-left: 0 !important;
    }
    .event-top-info__datetime {
        padding-left: 0 !important;
        margin-top: 0.35rem;
        border-top: 1px solid #efefef;
        padding-top: 0.5rem !important;
    }
    .event-top-info__datetime--no-separator {
        border-top: none;
        padding-top: 0 !important;
        margin-top: 0.25rem;
    }
    .event-top-info__datetime .ev-event-date {
        font-weight: 500;
        color: #212529;
    }
    .event-sessions-inline {
        flex: 1 1 100%;
        min-width: 0;
    }

    .occ-date-tabs-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        margin-bottom: 0.5rem;
        padding-bottom: 2px;
    }
    .occ-date-tabs {
        min-height: 42px;
        align-items: center;
    }
    .occ-date-tab {
        flex: 0 0 auto;
        white-space: nowrap;
        border-radius: 999px;
        padding: 0.4rem 1rem;
        font-size: 0.8125rem;
        line-height: 1.35;
        cursor: pointer;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #212529;
        font-weight: 500;
        font-family: inherit;
        transition: border-color 0.15s, background-color 0.15s, box-shadow 0.15s;
    }
    .occ-date-tab:focus-visible {
        outline: 2px solid #212529;
        outline-offset: 2px;
    }
    .occ-date-tab:hover {
        border-color: #adb5bd;
        background-color: #f8f9fa;
        color: #000;
    }
    .occ-date-tab.active {
        border-color: #212529;
        background-color: #f3f4f6;
        color: #000;
        font-weight: 600;
        box-shadow: inset 0 0 0 1px #212529;
    }
    .occ-date-tab .occ-date-tab-sub {
        font-weight: 400;
        opacity: 0.75;
    }
    .occ-date-tab.active .occ-date-tab-sub {
        opacity: 0.85;
    }
    .occ-ticket-panel .ticket-qty-input {
        min-width: 2rem;
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
            $datesCount  = public_event_dates_count($event);
            $occurrencesForDisplay = array_values(array_filter($occurrences, static function ($o) {
                return is_array($o) && ! empty($o['start_date'] ?? null);
            }));
            if ($occurrencesForDisplay === [] && ! empty($event['start_dates']) && is_array($event['start_dates'])) {
                $ends = is_array($event['end_dates'] ?? null) ? $event['end_dates'] : [];
                foreach ($event['start_dates'] as $i => $sd) {
                    if ($sd !== null && $sd !== '') {
                        $occurrencesForDisplay[] = [
                            'start_date' => $sd,
                            'end_date'   => $ends[$i] ?? null,
                        ];
                    }
                }
            }
            $firstOcc    = $occurrencesForDisplay[0] ?? ($occurrences[0] ?? null);
            $baseTicketTypes = ($firstOcc && is_array($firstOcc['ticket_types'] ?? null)) ? $firstOcc['ticket_types'] : [];
            $occurrencesForTickets = [];
            foreach ($occurrencesForDisplay as $idx => $occRow) {
                $types = $occRow['ticket_types'] ?? null;
                if (! is_array($types) || $types === []) {
                    $types = $baseTicketTypes;
                }
                $occurrencesForTickets[] = [
                    'occurrence'    => $occRow,
                    'index'         => $idx,
                    'key'           => isset($occRow['id']) ? (string) $occRow['id'] : 'occ_'.$idx,
                    'ticket_types'  => $types,
                ];
            }
            $currency    = $event['currency'] ?? '';
            $displayCurrency = $currency === 'XOF' ? 'FCFA' : $currency;
        @endphp

        <div class="event-dt-block py-3">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="event-top-dts">
                            <div class="event-top-date @if($datesCount > 1) event-top-date--multi @endif">
                                @if($datesCount <= 1 && $firstOcc && !empty($firstOcc['start_date']))
                                    <span class="event-month">{{ \Carbon\Carbon::parse($firstOcc['start_date'])->translatedFormat('M') }}</span>
                                    <span class="event-date">{{ \Carbon\Carbon::parse($firstOcc['start_date'])->translatedFormat('d') }}</span>
                                @elseif($datesCount > 1)
                                    <span class="event-dates-count-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                    <span class="event-dates-count-num">{{ $datesCount }}</span>
                                    <span class="event-dates-count-label">{{ trans_choice('event_dates_short_label', $datesCount) }}</span>
                                @else
                                    <span class="event-month">—</span>
                                    <span class="event-date">—</span>
                                @endif
                            </div>
                            <div class="event-top-dt px-2">
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
                                @php
                                    $hasEventLocation = ! empty($event['city']) || ! empty($event['address']);
                                @endphp
                                <div class="event-top-info-status mt-2">
                                    @if($hasEventLocation)
                                        <span class="event-type-name event-top-info__location w-100 d-block">
                                            <i class="fa-solid fa-location-dot"></i>
                                            {{ trim(implode(', ', array_filter([$event['city'] ?? '', $event['address'] ?? '']))) }}
                                        </span>
                                    @endif
                                    @if($datesCount >= 1 && $firstOcc && !empty($firstOcc['start_date']))
                                        @php
                                            $headerStartFormatted = \Carbon\Carbon::parse($firstOcc['start_date'])->translatedFormat('D, d M Y H:i');
                                        @endphp
                                        <span class="event-type-name event-top-info__datetime w-100 d-block @if(! $hasEventLocation) event-top-info__datetime--no-separator @endif">
                                            @if($datesCount > 1)
                                                {{ __('Starts on') }} <span class="ev-event-date">{{ $headerStartFormatted }}</span>
                                            @else
                                                <span class="ev-event-date">{{ $headerStartFormatted }}</span>
                                            @endif
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
                                <h4>{{ __('Event details') }}</h4>
                            </div>
                            <div class="event-dt-right-group pt-4">
                                <div class="event-dt-right-icon">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div class="event-dt-right-content">
                                    <h4>{{ $datesCount > 1 ? __('Dates and times') : __('Date and time') }}</h4>
                                    @if($datesCount === 0)
                                        <h5>—</h5>
                                    @elseif($datesCount === 1 && $firstOcc && !empty($firstOcc['start_date']))
                                        <h5 class="mb-0">
                                            {{ \Carbon\Carbon::parse($firstOcc['start_date'])->translatedFormat('D, d M Y H:i') }}
                                            @if(!empty($firstOcc['end_date']))
                                                <span class="text-muted fw-normal"> → {{ \Carbon\Carbon::parse($firstOcc['end_date'])->translatedFormat('H:i') }}</span>
                                            @endif
                                        </h5>
                                    @else
                                        <ul class="list-unstyled mb-0">
                                            @foreach($occurrencesForDisplay as $idx => $occ)
                                                <li class="mb-2 pb-2 @if(!$loop->last) border-bottom border-light @endif">
                                                    <span class="text-muted small d-block">{{ __('Date :number', ['number' => $idx + 1]) }}</span>
                                                    <h5 class="mb-0 fs-6 fw-semibold">
                                                        {{ \Carbon\Carbon::parse($occ['start_date'])->translatedFormat('D, d M Y H:i') }}
                                                        @if(!empty($occ['end_date']))
                                                            <span class="text-muted fw-normal"> → {{ \Carbon\Carbon::parse($occ['end_date'])->translatedFormat('H:i') }}</span>
                                                        @endif
                                                    </h5>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <div class="event-dt-right-group">
                                <div class="event-dt-right-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="event-dt-right-content">
                                    <h4>{{ __('Location') }}</h4>
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

                                @php
                                    $hasMultiOcc = count($occurrencesForTickets) > 1;
                                    $anyTickets = false;
                                    foreach ($occurrencesForTickets as $row) {
                                        if (! empty($row['ticket_types'])) {
                                            $anyTickets = true;
                                            break;
                                        }
                                    }
                                @endphp

                                @if($hasMultiOcc)
                                    <p class="small text-muted mb-2">{{ __('Select a date for tickets') }}</p>
                                    <div class="occ-date-tabs-wrapper mb-3">
                                        <div class="occ-date-tabs d-flex flex-nowrap gap-2 pb-1" role="tablist" aria-label="{{ __('Event dates') }}">
                                            @foreach($occurrencesForTickets as $ot)
                                                @php
                                                    $oi = $ot['index'];
                                                    $sd = $ot['occurrence']['start_date'] ?? null;
                                                @endphp
                                                <button type="button"
                                                        class="occ-date-tab @if($oi === 0) active @endif"
                                                        data-occ-panel="{{ $oi }}"
                                                        role="tab"
                                                        aria-selected="{{ $oi === 0 ? 'true' : 'false' }}"
                                                        id="occ-date-tab-{{ $oi }}"
                                                        aria-controls="occ-ticket-panel-{{ $oi }}">
                                                    <span>{{ __('Date :number', ['number' => $oi + 1]) }}</span>
                                                    @if($sd)
                                                        <span class="d-none d-sm-inline occ-date-tab-sub"> · {{ \Carbon\Carbon::parse($sd)->translatedFormat('d M') }}</span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(! $anyTickets)
                                    <p class="text-muted mb-0">{{ __('No tickets available.') }}</p>
                                @else
                                    @foreach($occurrencesForTickets as $ot)
                                        @php
                                            $oi = $ot['index'];
                                            $occKey = $ot['key'];
                                            $ticketTypesPanel = $ot['ticket_types'];
                                            $panelVisible = ! $hasMultiOcc || $oi === 0;
                                        @endphp
                                        <div class="occ-ticket-panel @if(! $panelVisible) d-none @endif"
                                             id="occ-ticket-panel-{{ $oi }}"
                                             role="tabpanel"
                                             aria-labelledby="occ-date-tab-{{ $oi }}"
                                             data-occurrence-key="{{ $occKey }}"
                                             @if($hasMultiOcc) data-occ-index="{{ $oi }}" @endif>
                                            @if($hasMultiOcc && !empty($ot['occurrence']['start_date']))
                                                <p class="small text-muted mb-3 mb-md-2">
                                                    <i class="fa-regular fa-calendar me-1"></i>
                                                    {{ \Carbon\Carbon::parse($ot['occurrence']['start_date'])->translatedFormat('l d M Y, H:i') }}
                                                    @if(!empty($ot['occurrence']['end_date']))
                                                        <span class="text-muted">→ {{ \Carbon\Carbon::parse($ot['occurrence']['end_date'])->translatedFormat('H:i') }}</span>
                                                    @endif
                                                </p>
                                            @endif

                                            @foreach($ticketTypesPanel as $tt)
                                                @php
                                                    $ttId        = $tt['id'] ?? null;
                                                    $ttName      = $tt['name'] ?? '—';
                                                    $ttPrice     = (float) ($tt['price'] ?? 0);
                                                    $ttRemaining = (int) ($tt['remaining_quantity'] ?? 0);
                                                    $collapseId  = 'ticketMore-'.$oi.'-'.($ttId ?? $loop->index);
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
                                                                       name="tickets[{{ $occKey }}][{{ $ttId }}][quantity]"
                                                                       value="0"
                                                                       data-max="{{ $ttRemaining }}"
                                                                       data-occurrence-key="{{ $occKey }}"
                                                                       data-date-label="{{ $hasMultiOcc ? __('Date :number', ['number' => $oi + 1]) : '' }}"
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
            const dateLbl  = (input.dataset.dateLabel || '').trim();
            if (qty > 0) {
                const subtotal = qty * price;
                grandTotal += subtotal;
                totalTickets += qty;
                lines.push({ qty, name, subtotal, currency, dateLbl });
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
        listEl.innerHTML = lines.map(l => {
            const prefix = l.dateLbl ? `<span class="text-muted small me-1">${escHtml(l.dateLbl)} — </span>` : '';
            return `<li class="d-flex justify-content-between flex-wrap gap-1">
                <span>${prefix}${l.qty} × ${escHtml(l.name)}</span>
                <span>${escHtml(l.currency)} ${formatNum(l.subtotal)}</span>
            </li>`;
        }).join('');

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

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.occ-date-tab').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var idx = this.getAttribute('data-occ-panel');
                document.querySelectorAll('.occ-date-tab').forEach(function (b) {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                document.querySelectorAll('.occ-ticket-panel').forEach(function (p) {
                    p.classList.add('d-none');
                });
                var panel = document.getElementById('occ-ticket-panel-' + idx);
                if (panel) {
                    panel.classList.remove('d-none');
                }
                updateSummary();
            });
        });
    });
</script>
@endpush