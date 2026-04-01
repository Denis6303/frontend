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
.home-category-filters .control { flex-shrink: 0; border-radius: 999px; padding-inline: 1rem; }
@media (min-width: 992px) {
  .home-category-filters { justify-content: center; flex-wrap: wrap; overflow: visible; }
}
@media (max-width: 991.98px) {
  .home-category-filters-scroll { position: relative; }
  .home-category-filters-scroll .category-scroll-arrow {
    display: flex;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: #fff;
    border: 1px solid rgba(0,0,0,.15);
    color: #000;
    cursor: pointer;
    z-index: 2;
  }
  .home-category-filters-scroll .category-scroll-arrow.category-scroll-left { left: -10px; }
  .home-category-filters-scroll .category-scroll-arrow.category-scroll-right { right: -10px; }

  /* 2 lignes "visibles" + scroll horizontal (sans perdre l’UX) */
  .home-category-filters {
    display: grid;
    grid-auto-flow: column;
    grid-template-rows: repeat(2, auto);
    grid-auto-columns: max-content;
    align-content: start;
    justify-content: start;
    gap: 0.5rem;
    padding: 1rem 0;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
  }
  .home-category-filters .control {
    flex-shrink: 0;
    justify-self: start;
    white-space: nowrap;
  }
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
                            <h2>Concert de l'année 2025</h2>
                            <p>L'événement le plus attendu de la saison</p>
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
                            <h2>Festival électro Summer Vibes</h2>
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
                            <h2>Concert acoustique intimiste</h2>
                            <p>Une soirée unique en petit comité</p>
                            <a href="{{ route('events.show', ['locale' => $locale ?? 'fr', 'slug' => 'event-3']) }}" class="main-btn btn-hover">
                                {{ __('Acheter tickets') }}
                                <i class="fa-solid fa-arrow-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>
            </div>
        </div>

        {{-- Filtres catégories (centrés, juste après la bannière) --}}
        <div class="container">
            <div class="home-category-filters-scroll">
                <button type="button" class="category-scroll-arrow category-scroll-left" aria-label="Scroll categories left">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div id="homeCategoryFilters" class="home-category-filters">
                    <button type="button" class="control" data-filter="all">{{ __('All') }}</button>
                    @foreach(($categories ?? []) as $cat)
                        @php
                            $slug = \Illuminate\Support\Str::slug($cat['name_en'] ?? $cat['name'] ?? '');
                        @endphp
                        <button type="button" class="control" data-filter=".{{ $slug }}">
                            {{ $cat['name'] ?? $cat['name_en'] ?? '' }}
                        </button>
                    @endforeach
                </div>
                <button type="button" class="category-scroll-arrow category-scroll-right" aria-label="Scroll categories right">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
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
                                            $categorySlug = \Illuminate\Support\Str::slug($event['category']['name_en'] ?? $event['category']['name'] ?? '');
                                        @endphp
                                        <div
                                            class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mix {{ $categorySlug ? $categorySlug : '' }}"
                                            data-ref="mixitup-target">
                                            @include('partials.event-card', ['event' => $event])
                                        </div>
                                    @endforeach
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
@endsection

@push('scripts')
<script>
    (function () {
        const container = document.getElementById('homeCategoryFilters');
        if (!container) return;

        const leftBtn = document.querySelector('.home-category-filters-scroll .category-scroll-left');
        const rightBtn = document.querySelector('.home-category-filters-scroll .category-scroll-right');
        if (!leftBtn || !rightBtn) return;

        const updateButtons = () => {
            const maxLeft = container.scrollWidth - container.clientWidth;
            const left = container.scrollLeft;

            leftBtn.style.visibility = left <= 2 ? 'hidden' : 'visible';
            rightBtn.style.visibility = left >= maxLeft - 2 ? 'hidden' : 'visible';
        };

        leftBtn.addEventListener('click', () => {
            container.scrollBy({ left: -220, behavior: 'smooth' });
        });

        rightBtn.addEventListener('click', () => {
            container.scrollBy({ left: 220, behavior: 'smooth' });
        });

        container.addEventListener('scroll', updateButtons, { passive: true });
        window.addEventListener('resize', updateButtons);
        updateButtons();
    })();
</script>
@endpush

