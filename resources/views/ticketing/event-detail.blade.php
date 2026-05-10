@extends('layouts.app')

@section('title', ($event['title'] ?? 'Événement') . ' - Votix')

@section('content')
    <div class="wrapper">
        <div class="breadcrumb-block">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-10">
                        <div class="barren-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('ticketing.index', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('Home') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('ticketing.events', ['locale' => $locale ?? app()->getLocale()]) }}">Explore
                                            Events</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $event['title'] ?? 'Event' }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $category    = $event['category'] ?? null;
            $categoryLabel = null;
            if (is_array($category)) {
                $categoryLabel = ($locale ?? app()->getLocale()) === 'en'
                    ? ($category['name_en'] ?? $category['name'] ?? null)
                    : ($category['name'] ?? $category['name_en'] ?? null);
            }
            $occurrences = is_array($event['occurrences'] ?? null) ? $event['occurrences'] : [];
            $firstOcc    = $occurrences[0] ?? null;
            $ticketTypes = ($firstOcc && is_array($firstOcc['ticket_types'] ?? null)) ? $firstOcc['ticket_types'] : [];
            $currency    = $event['currency'] ?? '';
        @endphp

        <div class="event-dt-block p-80">
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
                                <h3 class="event-main-title">
                                    {{ $event['title'] ?? '—' }}
                                </h3>
                                <div class="event-top-info-status">
                                    @if(!empty($categoryLabel))
                                        <span class="event-type-name">
                                            <i class="fa-solid fa-tag"></i> {{ $categoryLabel }}
                                        </span>
                                    @endif
                                    @if(!empty($event['city']) || !empty($event['address']))
                                        <span class="event-type-name"><i class="fa-solid fa-location-dot"></i>
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
                                <img src="{{ votix_media_url($event['cover_url'] ?? null) ?? asset('images/logos/bottomless.png') }}" alt="Event image" style="max-height:320px;object-fit:cover;width:100%;">
                            </div>
                            <div class="main-event-content">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <h4 class="mb-0">{{ __('Description') }}</h4>
                                    <button class="btn btn-sm btn-outline-dark"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#eventDescription"
                                            aria-expanded="false"
                                            aria-controls="eventDescription">
                                        {{ __('Show') }}
                                    </button>
                                </div>
                                <div class="collapse mt-3" id="eventDescription">
                                    <p class="mb-0">{!! nl2br(e($event['description'] ?? '')) !!}</p>
                                </div>

                                {{-- Placeholder pour le futur module de vote --}}
                                <div id="vote-root" class="mt-4">
                                    {{-- Le module de vote sera monté ici (JS / template dédié) --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <div class="main-card event-right-dt">
                            <div class="bp-title">
                                <h4>{{ __('Event details') }}</h4>
                            </div>
                            <div class="event-dt-right-group mt-4">
                                <div class="event-dt-right-icon">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div class="event-dt-right-content">
                                    <h4>{{ __('Date and time') }}</h4>
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
                                <h4>{{ __('Location') }}</h4>
                                <h5 class="mb-0">
                                    {{ trim(implode(', ', array_filter([$event['city'] ?? '', $event['address'] ?? '']))) ?: '—' }}
                                </h5>
                                </div>
                            </div>
                            <div class="select-tickets-block" id="ticketPicker"
                                 data-currency="{{ e($currency) }}">
                                <h6 class="mb-3">{{ __('Select Tickets') }}</h6>

                                @if(empty($ticketTypes))
                                    <p class="text-muted mb-0">{{ __('No tickets available.') }}</p>
                                @else
                                    @foreach($ticketTypes as $tt)
                                        @php
                                            $ttId = $tt['id'] ?? null;
                                            $ttName = $tt['name'] ?? '—';
                                            $ttPrice = (float) ($tt['price'] ?? 0);
                                            $ttRemaining = (int) ($tt['remaining_quantity'] ?? 0);
                                        @endphp
                                        <div class="border rounded-3 p-3 mb-2"
                                             data-ticket
                                             data-ticket-id="{{ $ttId }}"
                                             data-ticket-name="{{ e($ttName) }}"
                                             data-ticket-price="{{ $ttPrice }}"
                                             data-ticket-remaining="{{ $ttRemaining }}">
                                            <div class="d-flex align-items-start justify-content-between gap-3">
                                                <div>
                                                    <div class="fw-semibold">{{ $ttName }}</div>
                                                    <div class="text-muted small">
                                                        {{ $currency }} {{ number_format($ttPrice, 0, ',', ' ') }}
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-dark" data-qty-down>-</button>
                                                    <input type="number" class="form-control form-control-sm text-center"
                                                           style="width:70px"
                                                           min="0"
                                                           max="{{ $ttRemaining }}"
                                                           value="0"
                                                           data-qty-input>
                                                    <button type="button" class="btn btn-sm btn-outline-dark" data-qty-up>+</button>
                                                </div>
                                            </div>

                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-link p-0"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#ticketMore{{ $ttId }}"
                                                        aria-expanded="false"
                                                        aria-controls="ticketMore{{ $ttId }}">
                                                    {{ __('More details') }}
                                                </button>
                                                <div class="collapse mt-2" id="ticketMore{{ $ttId }}">
                                                    <div class="text-muted small">
                                                        @if(!empty($tt['description']))
                                                            <div><strong>{{ __('Description') }}:</strong> {{ $tt['description'] }}</div>
                                                        @endif
                                                        @if(!empty($tt['general_conditions']))
                                                            <div><strong>{{ __('Conditions') }}:</strong> {{ $tt['general_conditions'] }}</div>
                                                        @endif
                                                        @if(isset($tt['remaining_quantity']))
                                                            <div><strong>{{ __('Remaining') }}:</strong> {{ $tt['remaining_quantity'] }}</div>
                                                        @endif
                                                        @if(isset($tt['total_quantity']))
                                                            <div><strong>{{ __('Total') }}:</strong> {{ $tt['total_quantity'] }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="mt-3 border-top pt-3">
                                        <div class="fw-semibold mb-2">{{ __('Selection summary') }}</div>
                                        <div id="ticketSummaryLines" class="small text-muted"></div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <div class="fw-semibold">{{ __('Total') }}</div>
                                            <div class="fw-semibold" id="ticketSummaryTotal">{{ $currency }} 0</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="booking-btn mt-4">
                                    <a href="{{ route('ticketing.cart', ['locale' => $locale ?? app()->getLocale()]) }}"
                                        class="main-btn btn-hover w-100">{{ __('Book now') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(() => {
  const root = document.getElementById('ticketPicker');
  if (!root) return;

  const currency = root.dataset.currency || '';
  const linesEl = document.getElementById('ticketSummaryLines');
  const totalEl = document.getElementById('ticketSummaryTotal');

  const fmt = (n) => {
    try { return new Intl.NumberFormat(undefined).format(n); } catch (e) { return String(n); }
  };

  function recompute() {
    const tickets = Array.from(root.querySelectorAll('[data-ticket]'));
    const lines = [];
    let total = 0;

    tickets.forEach(t => {
      const name = t.dataset.ticketName || '—';
      const price = Number(t.dataset.ticketPrice || 0);
      const qtyInput = t.querySelector('[data-qty-input]');
      const qty = qtyInput ? Number(qtyInput.value || 0) : 0;
      if (qty > 0) {
        const subtotal = qty * price;
        total += subtotal;
        lines.push(`${qty}× ${name} — ${currency} ${fmt(subtotal)}`);
      }
    });

    if (linesEl) {
      linesEl.innerHTML = lines.length ? lines.map(l => `<div>${l}</div>`).join('') : `<div>${'—'}</div>`;
    }
    if (totalEl) totalEl.textContent = `${currency} ${fmt(total)}`;
  }

  root.addEventListener('click', (e) => {
    const up = e.target.closest('[data-qty-up]');
    const down = e.target.closest('[data-qty-down]');
    if (!up && !down) return;

    const ticket = e.target.closest('[data-ticket]');
    const input = ticket ? ticket.querySelector('[data-qty-input]') : null;
    if (!ticket || !input) return;

    const max = Number(input.max || ticket.dataset.ticketRemaining || 0);
    const cur = Number(input.value || 0);

    let next = cur;
    if (up) next = Math.min(cur + 1, max);
    if (down) next = Math.max(cur - 1, 0);

    input.value = String(next);
    recompute();
  });

  root.addEventListener('input', (e) => {
    const input = e.target.closest('[data-qty-input]');
    if (!input) return;
    const ticket = e.target.closest('[data-ticket]');
    const max = Number(input.max || (ticket ? ticket.dataset.ticketRemaining : 0) || 0);
    const cur = Number(input.value || 0);
    input.value = String(Math.max(0, Math.min(cur, max)));
    recompute();
  });

  recompute();
})();
</script>
@endpush
@endsection

