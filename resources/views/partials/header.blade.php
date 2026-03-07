<header class="header">
    <div class="header-inner">
        <nav class="navbar navbar-expand-lg bg-barren barren-head navbar fixed-top justify-content-sm-start pt-0 pb-0">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                    aria-controls="offcanvasNavbar">
                    <span class="navbar-toggler-icon">
                        <i class="fa-solid fa-bars"></i>
                    </span>
                </button>
                <a class="navbar-brand order-1 order-lg-0 ml-lg-0 ml-2" href="{{ route('home', ['locale' => $locale ?? 'fr']) }}">
                    <div class="res-main-logo">
                        <img src="{{ asset('template/images/logo-icon.svg') }}" alt="">
                    </div>
                    <div class="main-logo" id="logo">
                        <img src="{{ asset('template/images/logo.svg') }}" alt="">
                        <img class="logo-inverse" src="{{ asset('template/images/dark-logo.svg') }}" alt="">
                    </div>
                </a>
                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <div class="offcanvas-logo" id="offcanvasNavbarLabel">
                            <img src="{{ asset('template/images/logo-icon.svg') }}" alt="">
                        </div>
                        <button type="button" class="close-btn" data-bs-dismiss="offcanvas" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="offcanvas-top-area">
                            <div class="create-bg d-flex gap-2 flex-wrap">
                                @if(session(config('votix_api.session_access_token_key')))
                                    <form method="POST" action="{{ route('logout', ['locale' => $locale ?? 'fr']) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="offcanvas-create-btn border-0 bg-transparent p-0" style="cursor:pointer;">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                            <span>{{ __('Logout') }}</span>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login', ['locale' => $locale ?? 'fr']) }}" class="offcanvas-create-btn">
                                        <i class="fa-solid fa-user"></i>
                                        <span>{{ __('Connexion') }}</span>
                                    </a>
                                @endif
                                <a href="#" class="offcanvas-create-btn">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>{{ __('Create Event') }}</span>
                                </a>
                            </div>
                        </div>
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe_5">
                            <li class="nav-item">
                                <a class="nav-link @if (request()->routeIs('home')) active @endif"
                                    aria-current="page" href="{{ route('home', ['locale' => $locale ?? 'fr']) }}">{{ __('Home') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if (request()->routeIs('ticketing.events*')) active @endif"
                                    href="{{ route('ticketing.events', ['locale' => $locale ?? 'fr']) }}">{{ __('Explore Events') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ __('Pricing') }}</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    {{ __('Help') }}
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#">{{ __('FAQ') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('Help Center') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('contact', ['locale' => $locale ?? 'fr']) }}">{{ __('Contact Us') }}</a></li>
                                </ul>
                            </li>
                            {{-- Langue : dans le menu (desktop + hamburger) --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('Language') }} ({{ strtoupper($locale ?? 'fr') }})
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="{{ url('/locale/fr') }}">{{ __('Français') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/locale/en') }}">{{ __('English') }}</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="offcanvas-footer">
                        <div class="offcanvas-social">
                            <h5>{{ __('Follow Us') }}</h5>
                            <ul class="social-links">
                                <li><a href="#" class="social-link"><i class="fab fa-facebook-square"></i></a>
                                <li><a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                <li><a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                <li><a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                                <li><a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="right-header order-3">
                    <ul class="align-self-stretch">
                        <li class="d-none d-lg-inline-block header-buttons-group">
                            @if(session(config('votix_api.session_access_token_key')))
                                <form method="POST" action="{{ route('logout', ['locale' => $locale ?? 'fr']) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="create-btn btn-hover">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span>{{ __('Logout') }}</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login', ['locale' => $locale ?? 'fr']) }}" class="create-btn btn-hover">
                                    <i class="fa-solid fa-user"></i>
                                    <span>{{ __('Connexion') }}</span>
                                </a>
                            @endif
                            <a href="#" class="create-btn btn-hover">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>{{ __('Create Event') }}</span>
                            </a>
                        </li>
                        <li class="d-none d-lg-inline-block header-theme-sep">
                            <div class="night_mode_switch__btn">
                                <div id="night-mode" class="fas fa-moon fa-sun"></div>
                            </div>
                        </li>
                        <li class="header-theme-sep header-responsive-order-3 d-lg-none">
                            <div class="night_mode_switch__btn">
                                <div id="night-mode" class="fas fa-moon fa-sun"></div>
                            </div>
                        </li>
                        <li class="d-lg-none header-search-trigger-li header-responsive-order-2">
                            <button type="button" class="header-search-trigger btn btn-link p-0" id="headerSearchTrigger" aria-label="Rechercher">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="overlay"></div>
    </div>

    {{-- Overlay recherche (responsive uniquement) --}}
    <div class="search-overlay" id="searchOverlay" aria-hidden="true">
        <div class="search-overlay-backdrop" id="searchOverlayBackdrop"></div>
        <div class="search-overlay-content">
            <div class="search-overlay-header">
                <span class="search-overlay-title">{{ __('Search') }}</span>
                <button type="button" class="search-overlay-close" id="searchOverlayClose" aria-label="Fermer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form action="{{ route('ticketing.events', ['locale' => $locale ?? 'fr']) }}" method="GET" class="search-overlay-form" role="search">
                <div class="search-overlay-inner">
                    <i class="fa-solid fa-search search-overlay-icon"></i>
                    <input type="search" name="q" class="search-overlay-input" placeholder="{{ __('Search placeholder') }}" aria-label="{{ __('Search') }}" autocomplete="off">
                </div>
                <button type="submit" class="main-btn btn-hover w-100 mt-3">{{ __('Search') }}</button>
            </form>
        </div>
    </div>
</header>
<script>
(function() {
    var overlay = document.getElementById('searchOverlay');
    var trigger = document.getElementById('headerSearchTrigger');
    var backdrop = document.getElementById('searchOverlayBackdrop');
    var closeBtn = document.getElementById('searchOverlayClose');
    if (!overlay || !trigger) return;
    function openSearch() {
        overlay.classList.add('search-overlay--open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        var input = overlay.querySelector('.search-overlay-input');
        if (input) { input.focus(); input.value = ''; }
    }
    function closeSearch() {
        overlay.classList.remove('search-overlay--open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
    trigger.addEventListener('click', function(e) { e.preventDefault(); openSearch(); });
    if (backdrop) backdrop.addEventListener('click', closeSearch);
    if (closeBtn) closeBtn.addEventListener('click', closeSearch);
    overlay.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSearch();
    });
})();
</script>

