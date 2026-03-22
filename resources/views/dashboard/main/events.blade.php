@extends('dashboard.layouts.base')

@section('title', __('Events'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- <div class="col-md-12">
            <div class="d-main-title">
                <h3><i class="fa-solid fa-calendar-days me-3"></i>{{ __('Events') }}</h3>
            </div>
        </div> -->
        <div class="col-md-12">
            <div class="main-card mt-5">
                <div class="dashboard-wrap-content p-4">
                    <h5 class="mb-4">{{ __('Events') }}</h5>
                    <div class="d-md-flex flex-wrap align-items-center">
                        <div class="dashboard-date-wrap">
                            <div class="form-group">
                                <div class="relative-input position-relative">
                                    <input class="form-control h_40" type="text" placeholder="Search by event name, status" value="">
                                    <i class="uil uil-search"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rs ms-auto mt_r4">
                            <div class="nav custom2-tabs btn-group" role="tablist">
                                <button class="tab-link {{ $activeTab === 'upcoming' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#upcoming-tab" type="button" role="tab" aria-controls="upcoming-tab" aria-selected="{{ $activeTab === 'upcoming' ? 'true' : 'false' }}">
                                    À venir (<span class="total_event_counter">{{ count($eventsByStatus['upcoming'] ?? []) }}</span>)
                                </button>
                                <button class="tab-link {{ $activeTab === 'completed' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#past-tab" type="button" role="tab" aria-controls="past-tab" aria-selected="{{ $activeTab === 'completed' ? 'true' : 'false' }}">
                                    Passés (<span class="total_event_counter">{{ count($eventsByStatus['completed'] ?? []) }}</span>)
                                </button>
                                <button class="tab-link {{ $activeTab === 'saved' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#draft-tab" type="button" role="tab" aria-controls="draft-tab" aria-selected="{{ $activeTab === 'saved' ? 'true' : 'false' }}">
                                    Brouillons (<span class="total_event_counter">{{ count($eventsByStatus['saved'] ?? []) }}</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="event-list">
                <div class="tab-content">
                    @include('dashboard.events.upcoming')
                    @include('dashboard.events.past')
                    @include('dashboard.events.draft')
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
        var map = { 'upcoming-tab': 'upcoming', 'past-tab': 'completed', 'draft-tab': 'saved' };
        return map[paneId] || 'upcoming';
    }
    function syncTabQuery(tab) {
        var u = new URL(window.location.href);
        u.searchParams.set('tab', tab);
        window.history.replaceState({}, '', u);
        document.querySelectorAll('input[name="tab"]').forEach(function (inp) {
            inp.value = tab;
        });
        document.querySelectorAll('.dashboard-event-action-link').forEach(function (a) {
            try {
                var href = a.getAttribute('href');
                if (!href || href.indexOf('#') === 0) return;
                var u2 = new URL(href, window.location.origin);
                u2.searchParams.set('tab', tab);
                a.setAttribute('href', u2.pathname + u2.search + u2.hash);
            } catch (e) {}
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        var u = new URL(window.location.href);
        var initial = u.searchParams.get('tab');
        if (initial) {
            syncTabQuery(initial);
        }
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (btn) {
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