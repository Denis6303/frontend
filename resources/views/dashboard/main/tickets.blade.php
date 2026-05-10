@extends('dashboard.layouts.base')

@section('title', __('My Tickets'))

@push('styles')
<link href="{{ asset('dashboard/css/dashboard-tickets.css') }}" rel="stylesheet">
@endpush

@push('scripts')
@vite(['resources/js/ticket-qr.js'])
@endpush

@section('content')
<div class="container-fluid vtx-tickets-page">
    <div class="row">
        <div class="col-md-12">
            <div class="main-card">
                <div class="dashboard-wrap-content p-4">
                    <h5 class="mb-4">{{ __('My Tickets') }}</h5>
                    <div class="d-md-flex flex-wrap align-items-center gap-3">
                        <form method="get" action="{{ route('dashboard.tickets.index', ['locale' => $locale]) }}" class="dashboard-events-search-form vtx-dashboard-live-search mb-0">
                            <input type="hidden" name="tab" value="{{ $activeTab }}">
                            <div class="dashboard-date-wrap dashboard-events-search-wrap">
                                <div class="form-group mb-0">
                                    <div class="relative-input position-relative">
                                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                        <input
                                            class="form-control h_40"
                                            type="search"
                                            name="query"
                                            value="{{ request('query') }}"
                                            placeholder="{{ __('Search by event name') }}"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="rs ms-auto mt_r4">
                            <div class="nav custom2-tabs btn-group" role="tablist">
                                <button class="tab-link {{ $activeTab === 'upcoming' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tickets-upcoming-tab" type="button" role="tab" aria-controls="tickets-upcoming-tab" aria-selected="{{ $activeTab === 'upcoming' ? 'true' : 'false' }}">
                                    {{ __('Upcoming') }} (<span class="total_event_counter">{{ $ticketPaginators['upcoming']->total() ?? 0 }}</span>)
                                </button>
                                <button class="tab-link {{ $activeTab === 'past' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tickets-past-tab" type="button" role="tab" aria-controls="tickets-past-tab" aria-selected="{{ $activeTab === 'past' ? 'true' : 'false' }}">
                                    {{ __('Past') }} (<span class="total_event_counter">{{ $ticketPaginators['past']->total() ?? 0 }}</span>)
                                </button>
                                <button class="tab-link {{ $activeTab === 'cancelled' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tickets-cancelled-tab" type="button" role="tab" aria-controls="tickets-cancelled-tab" aria-selected="{{ $activeTab === 'cancelled' ? 'true' : 'false' }}">
                                    {{ __('Cancelled') }} (<span class="total_event_counter">{{ $ticketPaginators['cancelled']->total() ?? 0 }}</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="event-list dashboard-events-list">
                <div class="tab-content">
                    @include('dashboard.tickets.upcoming')
                    @include('dashboard.tickets.past')
                    @include('dashboard.tickets.cancelled')
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade vtx-ticket-action-modal" id="ticketTransferModal" tabindex="-1" aria-labelledby="ticketTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable vtx-ticket-action-modal__dialog">
        <div class="modal-content vtx-ticket-action-modal__content">
            <form id="ticketTransferForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketTransferModalLabel">{{ __('Transfer ticket') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="transferEmail" class="form-label">{{ __('Email') }}</label>
                        <input type="email" id="transferEmail" name="email" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label for="transferPassword" class="form-label">{{ __('Password') }}</label>
                        <input type="password" id="transferPassword" name="password" class="form-control" required autocomplete="current-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-dark">{{ __('Transfer ticket') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade vtx-ticket-action-modal" id="ticketCancelModal" tabindex="-1" aria-labelledby="ticketCancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable vtx-ticket-action-modal__dialog">
        <div class="modal-content vtx-ticket-action-modal__content">
            <form id="ticketCancelForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketCancelModalLabel">{{ __('Cancel ticket') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">{{ __('Reason') }}</label>
                        <input type="text" id="cancelReason" name="reason" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label for="cancelPassword" class="form-label">{{ __('Password') }}</label>
                        <input type="password" id="cancelPassword" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Keep ticket') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Cancel ticket') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function tabFromPaneId(paneId) {
        var map = {
            'tickets-upcoming-tab': 'upcoming',
            'tickets-past-tab': 'past',
            'tickets-cancelled-tab': 'cancelled'
        };
        return map[paneId] || 'upcoming';
    }
    function syncTabQuery(tab) {
        var u = new URL(window.location.href);
        u.searchParams.set('tab', tab);
        window.history.replaceState({}, '', u);
        document.querySelectorAll('.vtx-tickets-page input[name="tab"]').forEach(function (inp) {
            inp.value = tab;
        });
    }
    function debounce(fn, wait) {
        var t;
        return function () {
            var ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, wait);
        };
    }
    document.addEventListener('DOMContentLoaded', function () {
        var searchForm = document.querySelector('.vtx-tickets-page .vtx-dashboard-live-search');
        if (searchForm) {
            var searchInput = searchForm.querySelector('input[name="query"]');
            if (searchInput) {
                var lastQuerySent = (searchInput.value || '').trim();
                var navigateSearch = debounce(function () {
                    var v = (searchInput.value || '').trim();
                    if (v === lastQuerySent) {
                        return;
                    }
                    lastQuerySent = v;
                    var u = new URL(window.location.href);
                    var tabInp = searchForm.querySelector('input[name="tab"]');
                    if (tabInp && tabInp.value) {
                        u.searchParams.set('tab', tabInp.value);
                    }
                    if (v) {
                        u.searchParams.set('query', v);
                    } else {
                        u.searchParams.delete('query');
                    }
                    ['page_upcoming', 'page_past', 'page_cancelled'].forEach(function (k) {
                        u.searchParams.delete(k);
                    });
                    window.location.href = u.pathname + u.search + u.hash;
                }, 350);
                searchInput.addEventListener('input', navigateSearch);
                searchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                });
            }
        }
        var u = new URL(window.location.href);
        var initial = u.searchParams.get('tab');
        if (initial) {
            syncTabQuery(initial);
        }
        document.querySelectorAll('.vtx-tickets-page [data-bs-toggle="tab"]').forEach(function (btn) {
            btn.addEventListener('shown.bs.tab', function (e) {
                var target = e.target.getAttribute('data-bs-target');
                if (!target) return;
                var paneId = target.replace('#', '');
                syncTabQuery(tabFromPaneId(paneId));
            });
        });

        var transferModalEl = document.getElementById('ticketTransferModal');
        var cancelModalEl = document.getElementById('ticketCancelModal');
        var transferForm = document.getElementById('ticketTransferForm');
        var cancelForm = document.getElementById('ticketCancelForm');

        var transferModal = transferModalEl && window.bootstrap ? new bootstrap.Modal(transferModalEl) : null;
        var cancelModal = cancelModalEl && window.bootstrap ? new bootstrap.Modal(cancelModalEl) : null;

        document.querySelectorAll('[data-ticket-transfer]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-ticket-id');
                if (!id || !transferForm || !transferModal) return;
                transferForm.action = '{{ route('dashboard.tickets.transfer', ['locale' => $locale, 'id' => '__ID__']) }}'.replace('__ID__', id);
                transferModal.show();
            });
        });

        document.querySelectorAll('[data-ticket-cancel]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-ticket-id');
                if (!id || !cancelForm || !cancelModal) return;
                cancelForm.action = '{{ route('dashboard.tickets.cancel', ['locale' => $locale, 'id' => '__ID__']) }}'.replace('__ID__', id);
                cancelModal.show();
            });
        });
    });
})();
</script>
@endpush
