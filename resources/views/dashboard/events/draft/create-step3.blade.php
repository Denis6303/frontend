@extends('dashboard.layouts.base')

@section('title', __('Create Event'))

@section('content')
<div class="wrapper">
    <div class="event-dt-block event-create-form-block">
        <div class="ef-root">
            <div class="ef-shell">

                <div class="ef-stepper">
                    <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => request('draft_id')]) }}"
                       class="ef-step">
                        <div class="ef-step-num">1</div>
                        <span class="ef-step-label">{{ __('Details') }}</span>
                        <span class="ef-step-line"></span>
                    </a>
                    <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => request('draft_id')]) }}"
                       class="ef-step">
                        <div class="ef-step-num">2</div>
                        <span class="ef-step-label">{{ __('Location & Dates') }}</span>
                        <span class="ef-step-line"></span>
                    </a>
                    <a href="{{ route('dashboard.events.draft.create.step3', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => request('draft_id')]) }}"
                       class="ef-step active">
                        <div class="ef-step-num">3</div>
                        <span class="ef-step-label">{{ __('Tickets') }}</span>
                        <span class="ef-step-line"></span>
                    </a>
                    <a href="{{ route('dashboard.events.draft.create.step4', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => request('draft_id')]) }}"
                       class="ef-step">
                        <div class="ef-step-num">4</div>
                        <span class="ef-step-label">{{ __('Summary') }}</span>
                    </a>
                </div>

                <div class="ef-card">
                    <div class="ef-header">
                        <div class="ef-header-icon">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <div>
                            <h2 class="ef-header-title">{{ __('Tickets') }}</h2>
                            <p class="ef-header-sub">
                                {{ __('Configure your free or paid tickets, quantities and conditions.') }}
                            </p>
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('dashboard.events.draft.create.step3.store', ['locale' => $locale ?? app()->getLocale()]) }}">
                        @csrf
                        <input type="hidden" name="draft_id" value="{{ request('draft_id') }}">

                        <div class="ef-body">
                            <div class="ef-col" style="grid-column: 1 / -1;">
                                @include('dashboard.events.draft.components.form-step3')
                            </div>
                        </div>

                        <div class="ef-footer">
                            <div class="ef-progress">
                                <div class="ef-progress-dots">
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot"></div>
                                </div>
                                <span class="ef-progress-text">{{ __('Step 3 of 4') }}</span>
                            </div>
                            <div>
                                <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => request('draft_id')]) }}"
                                   class="btn btn-outline-dark me-2">
                                    <i class="fa-solid fa-arrow-left-long me-2"></i>{{ __('Previous') }}
                                </a>
                                <button type="submit" class="ef-btn-next">
                                    {{ __('Continue') }}
                                    <i class="fa-solid fa-arrow-right-long ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/form-step.css') }}">
@endpush

