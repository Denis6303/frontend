<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=9">
    <meta name="description" content="Votix - Event ticketing platform">
    <meta name="author" content="Votix">
    <title>@yield('title', __('Dashboard')) - {{ config('app.name', 'Votix') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/fav.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body,
        .nav-link,
        .btn,
        .main-btn,
        .create-btn,
        .dropdown-item,
        input,
        textarea,
        .form-control {
            font-family: 'DM Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }
    </style>
    <link href="{{ asset('vendor/unicons-2.0.1/css/unicons.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/vertical-responsive-menu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/analytics.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/night-mode.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/OwlCarousel/assets/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/OwlCarousel/assets/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.css" rel="stylesheet" crossorigin="anonymous">

    {{-- Force the same font as the public site (dashboard CSS sets Roboto with !important) --}}
    <style>
        html,
        body,
        a,
        button,
        input,
        textarea,
        select,
        label,
        p,
        span,
        div,
        li,
        ul,
        ol,
        small,
        strong,
        em,
        h1, h2, h3, h4, h5, h6 {
            font-family: 'DM Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column h-100">
    @include('dashboard.partials.header')
    @include('dashboard.partials.sidebar')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            @yield('content')
        </div>
    </div>

    @include('partials.votix-feedback-modal')

    <script src="{{ asset('dashboard/js/vertical-responsive-menu.min.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var nav = document.querySelector('.vertical_nav');
        var wrapper = document.querySelector('.wrapper');
        var overlay = document.querySelector('.header .overlay');
        var toggleMenu = document.getElementById('toggleMenu');

        function closeMobileNav() {
            if (!nav || !wrapper) return;
            nav.classList.remove('vertical_nav__opened');
            wrapper.classList.remove('toggle-content');
            if (overlay) overlay.style.display = 'none';
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                closeMobileNav();
            });
        }

        document.addEventListener('click', function (e) {
            if (window.matchMedia('(min-width: 992px)').matches) return;
            if (!nav || !nav.classList.contains('vertical_nav__opened')) return;
            if (nav.contains(e.target) || (toggleMenu && toggleMenu.contains(e.target))) return;
            closeMobileNav();
        });

        if (nav && overlay) {
            var syncOverlay = function () {
                if (window.matchMedia('(min-width: 992px)').matches) {
                    overlay.style.display = 'none';
                    return;
                }
                overlay.style.display = nav.classList.contains('vertical_nav__opened') ? 'block' : 'none';
            };
            new MutationObserver(syncOverlay).observe(nav, { attributes: true, attributeFilter: ['class'] });
            syncOverlay();
        }

        window.addEventListener('resize', function () {
            if (window.matchMedia('(min-width: 992px)').matches) {
                closeMobileNav();
            }
        });

        document.querySelectorAll('#js-menu a[href]').forEach(function (a) {
            var href = (a.getAttribute('href') || '').trim();
            if (!href || href === '#') return;
            a.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 991.98px)').matches) {
                    closeMobileNav();
                }
            });
        });
    });
    </script>
    <script src="{{ asset('dashboard/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('dashboard/js/analytics.js') }}"></script>
    <script src="{{ asset('js/votix-feedback.js') }}"></script>
    <script src="{{ asset('dashboard/js/custom.js') }}"></script>
    @stack('scripts')
</body>
</html>
