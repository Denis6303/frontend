@extends('dashboard.layouts.base')

@section('title', __('Create Event'))

@section('content')
<div class="wrapper">
    <div class="event-dt-block event-create-form-block">
        <div class="ef-root">
            <div class="ef-shell">

                {{-- Stepper --}}
                <div class="ef-stepper">
                    <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => $draftId ?? request('draft_id')]) }}"
                       class="ef-step">
                        <div class="ef-step-num">1</div>
                        <span class="ef-step-label">{{ __('Details') }}</span>
                        <span class="ef-step-line"></span>
                    </a>
                    <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => $draftId ?? request('draft_id')]) }}"
                       class="ef-step active">
                        <div class="ef-step-num">2</div>
                        <span class="ef-step-label">{{ __('Location & Dates') }}</span>
                        <span class="ef-step-line"></span>
                    </a>
                    <a href="{{ route('dashboard.events.draft.create.step3', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => $draftId ?? request('draft_id')]) }}"
                       class="ef-step">
                        <div class="ef-step-num">3</div>
                        <span class="ef-step-label">{{ __('Tickets') }}</span>
                        <span class="ef-step-line"></span>
                    </a>
                    <a href="{{ route('dashboard.events.draft.create.step4', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => $draftId ?? request('draft_id')]) }}"
                       class="ef-step">
                        <div class="ef-step-num">4</div>
                        <span class="ef-step-label">{{ __('Summary') }}</span>
                    </a>
                </div>

                <div class="ef-card">
                    <div class="ef-header">
                        <div class="ef-header-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h2 class="ef-header-title">{{ __('Location & Dates') }}</h2>
                            <p class="ef-header-sub">
                                {{ __('Define where your event takes place and when it starts and ends.') }}
                            </p>
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('dashboard.events.draft.create.step2.store', ['locale' => $locale ?? app()->getLocale()]) }}">
                        @csrf
                        <input type="hidden" name="draft_id" value="{{ $draftId ?? request('draft_id') }}">

                        <div class="ef-body">
                            <div class="ef-col" style="grid-column: 1 / -1;">
                                @include('dashboard.events.draft.components.form-step2')
                            </div>
                        </div>

                        <div class="ef-footer">
                            <div class="ef-progress">
                                <div class="ef-progress-dots">
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot"></div>
                                    <div class="ef-dot"></div>
                                </div>
                                <span class="ef-progress-text">{{ __('Step 2 of 4') }}</span>
                            </div>
                            <div>
                                <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => $draftId ?? request('draft_id')]) }}"
                                   class="btn btn-outline-dark me-2">
                                    <i class="fa-solid fa-arrow-left-long me-2"></i>{{ __('Previous') }}
                                </a>
                                <button type="submit" class="ef-btn-next">
                                    {{ __('Continue') }}
                                    <i class="fa-solid fa-arrow-right-long ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('dashboard/css/form-step.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var locale = document.documentElement.lang === 'fr' ? 'fr' : 'en';
    var fpConfig = { enableTime: true, time_24hr: true, dateFormat: 'Y-m-d H:i', locale: locale };
    var removeLabel = {!! json_encode(__('Remove this date')) !!};

    function bindFlatpickr(container) {
        (container || document).querySelectorAll('.event-datetime-picker').forEach(function (el) {
            if (el._flatpickr) return;
            flatpickr(el, fpConfig);
        });
    }

    var datesContainer = document.getElementById('dates-container');
    var index = 1;
    if (datesContainer) {
        datesContainer.querySelectorAll('[id^="start_date_"]').forEach(function (el) {
            var m = el.id.match(/^start_date_(\d+)$/);
            if (m) index = Math.max(index, parseInt(m[1], 10) + 1);
        });
        bindFlatpickr(datesContainer);
        datesContainer.querySelectorAll('.remove-date-row').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var row = btn.closest('.row');
                if (row) row.remove();
            });
        });
    }

    if (typeof jQuery !== 'undefined' && jQuery.fn.selectpicker) {
        jQuery('select.selectpicker').each(function () {
            var $el = jQuery(this);
            if (!$el.parent().hasClass('bootstrap-select')) {
                $el.selectpicker();
            }
        });
    }

    var addBtn = document.getElementById('add-date-row');
    if (addBtn && datesContainer) {
        addBtn.addEventListener('click', function () {
            var row = document.createElement('div');
            row.className = 'row g-3 mb-3 date-row-extra';
            row.innerHTML =
                '<div class="col-md-6">' +
                    '<label class="form-label fs-6">Start date & time*</label>' +
                    '<div class="loc-group position-relative">' +
                        '<input class="form-control h_50 event-datetime-picker" type="text" ' +
                        'name="start_dates[' + index + ']" id="start_date_' + index + '" placeholder="YYYY-MM-DD HH:MM" autocomplete="off">' +
                        '<span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<label class="form-label fs-6">End date & time*</label>' +
                    '<div class="loc-group position-relative">' +
                        '<input class="form-control h_50 event-datetime-picker" type="text" ' +
                        'name="end_dates[' + index + ']" id="end_date_' + index + '" placeholder="YYYY-MM-DD HH:MM" autocomplete="off">' +
                        '<span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>' +
                    '</div>' +
                '</div>' +
                '<div class="col-12 text-end mt-2">' +
                    '<button type="button" class="btn btn-link text-danger p-0 remove-date-row">' +
                        '<i class="fa-solid fa-trash-can me-1"></i>' + removeLabel +
                    '</button>' +
                '</div>';
            datesContainer.appendChild(row);
            bindFlatpickr(row);
            var removeBtn = row.querySelector('.remove-date-row');
            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    row.remove();
                });
            }
            index++;
        });
    }
});
</script>
@endpush
@endsection
