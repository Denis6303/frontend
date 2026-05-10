@extends('layouts.app')

@section('title', 'Événements - Votix')

@section('content')
    <div class="wrapper">
        <div class="explore-events p-80">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="main-title">
                            <h3>{{ __('Explore Events') }}</h3>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="event-filter-items">
                            <div class="featured-controls">
                                <div class="controls">
                                    <button type="button" class="control" data-filter="all">{{ __('All') }}</button>
                                    <button type="button" class="control" data-filter=".arts">{{ __('Arts') }}</button>
                                    <button type="button" class="control" data-filter=".business">{{ __('Business') }}</button>
                                    <button type="button" class="control" data-filter=".concert">{{ __('Concert') }}</button>
                                    <button type="button" class="control" data-filter=".workshops">{{ __('Workshops') }}</button>
                                </div>
                                <div class="row" data-ref="event-filter-content">
                                    @forelse($events as $event)
                                        @php
                                            $cover = votix_media_url($event['cover_url'] ?? null) ?? asset('images/event-imgs/img-1.jpg');
                                            $occ   = $event['occurrences'][0] ?? null;
                                            $start = $occ['start_date'] ?? null;
                                            $price = $event['price_min'] ?? null;
                                            $city  = $event['city'] ?? null;
                                        @endphp
                                        <div
                                            class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mix"
                                            data-ref="mixitup-target">
                                            <div class="main-card mt-4">
                                                <div class="event-thumbnail">
                                                    <a href="{{ route('events.show', ['locale' => $locale ?? app()->getLocale(), 'slug' => $event['slug']]) }}"
                                                       class="thumbnail-img">
                                                        <img src="{{ $cover }}" alt="">
                                                    </a>
                                                </div>
                                                <div class="event-content">
                                                    <a href="{{ route('events.show', ['locale' => $locale ?? app()->getLocale(), 'slug' => $event['slug']]) }}"
                                                       class="event-title">{{ $event['title'] ?? '—' }}</a>
                                                    <div class="duration-price-remaining">
                                                        @if($price !== null)
                                                            <span class="duration-price">
                                                                {{ $event['currency'] ?? '' }} {{ number_format((float) $price, 0, ',', ' ') }}
                                                            </span>
                                                        @endif
                                                        @if($city)
                                                            <span class="remaining">{{ $city }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="event-footer">
                                                    <div class="event-timing">
                                                        @if($start)
                                                            <div class="publish-date">
                                                                <span>
                                                                    <i class="fa-solid fa-calendar-day me-2"></i>
                                                                    {{ \Carbon\Carbon::parse($start)->translatedFormat('d M') }}
                                                                </span>
                                                                <span class="dot"><i class="fa-solid fa-circle"></i></span>
                                                                <span>{{ \Carbon\Carbon::parse($start)->translatedFormat('D, H:i') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted mb-0">{{ __('No events found.') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

