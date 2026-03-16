@extends('layouts.app')

@section('title', 'Événements - Votix')

@push('styles')
<style>
.explore-events { margin-top: 2rem; }
.event-content .event-title { font-size: 1.05rem; font-weight: 600; }
</style>
@endpush

@section('content')
    <div class="wrapper">
        <div class="explore-events p-80">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="main-title">
                            <h3>Explore Events</h3>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="event-filter-items">
                            <div class="featured-controls">
                                <div class="controls">
                                    <button type="button" class="control" data-filter="all">All</button>
                                    @foreach(($categories ?? []) as $cat)
                                        @php
                                            $slug = \Illuminate\Support\Str::slug($cat['name_en'] ?? $cat['name'] ?? '');
                                        @endphp
                                        <button type="button" class="control" data-filter=".{{ $slug }}">
                                            {{ $cat['name'] ?? $cat['name_en'] ?? '' }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="row" data-ref="event-filter-content">
                                    @forelse($events as $event)
                                        @php
                                            $categorySlug = \Illuminate\Support\Str::slug($event['category']['name_en'] ?? $event['category']['name'] ?? '');
                                        @endphp
                                        <div
                                            class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mix {{ $categorySlug ? $categorySlug : '' }}"
                                            data-ref="mixitup-target">
                                            @include('partials.event-card', ['event' => $event])
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

