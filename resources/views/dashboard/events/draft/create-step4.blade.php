@extends('dashboard.layouts.base')

@section('title', __('Create Event'))

@section('content')
<div class="wrapper">
    <div class="event-dt-block event-create-form-block">
        <div class="ef-root">
            <div class="ef-shell">

                <div class="ef-stepper">
                    <a href="{{ route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => $draftId ?? request('draft_id')]) }}"
                       class="ef-step">
                        <div class="ef-step-num">1</div>
                        <span class="ef-step-label">{{ __('Details') }}</span>
                        <span class="ef-step-line"></span>
                    </a>
                    <a href="{{ route('dashboard.events.draft.create.step2', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => $draftId ?? request('draft_id')]) }}"
                       class="ef-step">
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
                       class="ef-step active">
                        <div class="ef-step-num">4</div>
                        <span class="ef-step-label">{{ __('Summary') }}</span>
                    </a>
                </div>

                <div class="ef-card">
                    <div class="ef-header">
                        <div class="ef-header-icon">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h2 class="ef-header-title">{{ __('Summary') }}</h2>
                            <p class="ef-header-sub">
                                {{ __('Review your draft, choose publication options and finish.') }}
                            </p>
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('dashboard.events.draft.create.step4.finalize', ['locale' => $locale ?? app()->getLocale()]) }}">
                        @csrf
                        <input type="hidden" name="draft_id" value="{{ $draftId ?? request('draft_id') ?? data_get($draft, 'id') }}">

                        <div class="ef-body">
                            <div class="ef-col" style="grid-column: 1 / -1;">
                                @include('dashboard.events.draft.components.form-step4')
                            </div>
                        </div>

                        <div class="ef-footer">
                            <div class="ef-progress">
                                <div class="ef-progress-dots">
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot active"></div>
                                </div>
                                <span class="ef-progress-text">{{ __('Step 4 of 4') }}</span>
                            </div>
                            <div>
                                <a href="{{ route('dashboard.events.draft.create.step3', ['locale' => $locale ?? app()->getLocale(), 'draft_id' => $draftId ?? request('draft_id')]) }}"
                                   class="btn btn-outline-dark me-2">
                                    <i class="fa-solid fa-arrow-left-long me-2"></i>{{ __('Previous') }}
                                </a>
                                <button type="submit" class="ef-btn-next">
                                    {{ __('Publish') }}
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

    // --- Card-style radio toggles (publish_now, is_private) ---
    document.querySelectorAll('.ef-attendance').forEach(function(group) {
        group.querySelectorAll('.ef-att-card').forEach(function(card) {
            card.addEventListener('click', function () {
                var radio = card.querySelector('input[type="radio"]');
                if (!radio) return;
                group.querySelectorAll('.ef-att-card').forEach(function (c) {
                    c.classList.remove('selected');
                });
                card.classList.add('selected');
                radio.checked = true;

                // If this is the publish_now group, re-run scheduling logic
                if (radio.id === 'publish_now_yes' || radio.id === 'publish_now_no') {
                    toggleSchedule();
                }
            });
        });
    });
});
</script>
@endpush
@endsection
