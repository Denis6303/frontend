@extends('layouts.auth')

@section('title', __('Reset Password'))

@section('auth-top-link')
    <a class="sidebar-register-link" href="{{ route('login', ['locale' => $locale ?? app()->getLocale()]) }}"><i class="fa-regular fa-circle-left me-2"></i>{{ __('Back to login') }}</a>
@endsection

@section('content')
    <div class="registration">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (! $token || ! $email)
            <p class="text-muted">{{ __('This password reset link is invalid or has expired.') }}</p>
            <a class="main-btn btn-hover mt-3" href="{{ route('password.request', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('Request a new link') }}</a>
        @else
            <form method="POST" action="{{ route('password.update', ['locale' => $locale ?? app()->getLocale()]) }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <h2 class="registration-title">{{ __('Reset Password') }}</h2>
                <p class="text-muted mt-3">{{ __('Enter your new password below.') }}</p>

                <div class="form-group mt-4">
                    <label class="form-label">{{ __('Password') }} *</label>
                    <div class="loc-group position-relative">
                        <input class="form-control h_50" type="password" name="password" placeholder="{{ __('New password') }}" required minlength="8" id="password-field">
                        <span class="pass-show-eye" id="toggle-password" role="button" aria-label="Afficher le mot de passe"><i class="fas fa-eye-slash"></i></span>
                    </div>
                </div>
                <div class="form-group mt-4">
                    <label class="form-label">{{ __('Confirm Password') }} *</label>
                    <input class="form-control h_50" type="password" name="password_confirmation" placeholder="{{ __('Confirm password') }}" required minlength="8">
                </div>
                <button class="main-btn btn-hover w-100 mt-4" type="submit">{{ __('Reset password') }}</button>
            </form>
        @endif

        <div class="new-sign-link mt-4">
            <a class="signup-link" href="{{ route('login', ['locale' => $locale ?? app()->getLocale()]) }}"><i class="fa-regular fa-circle-left me-2"></i>{{ __('Back to login') }}</a>
        </div>
    </div>

    @if ($token && $email)
    @push('scripts')
    <script>
        document.getElementById('toggle-password')?.addEventListener('click', function() {
            var field = document.getElementById('password-field');
            var icon = this.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });
    </script>
    @endpush
    @endif
@endsection
