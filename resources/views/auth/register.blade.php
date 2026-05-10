@extends('layouts.auth')

@section('title', __('Sign up'))

@section('auth-top-link')
    {{ __('Already have an account?') }} <a class="sidebar-register-link" href="{{ route('login', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('Login') }}</a>
@endsection

@section('content')
    <div class="registration">
        <form method="POST" action="{{ route('register', ['locale' => $locale ?? app()->getLocale()]) }}">
            @csrf
            <h2 class="registration-title">{{ __('Create a Votix account') }}</h2>

            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="form-group mt-5">
                <label class="form-label">{{ __('Email') }} *</label>
                <input class="form-control h_50" type="email" name="email" placeholder="{{ __('Your email') }}" value="{{ old('email') }}" required>
            </div>
            <div class="form-group mt-4">
                <div class="field-password">
                    <label class="form-label">{{ __('Password') }} *</label>
                </div>
                <div class="loc-group position-relative">
                    <input class="form-control h_50" type="password" name="password" placeholder="{{ __('Password') }}" required minlength="8" id="password-field">
                    <span class="pass-show-eye" id="toggle-password" role="button" aria-label="{{ __('Show password') }}"><i class="fas fa-eye-slash"></i></span>
                </div>
            </div>
            <button class="main-btn btn-hover w-100 mt-4" type="submit">{{ __('Sign up') }}</button>
        </form>
        <div class="agree-text">
            {{ __('By clicking "Sign up", you accept Votix Terms & Conditions and Privacy Policy.') }}
        </div>
        <div class="divider">
            <span>{{ __('or') }}</span>
        </div>
        <div class="social-btns-list mb-lg-5">
            <button type="button" class="social-login-btn">
                <svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 26.488 27.029"><g transform="translate(-0.126)"><path d="M1258.806,1021.475a11.578,11.578,0,0,0-.285-2.763h-12.688v5.015h7.448a6.605,6.605,0,0,1-2.763,4.384l-.025.168,4.012,3.108.278.028a13.214,13.214,0,0,0,4.024-9.941" transform="translate(-1232.192 -1007.66)" fill="#4285f4"></path><path d="M145.071,1502.921a12.881,12.881,0,0,0,8.949-3.273l-4.265-3.3a8,8,0,0,1-4.685,1.352,8.136,8.136,0,0,1-7.688-5.616l-.158.013-4.172,3.229-.055.152a13.5,13.5,0,0,0,12.073,7.448" transform="translate(-131.431 -1475.893)" fill="#34a853"></path><path d="M5.952,689.263a8.32,8.32,0,0,1-.45-2.673,8.744,8.744,0,0,1,.435-2.673l-.008-.179-4.224-3.28-.138.066a13.486,13.486,0,0,0,0,12.133l4.385-3.393" transform="translate(0 -673.076)" fill="#fbbc05"></path><path d="M145.071,5.225A7.49,7.49,0,0,1,150.3,7.238l3.814-3.724A12.984,12.984,0,0,0,145.071,0,13.5,13.5,0,0,0,133,7.448l4.37,3.394a8.169,8.169,0,0,1,7.7-5.616" transform="translate(-131.431)" fill="#eb4335"></path></g></svg>
                {{ __('Sign up with Google') }}
            </button>
        </div>
        <div class="new-sign-link">
            {{ __('Already have an account?') }} <a class="signup-link" href="{{ route('login', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('Login') }}</a>
        </div>
    </div>

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
@endsection
