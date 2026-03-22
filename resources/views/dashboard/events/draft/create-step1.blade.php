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
                       class="ef-step active">
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
                       class="ef-step">
                        <div class="ef-step-num">4</div>
                        <span class="ef-step-label">{{ __('Summary') }}</span>
                    </a>
                </div>

                {{-- Card --}}
                <div class="ef-card">

                    <div class="ef-header">
                        <div class="ef-header-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="ef-header-title">{{ __('Event details') }}</h2>
                            <p class="ef-header-sub">{{ __('Tell people what your event is about') }}</p>
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('dashboard.events.draft.create.step1.store', ['locale' => $locale ?? app()->getLocale()]) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="draft_id" value="{{ $draftId ?? request('draft_id') }}">
                        <input type="hidden" name="image_url" value="{{ old_or_prefill('image_url', ($prefill['cover_url'] ?? '')) }}">

                        @include('dashboard.events.draft.components.form-step1')

                        <div class="ef-footer">
                            <div class="ef-progress">
                                <div class="ef-progress-dots">
                                    <div class="ef-dot active"></div>
                                    <div class="ef-dot"></div>
                                    <div class="ef-dot"></div>
                                    <div class="ef-dot"></div>
                                </div>
                                <span class="ef-progress-text">{{ __('Step 1 of 4') }}</span>
                            </div>
                            <div>
                                <button type="submit" class="ef-btn-next">
                                    {{ __('Continue') }}
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                        <polyline points="12 5 19 12 12 19"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('dashboard/css/form-step.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Bannière déjà renseignée côté serveur (URL API / storage) : masquer le placeholder
    (function syncBannerFromServer() {
        var img = document.getElementById('banner-preview');
        var ph = document.getElementById('banner-placeholder');
        if (!img || !ph) return;
        var src = (img.getAttribute('src') || '').trim();
        if (src && src.indexOf('data:') !== 0) {
            img.classList.add('has-img');
            ph.style.display = 'none';
            img.addEventListener('error', function onBannerErr() {
                img.removeEventListener('error', onBannerErr);
                img.classList.remove('has-img');
                ph.style.display = '';
                img.removeAttribute('src');
            });
        }
    })();

    // --- Banner image preview ---
    const bannerInput   = document.getElementById('thumb-img');
    const bannerPreview = document.getElementById('banner-preview');
    const bannerPlaceholder = document.getElementById('banner-placeholder');

    if (bannerInput && bannerPreview) {
        bannerInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    bannerPreview.src = ev.target.result;
                    bannerPreview.classList.add('has-img');
                    if (bannerPlaceholder) bannerPlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Click on banner area opens file picker
    const bannerZone = document.getElementById('banner-zone');
    if (bannerZone && bannerInput) {
        bannerZone.addEventListener('click', function () {
            bannerInput.click();
        });
    }

    // --- CKEditor ---
    if (typeof ClassicEditor !== 'undefined' && document.getElementById('pd_editor')) {
        ClassicEditor.create(document.getElementById('pd_editor'), {
            removePlugins: ['ImageUpload'],
            toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','undo','redo']
        }).catch(function (err) { console.error(err); });
    }

    // --- Description char counter ---
    const descArea  = document.getElementById('pd_editor');
    const charCount = document.getElementById('char-count');
    if (descArea && charCount) {
        descArea.addEventListener('input', function () {
            const n = descArea.value.length;
            charCount.textContent = n + ' / 2000';
            if (n > 2000) descArea.value = descArea.value.slice(0, 2000);
        });
    }

    // --- Attendance type cards ---
    document.querySelectorAll('.ef-att-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.ef-att-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            card.querySelector('input[type="radio"]').checked = true;
        });
    });

    // Bootstrap-select : init unique si besoin (ne jamais destroy → évite les selects « bruts » sans style)
    if (typeof jQuery !== 'undefined' && jQuery.fn.selectpicker) {
        jQuery('select.selectpicker').each(function () {
            var $el = jQuery(this);
            if (!$el.parent().hasClass('bootstrap-select')) {
                $el.selectpicker();
            }
        });
    }
});
</script>
@endpush
@endsection