<div class="step-tab-panel step-tab-info active" id="tab_step1">
    <div class="tab-from-content">
        <div class="main-card">
            <div class="bp-title">
                <h4><i class="fa-solid fa-circle-info step_icon me-3"></i>Details</h4>
            </div>
            <div class="p-4 bp-form main-form">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group border_bottom pb_30">
                            <label class="form-label fs-16">Give your event a name.*</label>
                            <p class="mt-2 d-block fs-14 mb-3">
                                See how your name appears on the event page and a list of all places where your event name will be used.
                                <a href="#" class="a-link">Learn more</a>
                            </p>
                            <input class="form-control h_50" type="text" name="title" placeholder="Enter event name here" value="{{ old('title') }}">
                        </div>

                        <div class="form-group border_bottom pt_30 pb_30">
                            <label class="form-label fs-16">Choose a category for your event.*</label>
                            <p class="mt-2 d-block fs-14 mb-3">
                                Choosing relevant categories helps to improve the discoverability of your event.
                                <a href="#" class="a-link">Learn more</a>
                            </p>
                            <select class="selectpicker" name="category_id" data-size="5" title="{{ __('Select category') }}" data-live-search="true">
                                @forelse($categories ?? [] as $cat)
                                    <option value="{{ $cat['id'] }}" {{ old('category_id') == $cat['id'] ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'fr' ? ($cat['name'] ?? $cat['name_en']) : ($cat['name_en'] ?? $cat['name']) }}
                                    </option>
                                @empty
                                    <option value="">{{ __('No category') }}</option>
                                @endforelse
                            </select>
                        </div>

                        {{-- Les dates/horaires sont gérées à l'étape 2 (API step2). --}}

                        <div class="form-group pt_30 pb_30">
                            <label class="form-label fs-16">Add a few images to your event banner.</label>
                            <p class="mt-2 fs-14 d-block mb-3 pe_right">
                                Upload colorful and vibrant images as the banner for your event!
                                <a href="#" class="a-link">Learn more</a>
                            </p>
                            <div class="content-holder mt-4">
                                <div class="default-event-thumb">
                                    <div class="default-event-thumb-btn">
                                        <div class="thumb-change-btn">
                                            <input type="file" id="thumb-img" name="image" accept="image/png,image/jpeg,image/jpg">
                                            <label for="thumb-img">Change Image</label>
                                        </div>
                                    </div>
                                    <img id="thumb-img-preview" src="{{ old('image_url') ?? asset('template/images/banner.jpg') }}" alt="">
                                </div>
                            </div>
                        </div>

                            <div class="form-group border_bottom pb_30">
                            <label class="form-label fs-16">Please describe your event.</label>
                            <p class="mt-2 fs-14 d-block mb-3">
                                Write a few words below to describe your event and provide any extra information such as schedules, itinerary or any special instructions required to attend your event.
                            </p>
                            <div class="text-editor mt-4">
                                <textarea name="description" id="pd_editor" rows="6" placeholder="{{ __('Describe your event') }}">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- La localisation détaillée est gérée à l'étape 2 (API step2). --}}

                        <div class="form-group pt_30 pb-2">
                            <label class="form-label fs-16">How will people attend? *</label>
                            <p class="mt-2 fs-14 d-block mb-3">Choose if your event is in-person or online.</p>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="attendance_type" id="attendance_in_person" value="in_person" checked>
                                    <label class="form-check-label" for="attendance_in_person">
                                        In person
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="attendance_type" id="attendance_online" value="online">
                                    <label class="form-check-label" for="attendance_online">
                                        Online
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>