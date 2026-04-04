<header class="header">
    <div class="header-inner">
        <nav class="navbar navbar-expand-lg bg-barren barren-head navbar fixed-top justify-content-sm-start pt-0 pb-0 ps-lg-0 pe-2">
            <div class="container-fluid ps-0">
                <button type="button" id="toggleMenu" class="toggle_menu">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <button id="collapse_menu" class="collapse_menu me-4">
                    <i class="fa-solid fa-bars collapse_menu--icon"></i>
                    <span class="collapse_menu--label"></span>
                </button>
                <a class="navbar-brand order-1 order-lg-0 ml-lg-0 ml-2 me-auto" href="{{ route('home', ['locale' => $locale ?? app()->getLocale()]) }}">
                    <div class="res-main-logo">
                        <img src="{{ asset('images/logos/black.jpeg') }}" alt="Votix">
                    </div>
                    <div class="main-logo" id="logo">
                        <img src="{{ asset('images/logos/black.jpeg') }}" alt="Votix">
                    </div>
                </a>
                <div class="right-header order-2">
                    <ul class="align-self-stretch">
                        <li>
                            <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale()]) }}" class="create-btn btn-hover">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>{{ __('Create Event') }}</span>
                            </a>
                        </li>
                        <li class="dropdown account-dropdown order-3">
                            <a href="#" class="account-link" role="button" id="accountClick" data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset('images/profile-imgs/img-13.jpg') }}" alt="">
                                <i class="fas fa-caret-down arrow-icon"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-account dropdown-menu-end" aria-labelledby="accountClick">
                                <li>
                                    <div class="dropdown-account-header">
                                        <h5>{{ auth_user_display_name() }}</h5>
                                        @if (auth_user_email())
                                        <p>{{ auth_user_email() }}</p>
                                        @else
                                        <p class="text-muted small">—</p>
                                        @endif
                                    </div>
                                </li>
                                <li class="profile-link">
                                    <!-- <a href="#" class="link-item">{{ __('My Profile') }}</a> -->
                                    <form method="POST" action="{{ route('logout', ['locale' => $locale ?? app()->getLocale()]) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="link-item border-0 bg-transparent p-0">{{ __('Sign Out') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <!-- <li>
                            <div class="night_mode_switch__btn">
                                <div id="night-mode" class="fas fa-moon fa-sun"></div>
                            </div>
                        </li> -->
                    </ul>
                </div>
            </div>
        </nav>
        <div class="overlay"></div>
    </div>
</header>