@push('scripts')
<script>
(function() {
    var tickets = [];
    var currency = 'XOF';

    function renderTicketCard(t, idx) {
        var price = parseFloat(t.price) === 0 ? '{{ __("Free") }}' : t.price + ' ' + currency;
        return '<div class="price-ticket-card mt-4" data-idx="' + idx + '">' +
            '<div class="price-ticket-card-head d-md-flex flex-wrap align-items-start justify-content-between position-relative p-4">' +
                '<div class="d-flex align-items-center top-name">' +
                    '<div class="icon-box">' +
                        '<span class="icon-big rotate-icon icon icon-purple"><i class="fa-solid fa-ticket"></i></span>' +
                        '<h5 class="fs-16 mb-1 mt-1">' + (t.name || 'Ticket') + ' - ' + price + '</h5>' +
                        '<p class="text-gray-50 m-0"><span class="visitor-date-time">' + (t.online_quantity || 0) + ' {{ __("tickets") }}</span></p>' +
                    '</div>' +
                '</div>' +
                '<div class="d-flex align-items-center">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger ticket-remove" data-idx="' + idx + '"><i class="fa-solid fa-trash-can"></i></button>' +
                '</div>' +
            '</div>' +
            '<div class="price-ticket-card-body border_top p-4">' +
                '<div class="full-width d-flex flex-wrap justify-content-between align-items-center">' +
                    '<div class="icon-box">' +
                        '<div class="icon me-3"><i class="fa-solid fa-ticket"></i></div>' +
                        '<span class="text-145">{{ __("Total tickets") }}</span>' +
                        '<h6 class="coupon-status">' + (t.online_quantity || 0) + '</h6>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    function updateHiddenInputs() {
        var container = document.getElementById('ticket-hidden-inputs');
        if (!container) return;
        container.innerHTML = '';
        tickets.forEach(function(t, i) {
            ['name','price','online_quantity','print_quantity','description','general_conditions'].forEach(function(k) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'tickets[' + i + '][' + k + ']';
                inp.value = t[k] || (k === 'price' ? '0' : k === 'online_quantity' ? '1' : k === 'print_quantity' ? '0' : '');
                container.appendChild(inp);
            });
        });
    }

    function updateUI() {
        var list = document.getElementById('ticket-list');
        var empty = document.getElementById('ticket-empty');
        var counter = document.getElementById('ticket-counter');
        if (!list || !empty) return;
        list.innerHTML = tickets.map(function(t, i) { return renderTicketCard(t, i); }).join('');
        empty.style.display = tickets.length ? 'none' : 'block';
        if (counter) counter.textContent = tickets.length;
        updateHiddenInputs();

        list.querySelectorAll('.ticket-remove').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var idx = parseInt(btn.getAttribute('data-idx'), 10);
                tickets.splice(idx, 1);
                updateUI();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('addTicketModal');
        var addBtn = document.getElementById('ticket-modal-add');
        if (!modal || !addBtn) return;

        function clearModal() {
            document.getElementById('ticket-name').value = '';
            document.getElementById('ticket-price').value = '0';
            document.getElementById('ticket-online-qty').value = '1';
            document.getElementById('ticket-print-qty').value = '0';
            document.getElementById('ticket-description').value = '';
            document.getElementById('ticket-conditions').value = '';
        }

        addBtn.addEventListener('click', function() {
            var name = (document.getElementById('ticket-name').value || '').trim();
            if (!name) { alert('{{ __("Ticket name is required") }}'); return; }
            var freeEvent = document.querySelector('input[name="free_event"]:checked');
            var isFree = freeEvent && freeEvent.value === 'true';
            var price = parseFloat(document.getElementById('ticket-price').value) || 0;
            if (!isFree && price < 0) price = 0;
            if (isFree) price = 0;

            tickets.push({
                name: name,
                price: String(price),
                online_quantity: parseInt(document.getElementById('ticket-online-qty').value, 10) || 1,
                print_quantity: parseInt(document.getElementById('ticket-print-qty').value, 10) || 0,
                description: (document.getElementById('ticket-description').value || '').trim(),
                general_conditions: (document.getElementById('ticket-conditions').value || '').trim()
            });
            updateUI();
            clearModal();
            var bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        });

        modal.addEventListener('hidden.bs.modal', clearModal);
        document.querySelector('form')?.addEventListener('submit', function(e) {
            if (tickets.length === 0) {
                e.preventDefault();
                alert('{{ __("Add at least one ticket") }}');
                return false;
            }
        });

        updateUI();
    });
    // --- Card-style radio toggles (free_event, etc.) ---
    document.querySelectorAll('.ef-attendance').forEach(function(group) {
        group.querySelectorAll('.ef-att-card').forEach(function(card) {
            card.addEventListener('click', function () {
                var radio = card.querySelector('input[type="radio"]');
                if (!radio) return;
                group.querySelectorAll('.ef-att-card').forEach(function (c) {
                    c.classList.remove('selected');
                });
                card.classList.add('selected');
                radio.checked = true;
            });
        });
    });
})();
</script>
@endpush
@endsection
