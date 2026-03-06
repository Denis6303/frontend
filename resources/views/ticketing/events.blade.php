@extends('layouts.app')

@section('title', 'Événements - Votix')

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
                                    <button type="button" class="control" data-filter=".arts">Arts</button>
                                    <button type="button" class="control" data-filter=".business">Business</button>
                                    <button type="button" class="control" data-filter=".concert">Concert</button>
                                    <button type="button" class="control" data-filter=".workshops">Workshops</button>
                                </div>
                                <div class="row" data-ref="event-filter-content">
                                    <div
                                        class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mix arts concert workshops volunteer sports health_Wellness"
                                        data-ref="mixitup-target">
                                        <div class="main-card mt-4">
                                            <div class="event-thumbnail">
                                                <a href="{{ route('ticketing.events.show', ['id' => 1]) }}"
                                                    class="thumbnail-img">
                                                    <img src="{{ asset('template/images/event-imgs/img-1.jpg') }}"
                                                        alt="">
                                                </a>
                                                <span class="bookmark-icon" title="Bookmark"></span>
                                            </div>
                                            <div class="event-content">
                                                <a href="{{ route('ticketing.events.show', ['id' => 1]) }}"
                                                    class="event-title">A New Way Of Life</a>
                                                <div class="duration-price-remaining">
                                                    <span class="duration-price">AUD $100.00*</span>
                                                    <span class="remaining"></span>
                                                </div>
                                            </div>
                                            <div class="event-footer">
                                                <div class="event-timing">
                                                    <div class="publish-date">
                                                        <span><i
                                                                class="fa-solid fa-calendar-day me-2"></i>15 Apr</span>
                                                        <span class="dot"><i class="fa-solid fa-circle"></i></span>
                                                        <span>Fri, 3.45 PM</span>
                                                    </div>
                                                    <span class="publish-time"><i
                                                            class="fa-solid fa-clock me-2"></i>1h</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- D’autres cartes d’événements pourront être rendues dynamiquement ici --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

