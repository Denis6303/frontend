@extends('layouts.app')

@section('title', 'Accueil billeterie - Votix')

@push('styles')
<style>
.hero-carousel { position: relative; min-height: 240px; overflow: hidden; }
.hero-carousel .carousel-item { min-height: 240px; }
.hero-slide-img { position: absolute; inset: 0; min-height: 240px; }
.hero-slide-overlay { position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.2) 100%); }
.hero-slide-content { position: absolute !important; z-index: 2; top: 50% !important; left: 50% !important; bottom: auto !important; right: auto !important; transform: translate(-50%, -50%); text-align: center; padding: 1rem; }
.hero-slide-content h2 { font-size: 1.5rem; font-weight: 700; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
.hero-slide-content p { font-size: 1rem; margin-bottom: 0.75rem; text-shadow: 0 1px 2px rgba(0,0,0,0.5); }
.hero-slide-content .main-btn { margin-top: 0.5rem; }
@media (min-width: 768px) {
  .hero-carousel, .hero-carousel .carousel-item, .hero-slide-img { min-height: 280px; }
  .hero-slide-content h2 { font-size: 1.75rem; }
}
.home-category-filters { display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; padding: 1rem 0; }
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
                    {{-- Slide 1 : concert le plus hype - Remplace par asset('template/images/hero/slide-1.jpg') quand tu ajoutes tes affiches --}}
                    <div class="carousel-item active">
                        <div class="hero-slide-img" style="background-image: url('{{ file_exists(public_path('template/images/hero/slide-1.jpg')) ? asset('template/images/hero/slide-1.jpg') : 'https://picsum.photos/1920/600?random=1' }}'); background-size: cover; background-position: center;">
                            <div class="hero-slide-overlay"></div>
                        </div>
                        <div class="carousel-caption hero-slide-content">
                            <h2>Concert de l'année 2025</h2>
                            <p>L'événement le plus attendu de la saison</p>
                            <a href="{{ route('ticketing.events.show', ['id' => 1]) }}" class="main-btn btn-hover">
                                Acheter tickets
                                <i class="fa-solid fa-arrow-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                    {{-- Slide 2 --}}
                    <div class="carousel-item">
                        <div class="hero-slide-img" style="background-image: url('{{ file_exists(public_path('template/images/hero/slide-2.jpg')) ? asset('template/images/hero/slide-2.jpg') : 'https://picsum.photos/1920/600?random=2' }}'); background-size: cover; background-position: center;">
                            <div class="hero-slide-overlay"></div>
                        </div>
                        <div class="carousel-caption hero-slide-content">
                            <h2>Festival électro Summer Vibes</h2>
                            <p>3 jours de musique en plein air</p>
                            <a href="{{ route('ticketing.events.show', ['id' => 2]) }}" class="main-btn btn-hover">
                                Acheter tickets
                                <i class="fa-solid fa-arrow-right ms-3"></i>
                            </a>
                        </div>
                    </div>
                    {{-- Slide 3 --}}
                    <div class="carousel-item">
                        <div class="hero-slide-img" style="background-image: url('{{ file_exists(public_path('template/images/hero/slide-3.jpg')) ? asset('template/images/hero/slide-3.jpg') : 'https://picsum.photos/1920/600?random=3' }}'); background-size: cover; background-position: center;">
                            <div class="hero-slide-overlay"></div>
                        </div>
                        <div class="carousel-caption hero-slide-content">
                            <h2>Concert acoustique intimiste</h2>
                            <p>Une soirée unique en petit comité</p>
                            <a href="{{ route('ticketing.events.show', ['id' => 3]) }}" class="main-btn btn-hover">
                                Acheter tickets
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
            <div class="home-category-filters">
                <button type="button" class="control" data-filter="all">All</button>
                <button type="button" class="control" data-filter=".arts">Arts</button>
                <button type="button" class="control" data-filter=".business">Business</button>
                <button type="button" class="control" data-filter=".concert">Concert</button>
                <button type="button" class="control" data-filter=".workshops">Workshops</button>
            </div>
        </div>

        {{-- Explore Events --}}
        <div class="explore-events home-explore-events">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="main-title">
                            <h3>Explore Events</h3>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="event-filter-items">
                            <div class="featured-controls">
                                <div class="row" data-ref="event-filter-content">
                                    <div
                                        class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mix arts concert workshops volunteer sports health_Wellness"
                                        data-ref="mixitup-target">
                                        <div class="main-card mt-4">
                                            <div class="event-thumbnail">
                                                <a href="{{ route('ticketing.events.show', ['id' => 1]) }}"
                                                    class="thumbnail-img">
                                                    <img src="{{ asset('template/images/event-imgs/img-1.jpg') }}"
                                                        alt="">
                                                </a>
                                                <span class="bookmark-icon" title="Bookmark"></span>
                                            </div>
                                            <div class="event-content">
                                                <a href="{{ route('ticketing.events.show', ['id' => 1]) }}"
                                                    class="event-title">A New Way Of Life</a>
                                                <div class="duration-price-remaining">
                                                    <span class="duration-price">AUD $100.00*</span>
                                                    <span class="remaining"></span>
                                                </div>
                                            </div>
                                            <div class="event-footer">
                                                <div class="event-timing">
                                                    <div class="publish-date">
                                                        <span><i
                                                                class="fa-solid fa-calendar-day me-2"></i>15 Apr</span>
                                                        <span class="dot"><i class="fa-solid fa-circle"></i></span>
                                                        <span>Fri, 3.45 PM</span>
                                                    </div>
                                                    <span class="publish-time"><i
                                                            class="fa-solid fa-clock me-2"></i>1h</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Autres cartes issues du backend plus tard --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

