@extends('layouts.app')

@section('title', __('Cart') . ' - Votix')

@section('content')
    <div class="wrapper">
        <div class="breadcrumb-block">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-10">
                        <div class="barren-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('ticketing.index') }}">{{ __('Home') }}</a></li>
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
                <div class="row">
                    <div class="col-xl-8 col-lg-7 col-md-12">
                        <div class="main-card">
                            <div class="bp-title">
                                <h4>{{ __('Order Summary') }}</h4>
                            </div>
                            <p class="mb-2">{{ __('Your cart is empty for now.') }}</p>
                            <a href="{{ route('ticketing.events') }}" class="main-btn btn-hover mt-3">{{ __('See events') }}</a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-5 col-md-12">
                        <div class="main-card">
                            <div class="bp-title">
                                <h4>{{ __('Total') }}</h4>
                            </div>
                            <div class="total-checkout">
                                <h4>AUD <span>$0.00</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

