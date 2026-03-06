@extends('layouts.app')

@section('title', 'Détail de l’événement - Votix')

@section('content')
    <div class="wrapper">
        <div class="breadcrumb-block">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-10">
                        <div class="barren-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('ticketing.home') }}">Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('ticketing.events') }}">Explore
                                            Events</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Venue Event Detail View</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="event-dt-block p-80">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="event-top-dts">
                            <div class="event-top-date">
                                <span class="event-month">Apr</span>
                                <span class="event-date">30</span>
                            </div>
                            <div class="event-top-dt">
                                <h3 class="event-main-title">
                                    Spring Showcase Saturday April 30th 2022 at 7pm
                                </h3>
                                <div class="event-top-info-status">
                                    <span class="event-type-name"><i class="fa-solid fa-location-dot"></i>Venue
                                        Event</span>
                                    <span class="event-type-name details-hr">Starts on
                                        <span class="ev-event-date">Sat, Apr 30, 2022 11:30 AM</span></span>
                                    <span class="event-type-name details-hr">2h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7 col-md-12">
                        <div class="main-event-dt">
                            <div class="event-img">
                                <img src="{{ asset('template/images/logo.svg') }}" alt="Event image">
                            </div>
                            <div class="main-event-content">
                                <h4>About This Event</h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin dolor justo, sodales
                                    mattis orci et, mattis faucibus est. Nulla semper consectetur sapien a tempor.
                                </p>

                                {{-- Placeholder pour le futur module de vote --}}
                                <div id="vote-root" class="mt-4">
                                    {{-- Le module de vote sera monté ici (JS / template dédié) --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-5 col-md-12">
                        <div class="main-card event-right-dt">
                            <div class="bp-title">
                                <h4>Event Details</h4>
                            </div>
                            <div class="event-dt-right-group mt-4">
                                <div class="event-dt-right-icon">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div class="event-dt-right-content">
                                    <h4>Date and Time</h4>
                                    <h5>Sat, Apr 30, 2022 11:30 AM</h5>
                                </div>
                            </div>
                            <div class="event-dt-right-group">
                                <div class="event-dt-right-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="event-dt-right-content">
                                    <h4>Location</h4>
                                    <h5 class="mb-0">00 Challis St, Newport, Victoria, 0000, Australia</h5>
                                </div>
                            </div>
                            <div class="select-tickets-block">
                                <h6>Select Tickets</h6>
                                <div class="select-ticket-action">
                                    <div class="ticket-price">AUD $75.00</div>
                                    <div class="quantity">
                                        <div class="counter">
                                            <span class="down">-</span>
                                            <input type="text" value="0">
                                            <span class="up">+</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-btn mt-4">
                                    <a href="{{ route('ticketing.cart') }}"
                                        class="main-btn btn-hover w-100">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

