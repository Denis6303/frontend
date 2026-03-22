@php
    $dashLocale = $locale ?? app()->getLocale();
@endphp
<nav class="vertical_nav">
    <div class="left_section menu_left" id="js-menu">
        <div class="left_section">
            <ul>
                {{-- Même bouton que le header (desktop) --}}
                <li class="menu--item menu-sidebar-create-wrap d-lg-none">
                    <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $dashLocale]) }}"
                       class="create-btn btn-hover menu-sidebar-create-btn">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>{{ __('Create Event') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="{{ route('dashboard.home', ['locale' => $dashLocale]) }}"
                       class="menu--link {{ request()->routeIs('dashboard.home') ? 'active' : '' }}"
                       title="{{ __('Dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-gauge menu--icon"></i>
                        <span class="menu--label">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="{{ route('dashboard.events.index', ['locale' => $dashLocale]) }}"
                       class="menu--link {{ request()->routeIs('dashboard.events.*') ? 'active' : '' }}"
                       title="{{ __('Events') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-calendar-days menu--icon"></i>
                        <span class="menu--label">{{ __('Events') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('Tickets') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-ticket menu--icon"></i>
                        <span class="menu--label">{{ __('Tickets') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="{{ route('dashboard.account', ['locale' => $dashLocale]) }}"
                       class="menu--link {{ request()->routeIs('dashboard.account') ? 'active' : '' }}"
                       title="{{ __('About') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-user menu--icon"></i>
                        <span class="menu--label">{{ __('My Account') }}</span>
                    </a>
                </li>
                <li class="menu--item menu-sidebar-divider" aria-hidden="true"><span class="menu-sidebar-divider-line"></span></li>
                <li class="menu--item">
                    <a href="{{ route('home', ['locale' => $dashLocale]) }}"
                       class="menu--link {{ request()->routeIs('home') ? 'active' : '' }}"
                       title="{{ __('Home') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-right-left menu--icon"></i>
                        <span class="menu--label">{{ __('Home') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="{{ route('ticketing.events', ['locale' => $dashLocale]) }}"
                       class="menu--link {{ request()->routeIs('ticketing.events') ? 'active' : '' }}"
                       title="{{ __('Explore Events') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-compass menu--icon"></i>
                        <span class="menu--label">{{ __('Explore Events') }}</span>
                    </a>
                </li>
                 <!--
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('Contact List') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-regular fa-address-card menu--icon"></i>
                        <span class="menu--label">{{ __('Contact List') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('Payouts') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-credit-card menu--icon"></i>
                        <span class="menu--label">{{ __('Payouts') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('Reports') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-chart-pie menu--icon"></i>
                        <span class="menu--label">{{ __('Reports') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('Subscription') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-bahai menu--icon"></i>
                        <span class="menu--label">{{ __('Subscription') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('Conversion Setup') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-square-plus menu--icon"></i>
                        <span class="menu--label">{{ __('Conversion Setup') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('About') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-circle-info menu--icon"></i>
                        <span class="menu--label">{{ __('About') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link team-lock" title="{{ __('My Team') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-user-group menu--icon"></i>
                        <span class="menu--label">{{ __('My Team') }}</span>
                    </a>
                </li>
                -->
            </ul>
            <div class="menu-sidebar-extras menu-sidebar-mobile-only">
                <p class="menu-social-title">{{ __('Follow Us') }}</p>
                <ul class="menu-social-links">
                    <li><a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-square"></i></a></li>
                    <li><a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="#" class="social-link" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></li>
                    <li><a href="#" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
