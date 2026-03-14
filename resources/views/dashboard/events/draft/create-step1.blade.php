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
                                      action="{{ route('dashboard.events.draft.create.step1.store', ['locale' => $locale ?? app()->getLocale()]) }}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="draft_id" value="{{ request('draft_id') }}">

                                    @include('dashboard.events.draft.components.form-step1')

                                    <div class="step-footer step-tab-pager mt-4">
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
<link href="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var thumbInput = document.getElementById('thumb-img');
    var thumbPreview = document.getElementById('thumb-img-preview');
    if (thumbInput && thumbPreview) {
        thumbInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(ev) { thumbPreview.src = ev.target.result; };
                reader.readAsDataURL(file);
            }
        });
    }
    if (typeof ClassicEditor !== 'undefined' && document.getElementById('pd_editor')) {
        ClassicEditor.create(document.getElementById('pd_editor'), {
            removePlugins: ['ImageUpload'],
            toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','undo','redo']
        }).catch(function(err) { console.error(err); });
    }
});
</script>
@endpush
@endsection