<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=9">
    <meta name="description" content="Votix - Event ticketing platform">
    <meta name="author" content="Votix">

    <title>@yield('title', __('Authentication')) - {{ config('app.name', 'Votix') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/fav.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="{{ asset('vendor/unicons-2.0.1/css/unicons.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/night-mode.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/OwlCarousel/assets/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/OwlCarousel/assets/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <style>
        .app-top-items { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; width: 100%; }
        .app-top-left-link { flex-shrink: 0; }
        .app-top-left-link .sidebar-register-link { color: #000; font-weight: 500; }
        .app-top-left-link .sidebar-register-link:hover { color: #000 !important; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="form-wrapper">
        <div class="app-form">
            <div class="app-form-sidebar">
                <div class="sidebar-sign-logo">
                    <img src="{{ asset('images/logos/white.png') }}" alt="Votix">
                </div>
                <div class="sign_sidebar_text">
                    <h1>{{ __('The easiest way to create events and sell more tickets online') }}</h1>
                </div>
            </div>
            <div class="app-form-content">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-10 col-md-10">
                            <div class="app-top-items">
                                <div class="app-top-left-link">
                                    <a class="sidebar-register-link" href="{{ route('home', ['locale' => $locale ?? app()->getLocale()]) }}"><i class="fa-regular fa-circle-left me-2"></i>{{ __('Back to home') }}</a>
                                </div>
                                <div class="app-top-right-link">
                                    @yield('auth-top-link')
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-6 col-md-7">
                            @yield('content')
                        </div>
                    </div>
                </div>
                <div class="copyright-footer">
                    © {{ date('Y') }}, {{ config('app.name', 'Votix') }}. {{ __('All rights reserved.') }}
                </div>
            </div>
        </div>
    </div>

    @include('partials.votix-feedback-modal')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="{{ asset('js/votix-feedback.js') }}"></script>
    @stack('scripts')
</body>
</html>
