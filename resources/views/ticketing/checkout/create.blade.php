@extends('layouts.app')

@section('title', __('Checkout') . ' — Votix')

@section('content')
    <div class="wrapper">
        <div class="breadcrumb-block">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-10">
                        <div class="barren-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('ticketing.index', ['locale' => $locale ?? app()->getLocale()]) }}">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Checkout') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="checkout-body p-80">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-10">
                        <div class="main-card">
                            <div class="bp-title">
                                <h4>{{ __('Confirmation') }}</h4>
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <p class="text-muted small mb-4">
                                {{ __('Occurrence ID: :id', ['id' => $draft['event_occurrence_id']]) }}
                            </p>
                            <form method="post" action="{{ route('ticketing.checkout.store', ['locale' => $locale ?? app()->getLocale()]) }}">
                                @csrf
                                <div class="mb-4">
                                    <p class="mb-1 text-muted small">{{ __('Tickets will be sent to') }}</p>
                                    <p class="mb-0 fw-semibold">{{ $userEmail ?? '—' }}</p>
                                </div>

                                <div class="mb-3">
                                    <label for="coupon_code" class="form-label">{{ __('Coupon code') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                    <input type="text" name="coupon_code" id="coupon_code" class="form-control" value="{{ old('coupon_code') }}">
                                </div>

                                <button type="submit" class="main-btn btn-hover">{{ __('Continue to payment') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Aucun script nécessaire ici (envoi par email automatique). --}}
