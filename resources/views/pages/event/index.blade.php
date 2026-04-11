@extends('layouts.app')

@section('title', 'Événements - Votix')

@push('styles')
<style>
.explore-events { margin-top: 2rem; }
.event-content .event-title { font-size: 1.05rem; font-weight: 600; }
.featured-controls .controls {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
.featured-controls .controls a.control {
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  border: 1px solid rgba(0,0,0,.2);
  outline: none;
  box-shadow: none;
  border-radius: 999px;
  padding: 0.35rem 1rem;
  font-size: 0.9rem;
}
.featured-controls .controls a.control:hover,
.featured-controls .controls a.control:focus,
.featured-controls .controls a.control:focus-visible,
.featured-controls .controls a.control:visited,
.featured-controls .controls a.control:active {
  text-decoration: none;
  border-color: rgba(0,0,0,.2);
  outline: none;
  box-shadow: none;
}
.featured-controls .controls .control.is-active,
.featured-controls .controls .control.is-active:hover,
.featured-controls .controls .control.is-active:focus,
.featured-controls .controls .control.is-active:focus-visible,
.featured-controls .controls .control.is-active:visited,
.featured-controls .controls .control.is-active:active {
  background: #111;
  border-color: #111;
}
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
                            @if(request()->filled('category_id'))
                                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
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
                                    @php
                                        $allQs = collect(request()->except('page'))->forget('category_id')->all();
                                        $allHref = route('ticketing.events', ['locale' => $locale]);
                                        $allQueryStr = http_build_query($allQs);
                                        if ($allQueryStr !== '') {
                                            $allHref .= '?' . $allQueryStr;
                                        }
                                    @endphp
                                    <a href="{{ $allHref }}" class="control {{ ! request()->filled('category_id') ? 'is-active' : '' }}">{{ __('All') }}</a>
                                    @foreach(($categories ?? []) as $cat)
                                        @continue(empty($cat['id']))
                                        @php
                                            $catQs = collect(request()->except('page'))->put('category_id', (int) $cat['id'])->all();
                                            $catHref = route('ticketing.events', ['locale' => $locale]) . '?' . http_build_query($catQs);
                                            $catActive = request()->filled('category_id') && (int) request('category_id') === (int) $cat['id'];
                                        @endphp
                                        <a href="{{ $catHref }}" class="control {{ $catActive ? 'is-active' : '' }}">
                                            {{ $cat['name'] ?? $cat['name_en'] ?? '' }}
                                        </a>
                                    @endforeach
                                </div>
                                <div class="row" data-ref="event-filter-content">
                                    @forelse($events as $event)
                                        @php
                                            $categorySlug = \Illuminate\Support\Str::slug($event['category']['name_en'] ?? $event['category']['name'] ?? '');
                                        @endphp
                                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 {{ $categorySlug ? $categorySlug : '' }}">
                                            @include('partials.event-card', ['event' => $event])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted mb-0">{{ __('No events found.') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                                @if(isset($paginator) && $paginator->hasPages())
                                    <div class="row mt-4">
                                        <div class="col-12 d-flex justify-content-center">
                                            {{ $paginator->withQueryString()->links('pagination.public-home') }}
                                        </div>
                                    </div>
                                @endif
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

