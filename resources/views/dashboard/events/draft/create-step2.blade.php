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
                                <li>
                                    <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Details</span>
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">{{ __('Location & Dates') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.events.draft.create.step3', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">{{ __('Tickets') }}</span>
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
                                <form method="POST"
                                      action="{{ route('dashboard.events.draft.create.step2.store', ['locale' => $locale ?? app()->getLocale()]) }}">
                                    @csrf
                                    <input type="hidden" name="draft_id" value="{{ request('draft_id') }}">

                                    @include('dashboard.events.draft.components.form-step2')

                                    <div class="step-footer step-tab-pager mt-4">
                                        <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => request('draft_id')]) }}"
                                           class="btn btn-default btn-hover steps_btn">
                                            {{ __('Previous') }}
                                        </a>
                                        <button type="submit" class="btn btn-default btn-hover steps_btn">
                                            {{ __('Next') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var locale = document.documentElement.lang === 'fr' ? 'fr' : 'en';
    var fpConfig = { enableTime: true, time_24hr: true, dateFormat: 'Y-m-d H:i', locale: locale };
    var startEl = document.getElementById('start_date_0');
    var endEl = document.getElementById('end_date_0');
    if (startEl) flatpickr(startEl, fpConfig);
    if (endEl) flatpickr(endEl, fpConfig);
});
</script>
@endpush
@endsection