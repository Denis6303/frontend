<header class="header">
    <div class="header-inner">
        <nav class="navbar navbar-expand-lg bg-barren barren-head navbar fixed-top justify-content-sm-start pt-0 pb-0">
            <div class="container">

                {{-- Hamburger (mobile) --}}
                <button class="navbar-toggler" type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar"
                    aria-controls="offcanvasNavbar">
                    <span class="navbar-toggler-icon">
                        <i class="fa-solid fa-bars"></i>
                    </span>
                </button>

                {{-- Logo --}}
                <a class="navbar-brand order-1 order-lg-0 ml-lg-0 ml-2 me-auto"
                   href="{{ route('home', ['locale' => $locale ?? 'fr']) }}">
                    <div class="res-main-logo">
                        <img src="{{ asset('images/logos/black.jpeg') }}" alt="Votix">
                    </div>
                    <div class="main-logo" id="logo">
                        <img src="{{ asset('images/logos/black.jpeg') }}" alt="Votix">
                    </div>
                </a>

                {{-- Offcanvas – menu hamburger mobile --}}
                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
                     aria-labelledby="offcanvasNavbarLabel">

                    <div class="offcanvas-header">
                        <div class="offcanvas-logo" id="offcanvasNavbarLabel">
                            <img src="{{ asset('images/logos/black.jpeg') }}" alt="Votix">
                        </div>
                        <button type="button" class="close-btn" data-bs-dismiss="offcanvas" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="offcanvas-body">

                        {{-- Créer un événement – en premier dans le hamburger --}}
                        <div class="offcanvas-create-event-wrap">
                            <a href="{{ session(config('votix_api.session_access_token_key'))
                                ? route('dashboard.events.draft.create.step1', ['locale' => $locale ?? 'fr'])
                                : route('login', ['locale' => $locale ?? 'fr', 'redirect' => route('dashboard.events.draft.create.step1', ['locale' => $locale ?? 'fr'])]) }}"
                               class="offcanvas-create-btn offcanvas-create-btn--full">
                                <i class="fa-solid fa-calendar-days pt-1"></i>
                                <span>{{ __('Create Event') }}</span>
                            </a>
                        </div>

                        {{-- Bloc compte utilisateur (mobile) --}}
                        <div class="offcanvas-user-block px-3">
                            @if(session(config('votix_api.session_access_token_key')))
                                <div class="offcanvas-user-info">
                                    <span class="offcanvas-user-avatar">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                </div>
                                <div class="offcanvas-user-actions">
                                    <a href="{{ route('dashboard.home', ['locale' => $locale ?? 'fr']) }}" class="offcanvas-user-link">
                                        <i class="fa-solid fa-gauge-high"></i> {{ __('Dashboard') }}
                                    </a>
                                    <a href="{{ route('dashboard.tickets.index', ['locale' => $locale ?? 'fr']) }}" class="offcanvas-user-link">
                                        <i class="fa-solid fa-ticket"></i> {{ __('My Tickets') }}
                                    </a>
                                    <a href="{{ route('dashboard.account', ['locale' => $locale ?? 'fr']) }}" class="offcanvas-user-link">
                                        <i class="fa-solid fa-gear"></i> {{ __('Settings') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout', ['locale' => $locale ?? 'fr']) }}">
                                        @csrf
                                        <button type="submit" class="offcanvas-user-link offcanvas-user-link--danger">
                                            <i class="fa-solid fa-right-from-bracket"></i> {{ __('Logout') }}
                                        </button>
                                    </form>
                                </div>
                            @else
                            <div class="offcanvas-auth-btns d-flex justify-content-center gap-2">
                                <a href="{{ route('login', ['locale' => $locale ?? 'fr']) }}" 
                                    class="offcanvas-create-btn d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                    <span>{{ __('Login') }}</span>
                                </a>
                                <a href="{{ route('register', ['locale' => $locale ?? 'fr']) }}" 
                                    class="offcanvas-create-btn offcanvas-create-btn--outline d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>{{ __('Register') }}</span>
                                </a>
                            </div>
                            @endif
                        </div>

                        {{-- Navigation principale --}}
                        <ul class="navbar-nav justify-content-end flex-grow-1 px-2">
                            <li class="nav-item">
                                <a class="nav-link @if (request()->routeIs('home')) active @endif"
                                   aria-current="page"
                                   href="{{ route('home', ['locale' => $locale ?? 'fr']) }}">
                                    <i class="fa-solid fa-house me-2"></i>{{ __('Home') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if (request()->routeIs('ticketing.events*')) active @endif"
                                   href="{{ route('ticketing.events', ['locale' => $locale ?? 'fr']) }}">
                                    <i class="fa-solid fa-calendar-days me-2"></i>{{ __('Events') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fa-solid fa-tags me-2"></i>{{ __('Pricing') }}
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-circle-question me-2"></i>{{ __('Help') }}
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fa-solid fa-circle-question me-2"></i>{{ __('FAQ') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fa-solid fa-life-ring me-2"></i>{{ __('Help Center') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('contact', ['locale' => $locale ?? 'fr']) }}">
                                            <i class="fa-solid fa-envelope me-2"></i>{{ __('Contact Us') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ strtoupper($locale ?? 'fr') }}
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li>
                                        <a class="dropdown-item" href="{{ url('/locale/fr') }}">
                                            <span class="dropdown-item-icon dropdown-flag" aria-hidden="true">
                                                <img class="dropdown-flag-img" src="{{ asset('flags/fr.svg') }}" alt="FR">
                                            </span>
                                            <span class="dropdown-item-text">{{ __('Français') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ url('/locale/en') }}">
                                            <span class="dropdown-item-icon dropdown-flag" aria-hidden="true">
                                                <img class="dropdown-flag-img" src="{{ asset('flags/gb.svg') }}" alt="EN">
                                            </span>
                                            <span class="dropdown-item-text">{{ __('English') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Right header – desktop --}}
                <div class="right-header order-2">
                    <ul class="align-self-stretch">

                        {{-- Langue (desktop) – FR / ENG + chevron, avant Créer un événement --}}
                        <li class="d-none d-lg-inline-block header-theme-sep dropdown header-lang-li">
                            <button class="header-lang-toggle border-0 bg-transparent"
                                    type="button"
                                    id="headerLangToggle"
                                    data-bs-toggle="dropdown"
                                    data-bs-auto-close="true"
                                    aria-expanded="false">
                                <span class="header-lang-label">{{ $locale === 'en' ? 'ENG' : 'FR' }}</span>
                                <i class="fa-solid fa-chevron-down header-lang-chevron"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end header-lang-menu" id="headerLangMenu">
                                <li>
                                    <a class="dropdown-item" href="{{ url('/locale/fr') }}">
                                        <span class="dropdown-item-icon dropdown-flag" aria-hidden="true">
                                            <img class="dropdown-flag-img" src="{{ asset('flags/fr.svg') }}" alt="FR">
                                        </span>
                                        <span class="dropdown-item-text">{{ __('Français') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ url('/locale/en') }}">
                                        <span class="dropdown-item-icon dropdown-flag" aria-hidden="true">
                                            <img class="dropdown-flag-img" src="{{ asset('flags/gb.svg') }}" alt="EN">
                                        </span>
                                        <span class="dropdown-item-text">{{ __('English') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="d-none d-lg-inline-block header-theme-sep">
                            <a href="{{ session(config('votix_api.session_access_token_key'))
                                ? route('dashboard.events.draft.create.step1', ['locale' => $locale ?? 'fr'])
                                : route('login', ['locale' => $locale ?? 'fr', 'redirect' => route('dashboard.events.draft.create.step1', ['locale' => $locale ?? 'fr'])]) }}"
                               class="create-btn btn-hover">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>{{ __('Create Event') }}</span>
                            </a>
                        </li>

                        {{-- Dropdown compte (desktop) --}}
                        <li class="d-none d-lg-inline-flex align-items-center dropdown header-user-li"
                            id="headerUserDropdownWrap">

                            <button class="header-user-btn"
                                    type="button"
                                    id="headerUserToggle"
                                    data-bs-toggle="dropdown"
                                    data-bs-auto-close="true"
                                    aria-expanded="false">
                                <span class="header-user-avatar @unless(session(config('votix_api.session_access_token_key'))) header-user-avatar--guest @endunless">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <i class="fa-solid fa-chevron-down header-user-chevron"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end header-user-menu"
                                id="headerUserDropdownMenu">
                                @if(session(config('votix_api.session_access_token_key')))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard.home', ['locale' => $locale ?? 'fr']) }}">
                                            <span class="dropdown-item-icon" aria-hidden="true">
                                                <i class="fa-solid fa-gauge-high"></i>
                                            </span>
                                            <span class="dropdown-item-text">{{ __('Dashboard') }}</span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard.tickets.index', ['locale' => $locale ?? 'fr']) }}">
                                            <span class="dropdown-item-icon" aria-hidden="true">
                                                <i class="fa-solid fa-ticket"></i>
                                            </span>
                                            <span class="dropdown-item-text">{{ __('My Tickets') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard.account', ['locale' => $locale ?? 'fr']) }}">
                                            <span class="dropdown-item-icon" aria-hidden="true">
                                                <i class="fa-solid fa-gear"></i>
                                            </span>
                                            <span class="dropdown-item-text">{{ __('Settings') }}</span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST"
                                              action="{{ route('logout', ['locale' => $locale ?? 'fr']) }}"
                                              class="dropdown-item-form">
                                            @csrf
                                            <button type="submit"
                                                class="dropdown-item dropdown-item--danger w-100 border-0 bg-transparent">
                                                <span class="dropdown-item-icon" aria-hidden="true">
                                                    <i class="fa-solid fa-right-from-bracket"></i>
                                                </span>
                                                <span class="dropdown-item-text">{{ __('Logout') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item" href="{{ route('login', ['locale' => $locale ?? 'fr']) }}">
                                            <span class="dropdown-item-icon" aria-hidden="true">
                                                <i class="fa-solid fa-right-to-bracket"></i>
                                            </span>
                                            <span class="dropdown-item-text">{{ __('Login') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('register', ['locale' => $locale ?? 'fr']) }}">
                                            <span class="dropdown-item-icon" aria-hidden="true">
                                                <i class="fa-solid fa-user-plus"></i>
                                            </span>
                                            <span class="dropdown-item-text">{{ __('Register') }}</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Thème clair/sombre – dernier sur le header desktop --}}
                        <!-- <li class="d-none d-lg-inline-block header-theme-sep">
                            <div class="night_mode_switch__btn">
                                <div id="night-mode" class="fas fa-moon fa-sun"></div>
                            </div>
                        </li>

                        <li class="header-theme-sep header-responsive-order-3 d-lg-none">
                            <div class="night_mode_switch__btn">
                                <div class="fas fa-moon fa-sun"></div>
                            </div>
                        </li> -->

                        <li class="d-lg-none header-search-trigger-li header-responsive-order-2">
                            <button type="button" class="header-search-trigger btn btn-link p-0"
                                    id="headerSearchTrigger" aria-label="{{ __('Search') }}">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </li>

                    </ul>
                </div>

            </div>
        </nav>
        <div class="overlay"></div>
    </div>

    {{-- Search overlay (mobile) --}}
    <div class="search-overlay" id="searchOverlay" aria-hidden="true">
        <div class="search-overlay-backdrop" id="searchOverlayBackdrop"></div>
        <div class="search-overlay-content">
            <div class="search-overlay-header">
                <span class="search-overlay-title">{{ __('Search') }}</span>
                <button type="button" class="search-overlay-close" id="searchOverlayClose" aria-label="{{ __('Close') }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            @php
                $headerLocale = $locale ?? 'fr';
                $overlayLiveOnListing = request()->routeIs('home', 'ticketing.index', 'ticketing.events');
            @endphp
            <form action="{{ route('home', ['locale' => $headerLocale]) }}" method="GET"
                  class="search-overlay-form vtx-overlay-live-search" role="search"
                  data-overlay-live="{{ $overlayLiveOnListing ? '1' : '0' }}"
                  data-search-fallback="{{ route('home', ['locale' => $headerLocale]) }}">
                <div class="search-overlay-inner">
                    <i class="fa-solid fa-search search-overlay-icon"></i>
                    <input type="search" name="query" class="search-overlay-input"
                           placeholder="{{ __('Search placeholder') }}" aria-label="{{ __('Search') }}" autocomplete="off"
                           value="{{ request('query', request('q')) }}">
                </div>
            </form>
        </div>
    </div>
</header>

<script>
(function () {
    var overlay  = document.getElementById('searchOverlay');
    var trigger  = document.getElementById('headerSearchTrigger');
    var backdrop = document.getElementById('searchOverlayBackdrop');
    var closeBtn = document.getElementById('searchOverlayClose');

    if (overlay && trigger) {
        var overlayForm = overlay.querySelector('.search-overlay-form');
        var searchInput = overlayForm ? overlayForm.querySelector('.search-overlay-input') : null;
        var lastOverlayQuery = searchInput ? String(searchInput.value || '').trim() : '';

        function debounce(fn, wait) {
            var t;
            return function () {
                var ctx = this, args = arguments;
                clearTimeout(t);
                t = setTimeout(function () { fn.apply(ctx, args); }, wait);
            };
        }

        function buildOverlaySearchHref() {
            if (!overlayForm || !searchInput) return '';
            var v = String(searchInput.value || '').trim();
            var live = overlayForm.getAttribute('data-overlay-live') === '1';
            var u = live
                ? new URL(window.location.href)
                : new URL(overlayForm.getAttribute('data-search-fallback'), window.location.origin);
            if (!live) {
                var cur = new URL(window.location.href);
                var cat = cur.searchParams.get('category_id');
                if (cat) {
                    u.searchParams.set('category_id', cat);
                } else {
                    u.searchParams.delete('category_id');
                }
            }
            if (v) {
                u.searchParams.set('query', v);
            } else {
                u.searchParams.delete('query');
                u.searchParams.delete('q');
            }
            u.searchParams.delete('page');
            var qs = u.searchParams.toString();
            return u.pathname + (qs ? '?' + qs : '') + u.hash;
        }

        function navigateOverlaySearch() {
            var href = buildOverlaySearchHref();
            if (href) {
                window.location.href = href;
            }
        }

        var debouncedOverlaySearch = debounce(function () {
            var v = String(searchInput.value || '').trim();
            if (v === lastOverlayQuery) {
                return;
            }
            lastOverlayQuery = v;
            navigateOverlaySearch();
        }, 350);

        function openSearch() {
            overlay.classList.add('search-overlay--open');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            if (searchInput) {
                lastOverlayQuery = String(searchInput.value || '').trim();
                searchInput.focus();
            }
        }
        function closeSearch() {
            overlay.classList.remove('search-overlay--open');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
        trigger.addEventListener('click', function (e) { e.preventDefault(); openSearch(); });
        if (backdrop) backdrop.addEventListener('click', closeSearch);
        if (closeBtn)  closeBtn.addEventListener('click', closeSearch);
        overlay.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeSearch(); });

        if (overlayForm && searchInput) {
            searchInput.addEventListener('input', debouncedOverlaySearch);
            overlayForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var v = String(searchInput.value || '').trim();
                if (v === lastOverlayQuery) {
                    closeSearch();
                    return;
                }
                lastOverlayQuery = v;
                navigateOverlaySearch();
            });
        }
    }

    var userToggle = document.getElementById('headerUserToggle');
    if (userToggle) {
        document.addEventListener('shown.bs.dropdown',  function (e) {
            if (e.target === userToggle) userToggle.classList.add('is-open');
        });
        document.addEventListener('hidden.bs.dropdown', function (e) {
            if (e.target === userToggle) userToggle.classList.remove('is-open');
        });
    }

    var offcanvas = document.getElementById('offcanvasNavbar');
    var headerEl = document.querySelector('.header');
    if (offcanvas && headerEl) {
        offcanvas.addEventListener('shown.bs.offcanvas', function () {
            headerEl.classList.add('offcanvas-open');
        });
        offcanvas.addEventListener('hidden.bs.offcanvas', function () {
            headerEl.classList.remove('offcanvas-open');
        });
    }
})();
</script>

