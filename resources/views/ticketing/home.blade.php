@extends('layouts.app')

@section('title', __('Home') . ' - Votix')

@push('styles')
<style>
.hero-carousel { position: relative; min-height: 380px; overflow: hidden; }
.hero-carousel .carousel-item { min-height: 380px; }
.hero-slide-img { position: absolute; inset: 0; min-height: 380px; }
.hero-slide-overlay { position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.2) 100%); }
.hero-slide-content { position: absolute !important; z-index: 2; top: 50% !important; left: 50% !important; bottom: auto !important; right: auto !important; transform: translate(-50%, -50%); text-align: center; padding: 1rem; }
.hero-slide-content h2 { font-size: 1.5rem; font-weight: 700; color: #ffffff; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
.hero-slide-content p { font-size: 1rem; margin-bottom: 0.75rem; color: #ffffff; text-shadow: 0 1px 2px rgba(0,0,0,0.5); }
.hero-slide-content .main-btn { margin-top: 0.5rem; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; flex-wrap: nowrap; }
.hero-slide-content .main-btn i { flex-shrink: 0; }
@media (min-width: 768px) {
  .hero-carousel, .hero-carousel .carousel-item, .hero-slide-img { min-height: 460px; }
  .hero-slide-content h2 { font-size: 1.75rem; }
}
.home-category-filters { display: flex; justify-content: flex-start; flex-wrap: nowrap; gap: 0.5rem; padding: 1rem 0; overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; scrollbar-width: thin; }
.home-category-filters .control { flex-shrink: 0; }
@media (min-width: 992px) {
  .home-category-filters { justify-content: center; flex-wrap: wrap; overflow: visible; }
}
@media (max-width: 991.98px) {
  .home-category-filters { margin-top: 1.5rem; padding-top: 1.25rem; }
  .home-explore-events { padding-top: 1.5rem !important; }
  .home-explore-events .event-filter-items .row { margin-top: 0.5rem; }
}
.home-explore-events { padding-top: 0.5rem !important; padding-bottom: 2rem !important; }
.home-explore-events .main-title { margin-bottom: 1rem; }
.home-explore-events .main-title h3 { font-size: 1.5rem; }
</style>
@endpush

@section('content')
    <div class="wrapper">
        {{-- Hero carousel : affiches d'événements qui défilent --}}
        <div class="hero-carousel">
            <div id="heroSlider" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    {{-- Slide 1 : concert le plus hype - Remplace par asset('images/hero/slide-1.jpg') quand tu ajoutes tes affiches --}}
                    <div class="carousel-item active">
                        <div class="hero-slide-img" style="background-image: url('{{ file_exists(public_path('images/hero/slide-1.jpg')) ? asset('images/hero/slide-1.jpg') : 'https://picsum.photos/1920/600?random=1' }}'); background-size: cover; background-position: center;">
                            <div class="hero-slide-overlay"></div>
                        </div>
                        <div class="carousel-caption hero-slide-content">
                            <h2>{{ __('Concert of the year 2025') }}</h2>
                            <p>{{ __('The most anticipated event of the season') }}</p>
                            <a href="{{ route('events.show', ['locale' => $locale ?? 'fr', 'slug' => 'event-1']) }}" class="main-btn btn-hover">
                                {{ __('Acheter tickets') }}
                                <i class="fa-solid fa-arrow-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                    {{-- Slide 2 --}}
                    <div class="carousel-item">
                        <div class="hero-slide-img" style="background-image: url('{{ file_exists(public_path('images/hero/slide-2.jpg')) ? asset('images/hero/slide-2.jpg') : 'https://picsum.photos/1920/600?random=2' }}'); background-size: cover; background-position: center;">
                            <div class="hero-slide-overlay"></div>
                        </div>
                        <div class="carousel-caption hero-slide-content">
                            <h2>{{ __('Electro festival Summer Vibes') }}</h2>
                            <p>3 jours de musique en plein air</p>
                            <a href="{{ route('events.show', ['locale' => $locale ?? 'fr', 'slug' => 'event-2']) }}" class="main-btn btn-hover">
                                {{ __('Acheter tickets') }}
                                <i class="fa-solid fa-arrow-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                    {{-- Slide 3 --}}
                    <div class="carousel-item">
                        <div class="hero-slide-img" style="background-image: url('{{ file_exists(public_path('images/hero/slide-3.jpg')) ? asset('images/hero/slide-3.jpg') : 'https://picsum.photos/1920/600?random=3' }}'); background-size: cover; background-position: center;">
                            <div class="hero-slide-overlay"></div>
                        </div>
                        <div class="carousel-caption hero-slide-content">
                            <h2>{{ __('Intimate acoustic concert') }}</h2>
                            <p>{{ __('A unique evening in a small setting') }}</p>
                            <a href="{{ route('events.show', ['locale' => $locale ?? 'fr', 'slug' => 'event-3']) }}" class="main-btn btn-hover">
                                {{ __('Acheter tickets') }}
                                <i class="fa-solid fa-arrow-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">{{ __('Previous') }}</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">{{ __('Next') }}</span>
                </button>
            </div>
        </div>

        {{-- Filtres catégories (centrés, juste après la bannière) --}}
        <div class="container">
            <div class="home-category-filters">
                <button type="button" class="control" data-filter="all">{{ __('All') }}</button>
                <button type="button" class="control" data-filter=".arts">{{ __('Arts') }}</button>
                <button type="button" class="control" data-filter=".business">{{ __('Business') }}</button>
                <button type="button" class="control" data-filter=".concert">{{ __('Concert') }}</button>
                <button type="button" class="control" data-filter=".workshops">{{ __('Workshops') }}</button>
            </div>
        </div>

        {{-- Explore Events --}}
        <div class="explore-events home-explore-events">
            <div class="container">
                {{-- Barre de recherche au-dessus de "Explore Events" --}}
                {{-- Barre de recherche (masquée en responsive, uniquement overlay sur mobile) --}}
                <div class="row mb-4 d-none d-lg-flex">
                    <div class="col-12">
                        <form action="{{ route('ticketing.events', ['locale' => $locale ?? 'fr']) }}" method="GET" class="page-search-form" role="search">
                            <div class="page-search-inner">
                                <i class="fa-solid fa-magnifying-glass page-search-icon"></i>
                                <input type="search" name="q" class="page-search-input" placeholder="{{ __('Search placeholder') }}" aria-label="{{ __('Search') }}" value="{{ request('q') }}">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="event-filter-items">
                            <div class="featured-controls">
                                <div class="row" data-ref="event-filter-content">
                                    @foreach($events as $event)
                                        @php
                                            $cover = votix_media_url($event['cover_url'] ?? null) ?? asset('images/event-imgs/img-1.jpg');
                                            $occ   = $event['occurrences'][0] ?? null;
                                            $start = $occ['start_date'] ?? null;
                                            $price = $event['price_min'] ?? null;
                                            $city  = $event['city'] ?? null;
                                        @endphp
                                        <div
                                            class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mix"
                                            data-ref="mixitup-target">
                                            <div class="main-card mt-4">
                                                <div class="event-thumbnail">
                                                    <a href="{{ route('events.show', ['locale' => $locale ?? 'fr', 'slug' => $event['slug']]) }}"
                                                        class="thumbnail-img">
                                                        <img src="{{ $cover }}" alt="">
                                                    </a>
                                                </div>
                                                <div class="event-content">
                                                    <a href="{{ route('events.show', ['locale' => $locale ?? 'fr', 'slug' => $event['slug']]) }}"
                                                        class="event-title">{{ $event['title'] ?? '—' }}</a>
                                                    <div class="duration-price-remaining">
                                                        @if($price !== null)
                                                            <span class="duration-price">
                                                                {{ $event['currency'] ?? '' }} {{ number_format((float) $price, 0, ',', ' ') }}
                                                            </span>
                                                        @endif
                                                        @if($city)
                                                            <span class="remaining">{{ $city }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="event-footer">
                                                    <div class="event-timing">
                                                        @if($start)
                                                            <div class="publish-date">
                                                                <span><i
                                                                        class="fa-solid fa-calendar-day me-2"></i>{{ \Carbon\Carbon::parse($start)->translatedFormat('d M') }}</span>
                                                                <span class="dot"><i class="fa-solid fa-circle"></i></span>
                                                                <span>{{ \Carbon\Carbon::parse($start)->translatedFormat('D, H:i') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

