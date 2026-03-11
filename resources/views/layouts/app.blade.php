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
        <link rel="icon" type="image/png" href="{{ asset('template/images/fav.png') }}">

        {{-- Fonts et styles du template --}}
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
        <link href="{{ asset('template/vendor/unicons-2.0.1/css/unicons.css') }}" rel="stylesheet">
        <link href="{{ asset('template/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('template/css/responsive.css') }}" rel="stylesheet">
        <link href="{{ asset('template/css/night-mode.css') }}" rel="stylesheet">

        {{-- Vendor styles --}}
        <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
        <link href="{{ asset('template/vendor/OwlCarousel/assets/owl.carousel.css') }}" rel="stylesheet">
        <link href="{{ asset('template/vendor/OwlCarousel/assets/owl.theme.default.min.css') }}" rel="stylesheet">
        <link href="{{ asset('template/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('template/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">

        {{-- Styles globaux (Laravel/Vite) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="d-flex flex-column h-100">
        {{-- Header global du site (sera remplacé par le header du template) --}}
        @includeIf('partials.header')

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

        {{-- Footer global du site (sera remplacé par le footer du template) --}}
        @includeIf('partials.footer')

        {{-- Bootstrap 5 JS (offcanvas, dropdown, etc.) pour le responsive --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        {{-- Scripts spécifiques au template ou aux pages --}}
        @stack('scripts')
    </body>
</html>

