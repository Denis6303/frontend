@extends('dashboard.layouts.base')

@section('title', __('My Tickets'))

@push('styles')
<link href="{{ asset('dashboard/css/dashboard-tickets.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid vtx-tickets-page">
    <div class="row">
        <div class="col-md-12">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                </div>
            @endif

            <div class="main-card">
                <div class="dashboard-wrap-content p-4">
                    <h5 class="mb-4">{{ __('My Tickets') }}</h5>
                    <div class="d-md-flex flex-wrap align-items-center gap-3">
                        <form method="get" action="{{ route('dashboard.tickets.index', ['locale' => $locale]) }}" class="dashboard-events-search-form mb-0">
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
                                            placeholder="{{ __('Rechercher') }}"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="rs ms-auto mt_r4">
                            <div class="nav custom2-tabs btn-group" role="tablist">
                                <button class="tab-link {{ $activeTab === 'upcoming' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tickets-upcoming-tab" type="button" role="tab" aria-controls="tickets-upcoming-tab" aria-selected="{{ $activeTab === 'upcoming' ? 'true' : 'false' }}">
                                    À venir (<span class="total_event_counter">{{ $ticketPaginators['upcoming']->total() ?? 0 }}</span>)
                                </button>
                                <button class="tab-link {{ $activeTab === 'past' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tickets-past-tab" type="button" role="tab" aria-controls="tickets-past-tab" aria-selected="{{ $activeTab === 'past' ? 'true' : 'false' }}">
                                    Passés (<span class="total_event_counter">{{ $ticketPaginators['past']->total() ?? 0 }}</span>)
                                </button>
                                <button class="tab-link {{ $activeTab === 'cancelled' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tickets-cancelled-tab" type="button" role="tab" aria-controls="tickets-cancelled-tab" aria-selected="{{ $activeTab === 'cancelled' ? 'true' : 'false' }}">
                                    Annulés (<span class="total_event_counter">{{ $ticketPaginators['cancelled']->total() ?? 0 }}</span>)
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
    document.addEventListener('DOMContentLoaded', function () {
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
    });
})();
</script>
@endpush
