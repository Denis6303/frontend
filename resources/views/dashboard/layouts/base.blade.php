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

    @if(session('success'))
        <div class="modal fade" id="eventSuccessModal" tabindex="-1" aria-labelledby="eventSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 d-flex align-items-center justify-content-center rounded-circle" style="width:40px;height:40px;background:#e6f4ea;color:#1a7f37;">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ __('Event published') }}</h5>
                                <p class="mb-0 text-muted small">{{ session('success') }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="{{ asset('template/dashboard/js/vertical-responsive-menu.min.js') }}"></script>
    <script src="{{ asset('template/dashboard/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('template/dashboard/js/analytics.js') }}"></script>
    <script src="{{ asset('template/dashboard/js/custom.js') }}"></script>
    <script src="{{ asset('template/dashboard/js/night-mode.js') }}"></script>
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalEl = document.getElementById('eventSuccessModal');
                if (modalEl && window.bootstrap) {
                    var modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            });
        </script>
    @endif
    @stack('scripts')
</body>
</html>
