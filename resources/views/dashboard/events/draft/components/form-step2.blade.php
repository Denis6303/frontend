<div class="step-tab-panel step-tab-gallery active" id="tab_step2">
    <div class="tab-from-content">
        <div class="main-card">
          
                <div class="p-4 form-group border_bottom pb_30">
                    <label class="form-label fs-16">Where and when is your event?*</label>
                    <p class="mt-2 fs-14 d-block mb-3">
                        Set country, currency and main dates for your event.
                    </p>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-6">Country*</label>
                            <select class="selectpicker" name="country_code" data-size="5" data-live-search="true">
                                <option value="tg">Togo</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-6">Currency*</label>
                            <select class="selectpicker" name="currency" data-size="5">
                                <option value="XOF">XOF</option>
                                <option value="EUR">EUR</option>
                                <option value="USD">USD</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-6">City</label>
                            <input class="form-control h_50" type="text" name="city" value="{{ old('city') }}" placeholder="{{ __('City') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-6">Address</label>
                            <input class="form-control h_50" type="text" name="address" value="{{ old('address') }}" placeholder="{{ __('Address') }}">
                        </div>
                    </div>

                    {{-- Separator before dates block --}}
                    <div class="ef-divider mb-3 mt-1"></div>

                    <div id="dates-container">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                            <label class="form-label fs-6">Start date & time*</label>
                            <div class="loc-group position-relative">
                                <input class="form-control h_50 event-datetime-picker" type="text" name="start_dates[0]" id="start_date_0" placeholder="YYYY-MM-DD HH:MM" value="{{ old('start_dates.0') }}" autocomplete="off">
                                <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                            </div>
                        </div>
                            <div class="col-md-6">
                            <label class="form-label fs-6">End date & time*</label>
                            <div class="loc-group position-relative">
                                <input class="form-control h_50 event-datetime-picker" type="text" name="end_dates[0]" id="end_date_0" placeholder="YYYY-MM-DD HH:MM" value="{{ old('end_dates.0') }}" autocomplete="off">
                                <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                            </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-dark mt-2" id="add-date-row">
                        <i class="fa-solid fa-plus me-2"></i>{{ __('Add another date') }}
                    </button>
                </div>
            
        </div>
    </div>
</div>