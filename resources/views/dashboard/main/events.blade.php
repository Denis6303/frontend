@extends('dashboard.layouts.base')

@section('title', __('Events'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-card">
                <div class="dashboard-wrap-content p-4">
                    <h5 class="mb-4">{{ __('Events') }}</h5>
                    <div class="d-md-flex flex-wrap align-items-center gap-3">
                        <form method="get" action="{{ route('dashboard.events.index', ['locale' => $locale]) }}" class="dashboard-events-search-form vtx-dashboard-live-search mb-0">
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
                                <button class="tab-link {{ $activeTab === 'upcoming' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#upcoming-tab" type="button" role="tab" aria-controls="upcoming-tab" aria-selected="{{ $activeTab === 'upcoming' ? 'true' : 'false' }}">
                                    {{ __('Upcoming') }} (<span class="total_event_counter">{{ $eventPaginators['upcoming']->total() ?? 0 }}</span>)
                                </button>
                                <button class="tab-link {{ $activeTab === 'completed' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#past-tab" type="button" role="tab" aria-controls="past-tab" aria-selected="{{ $activeTab === 'completed' ? 'true' : 'false' }}">
                                    {{ __('Past') }} (<span class="total_event_counter">{{ $eventPaginators['completed']->total() ?? 0 }}</span>)
                                </button>
                                <button class="tab-link {{ $activeTab === 'saved' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#draft-tab" type="button" role="tab" aria-controls="draft-tab" aria-selected="{{ $activeTab === 'saved' ? 'true' : 'false' }}">
                                    {{ __('Drafts') }} (<span class="total_event_counter">{{ $eventPaginators['saved']->total() ?? 0 }}</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="event-list dashboard-events-list">
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
    function debounce(fn, wait) {
        var t;
        return function () {
            var ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, wait);
        };
    }
    document.addEventListener('DOMContentLoaded', function () {
        var searchForm = document.querySelector('.vtx-dashboard-live-search');
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
                    ['page_upcoming', 'page_completed', 'page_saved'].forEach(function (k) {
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
