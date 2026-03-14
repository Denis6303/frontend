@extends('dashboard.layouts.base')

@section('title', __('Create Event'))

@section('content')
<div class="wrapper">
    <div class="event-dt-block event-create-form-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9 col-md-12">
                    <div class="wizard-steps-block">
                        <div id="add-event-tab" class="step-app">
                            <ul class="step-steps">
                                <li class="active">
                                    <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Details</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Tickets</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.events.draft.create.step3', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Setting</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.events.draft.create.step4', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Summary</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="step-content">
                                @include('dashboard.events.draft.components.form-step1')
                            </div>
                            <div class="step-footer step-tab-pager mt-4">
                                <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale()]) }}"
                                   class="btn btn-default btn-hover steps_btn">Next</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection