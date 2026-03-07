@extends('layouts.auth')

@section('title', __('Forgot Password'))

@section('auth-top-link')
    <a class="sidebar-register-link" href="{{ route('login', ['locale' => $locale ?? app()->getLocale()]) }}"><i class="fa-regular fa-circle-left me-2"></i>{{ __('Retour à la connexion') }}</a>
@endsection

@section('content')
    <div class="registration">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email', ['locale' => $locale ?? app()->getLocale()]) }}">
            @csrf
            <h2 class="registration-title">{{ __('Forgot Password') }}</h2>
            <p class="text-muted mt-3">{{ __('Forgot password instruction') }}</p>
            <div class="form-group mt-5">
                <label class="form-label">{{ __('Your email') }} *</label>
                <input class="form-control h_50" type="email" name="email" placeholder="{{ __('Your email') }}" value="{{ old('email') }}" required autofocus>
            </div>
            <button class="main-btn btn-hover w-100 mt-4" type="submit">{{ __('Send link') }}</button>
        </form>
        <div class="new-sign-link">
            <a class="signup-link" href="{{ route('login', ['locale' => $locale ?? app()->getLocale()]) }}"><i class="fa-regular fa-circle-left me-2"></i>{{ __('Retour à la connexion') }}</a>
        </div>
    </div>
@endsection
