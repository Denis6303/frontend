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
                                <li class="active">
                                    <a href="{{ route('dashboard.events.draft.create.step4', ['locale' => $locale ?? app()->getLocale()]) }}">
                                        <span class="number"></span>
                                        <span class="step-name">Summary</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="step-content">
                                <form method="POST"
                                      action="{{ route('dashboard.events.draft.create.step4.finalize', ['locale' => $locale ?? app()->getLocale()]) }}">
                                    @csrf
                                    <input type="hidden" name="draft_id" value="{{ request('draft_id') }}">

                                    @include('dashboard.events.draft.components.form-step4')

                                    <div class="step-footer step-tab-pager mt-4">
                                        <a href="{{ route('dashboard.events.draft.create.step3', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => request('draft_id')]) }}"
                                           class="btn btn-default btn-hover steps_btn">
                                            {{ __('Previous') }}
                                        </a>
                                        <button type="submit" class="btn btn-default btn-hover steps_btn">
                                            {{ __('Publish') }}
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
    var yes = document.getElementById('publish_now_yes');
    var no = document.getElementById('publish_now_no');
    var wrapper = document.getElementById('schedule-at-wrapper');
    var input = document.getElementById('scheduled_at');
    var fp = null;

    function toggleSchedule() {
        var show = no && no.checked;
        if (wrapper) wrapper.style.display = show ? 'block' : 'none';
        if (show && input) {
            if (!fp) {
                var locale = document.documentElement.lang === 'fr' ? 'fr' : 'en';
                fp = flatpickr(input, { enableTime: true, time_24hr: true, dateFormat: 'Y-m-d H:i', locale: locale });
            }
        } else if (fp && input) {
            input.value = '';
        }
    }

    if (yes) yes.addEventListener('change', toggleSchedule);
    if (no) no.addEventListener('change', toggleSchedule);
    toggleSchedule();
});
</script>
@endpush
@endsection