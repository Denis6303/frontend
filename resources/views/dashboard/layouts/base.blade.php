<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=9">
    <meta name="description" content="Votix - Billetterie évènementielle">
    <meta name="author" content="Votix">
    <title>@yield('title', __('Dashboard')) - {{ config('app.name', 'Votix') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('template/images/fav.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <style>body,.nav-link,.btn,.main-btn,.create-btn,.dropdown-item,input,textarea,.form-control{font-family:'Raleway',sans-serif!important}</style>
    <link href="{{ asset('template/vendor/unicons-2.0.1/css/unicons.css') }}" rel="stylesheet">
    <link href="{{ asset('template/dashboard/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('template/dashboard/css/vertical-responsive-menu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/dashboard/css/analytics.css') }}" rel="stylesheet">
    <link href="{{ asset('template/dashboard/css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('template/dashboard/css/night-mode.css') }}" rel="stylesheet">

    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/vendor/OwlCarousel/assets/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('template/vendor/OwlCarousel/assets/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.css" rel="stylesheet" crossorigin="anonymous">

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

    <script src="{{ asset('template/dashboard/js/vertical-responsive-menu.min.js') }}"></script>
    <script src="{{ asset('template/dashboard/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('template/dashboard/js/analytics.js') }}"></script>
    <script src="{{ asset('template/dashboard/js/custom.js') }}"></script>
    <script src="{{ asset('template/dashboard/js/night-mode.js') }}"></script>
    @stack('scripts')
</body>
</html>
