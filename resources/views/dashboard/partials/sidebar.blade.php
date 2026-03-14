<nav class="vertical_nav">
    <div class="left_section menu_left" id="js-menu">
        <div class="left_section">
            <ul>
                <li class="menu--item">
                    <a href="{{ route('dashboard.index', ['locale' => $locale ?? app()->getLocale()]) }}" class="menu--link active" title="{{ __('Dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-gauge menu--icon"></i>
                        <span class="menu--label">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('Events') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-calendar-days menu--icon"></i>
                        <span class="menu--label">{{ __('Events') }}</span>
                    </a>
                </li>
                <li class="menu--item">
                    <a href="#" class="menu--link" title="{{ __('Promotion') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa-solid fa-rectangle-ad menu--icon"></i>
                        <span class="menu--label">{{ __('Promotion') }}</span>
                    </a>
                </li>
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
            </ul>
        </div>
    </div>
</nav>
