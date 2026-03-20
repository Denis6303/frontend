<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, shrink-to-fit=9">
        <meta name="description" content="Votix - Billetterie évènementielle">
        <meta name="author" content="Votix">

        <title>@yield('title', config('app.name', 'Votix'))</title>

        {{-- Favicon --}}
        <link rel="icon" type="image/png" href="{{ asset('images/fav.png') }}">

        {{-- Fonts et styles du template --}}
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
        <link href="{{ asset('vendor/unicons-2.0.1/css/unicons.css') }}" rel="stylesheet">
        <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('dashboard/css/responsive.css') }}" rel="stylesheet">
        <link href="{{ asset('dashboard/css/night-mode.css') }}" rel="stylesheet">

        {{-- Vendor styles --}}
        <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/OwlCarousel/assets/owl.carousel.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/OwlCarousel/assets/owl.theme.default.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">

        {{-- Styles globaux --}}
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        @vite(['resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="d-flex flex-column h-100">
        <main>
            @if (session('success'))
                <div class="container mt-3">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        @stack('scripts')
    </body>
</html>

