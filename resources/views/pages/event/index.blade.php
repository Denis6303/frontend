@extends('layouts.app')

@section('title', 'Événements - Votix')

@push('styles')
<style>
.explore-events { margin-top: 2rem; }
.event-content .event-title { font-size: 1.05rem; font-weight: 600; }
</style>
@endpush

@section('content')
    <div class="wrapper">
        <div class="explore-events p-80">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <form action="{{ route('ticketing.events', ['locale' => $locale]) }}" method="GET"
                              class="page-search-form vtx-explore-live-search" role="search">
                            @foreach((array) request('statuses', $filters['statuses'] ?? ['upcoming']) as $st)
                                <input type="hidden" name="statuses[]" value="{{ $st }}">
                            @endforeach
                            @if(request('country_code', $filters['country_code'] ?? null))
                                <input type="hidden" name="country_code" value="{{ request('country_code', $filters['country_code']) }}">
                            @endif
                            @if(request('location', $filters['location'] ?? null))
                                <input type="hidden" name="location" value="{{ request('location', $filters['location']) }}">
                            @endif
                            <div class="page-search-inner">
                                <i class="fa-solid fa-magnifying-glass page-search-icon"></i>
                                <input type="search" name="query" class="page-search-input"
                                       placeholder="{{ __('Search placeholder') }}" aria-label="{{ __('Search') }}"
                                       value="{{ old('query', $search_query ?? request('query', request('q'))) }}"
                                       autocomplete="off">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="main-title">
                            <h3>Explore Events</h3>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="event-filter-items">
                            <div class="featured-controls">
                                <div class="controls">
                                    <button type="button" class="control" data-filter="all">All</button>
                                    @foreach(($categories ?? []) as $cat)
                                        @php
                                            $slug = \Illuminate\Support\Str::slug($cat['name_en'] ?? $cat['name'] ?? '');
                                        @endphp
                                        <button type="button" class="control" data-filter=".{{ $slug }}">
                                            {{ $cat['name'] ?? $cat['name_en'] ?? '' }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="row" data-ref="event-filter-content">
                                    @forelse($events as $event)
                                        @php
                                            $categorySlug = \Illuminate\Support\Str::slug($event['category']['name_en'] ?? $event['category']['name'] ?? '');
                                        @endphp
                                        <div
                                            class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mix {{ $categorySlug ? $categorySlug : '' }}"
                                            data-ref="mixitup-target">
                                            @include('partials.event-card', ['event' => $event])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted mb-0">{{ __('No events found.') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var searchForm = document.querySelector('.vtx-explore-live-search');
    if (!searchForm) return;
    var searchInput = searchForm.querySelector('input[name="query"]');
    if (!searchInput) return;

    function debounce(fn, wait) {
        var t;
        return function () {
            var ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, wait);
        };
    }

    function buildUrl() {
        var params = new URLSearchParams(new FormData(searchForm));
        var v = (searchInput.value || '').trim();
        if (v) {
            params.set('query', v);
        } else {
            params.delete('query');
        }
        params.delete('page');
        var u = new URL(searchForm.action, window.location.origin);
        var qs = params.toString();
        return u.pathname + (qs ? '?' + qs : '') + u.hash;
    }

    function goSearch() {
        window.location.href = buildUrl();
    }

    var lastQuerySent = (searchInput.value || '').trim();
    var navigateSearch = debounce(function () {
        var v = (searchInput.value || '').trim();
        if (v === lastQuerySent) return;
        lastQuerySent = v;
        goSearch();
    }, 350);

    searchInput.addEventListener('input', navigateSearch);
    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        goSearch();
    });
})();
</script>
@endpush

