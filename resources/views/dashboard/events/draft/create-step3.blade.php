@extends('dashboard.layouts.base')

@section('title', __('Create Event'))

@section('content')
<div class="wrapper">
    <div class="event-dt-block event-create-form-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9 col-md-12">
                    <div class="wizard-steps-block">
                        <div id="add-event-tab" class="step-app">
                            <ul class="step-steps">
                                <li>
                                    <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Details</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Tickets</span>
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="{{ route('dashboard.events.draft.create.step3', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">{{ __('Tickets') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.events.draft.create.step4', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Summary</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="step-content">
                                <form method="POST"
                                      action="{{ route('dashboard.events.draft.create.step3.store', ['locale' => $locale ?? app()->getLocale()]) }}">
                                    @csrf
                                    <input type="hidden" name="draft_id" value="{{ request('draft_id') }}">

                                    @include('dashboard.events.draft.components.form-step3')

                                    <div class="step-footer step-tab-pager mt-4">
                                        <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => request('draft_id')]) }}"
                                           class="btn btn-default btn-hover steps_btn">
                                            {{ __('Previous') }}
                                        </a>
                                        <button type="submit" class="btn btn-default btn-hover steps_btn">
                                            {{ __('Next') }}
                                        </button>
                                    </div>
                                </form>
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
})();
</script>
@endpush
@endsection