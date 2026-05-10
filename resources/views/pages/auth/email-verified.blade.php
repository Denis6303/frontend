@extends('layouts.auth')

@section('title', __('Email verified'))

@section('auth-top-link')
    <a class="sidebar-register-link" href="{{ route('login', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('Login') }}</a>
@endsection

@section('content')
    <div class="registration">
        @if (!empty($verified))
            <div class="alert alert-success">
                <h2 class="registration-title mb-3">{{ __('Email verified') }}</h2>
                <p class="mb-0">{{ __('Your email has been verified. You can now use all features.') }}</p>
            </div>
            <a class="main-btn btn-hover w-100 mt-4 d-inline-block text-center" href="{{ route('home', ['locale' => $locale ?? app()->getLocale()]) }}">
                {{ __('Go to home') }}
            </a>
        @else
            <h2 class="registration-title">{{ __('Email verification') }}</h2>
            <p class="text-muted mt-3">
                {{ $errorMessage ?? __('This link may have expired or already been used.') }}
            </p>
            <p class="mt-3">
                <a class="sidebar-register-link" href="{{ route('login', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('Back to login') }}</a>
            </p>
        @endif
    </div>
@endsection

