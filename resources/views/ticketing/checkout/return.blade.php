@extends('layouts.no-header-footer')

@section('title', __('Payment result') . ' — Votix')

@section('content')
    @php
        $locale = $locale ?? app()->getLocale();
        $st = is_array($intent) ? ($intent['status'] ?? '') : '';
    @endphp
    <div class="wrapper py-4">
        <div class="checkout-body py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="main-card text-center py-5">
                            @if ($paid)
                                <div class="mb-3 text-success fs-1"><i class="fa-solid fa-circle-check"></i></div>
                                <h4 class="mb-3">{{ __('Payment confirmed') }}</h4>
                                <p class="text-muted mb-4">{{ __('Thank you. Your order has been recorded.') }}</p>
                            @elseif ($verifyOk && ! $paid)
                                <div class="mb-3 text-warning fs-1"><i class="fa-solid fa-clock"></i></div>
                                <h4 class="mb-3">{{ __('Payment pending') }}</h4>
                                <p class="text-muted mb-4">{{ __('We are still confirming your payment. You will receive a confirmation shortly.') }}</p>
                            @else
                                <div class="mb-3 text-danger fs-1"><i class="fa-solid fa-circle-xmark"></i></div>
                                <h4 class="mb-3">{{ __('Unable to confirm') }}</h4>
                                <p class="text-muted mb-4">{{ __('Please try again or contact support if the problem persists.') }}</p>
                            @endif
                            @if ($st)
                                <p class="small text-muted">{{ __('Status') }}: {{ $st }}</p>
                            @endif
                            <a href="{{ route('ticketing.events', ['locale' => $locale]) }}" class="main-btn btn-hover mt-3">{{ __('Back to events') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
