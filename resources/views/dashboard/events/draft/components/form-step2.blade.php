@php
    $p = $prefill ?? [];
    $step2Starts = old('start_dates');
    $step2Ends = old('end_dates');
    if (! is_array($step2Starts) && ! empty($draft)) {
        $occ = data_get($draft, 'data.occurrences')
            ?? data_get($draft, 'occurrences')
            ?? [];
        $step2Starts = [];
        $step2Ends = [];
        foreach ($occ as $o) {
            $sd = $o['start_date'] ?? null;
            $ed = $o['end_date'] ?? null;
            try {
                $step2Starts[] = $sd ? \Illuminate\Support\Carbon::parse($sd)->format('Y-m-d H:i') : '';
            } catch (\Throwable $e) {
                $step2Starts[] = '';
            }
            try {
                $step2Ends[] = $ed ? \Illuminate\Support\Carbon::parse($ed)->format('Y-m-d H:i') : '';
            } catch (\Throwable $e) {
                $step2Ends[] = '';
            }
        }
    }
    if (! is_array($step2Starts) || empty($step2Starts)) {
        $step2Starts = [''];
    }
    if (! is_array($step2Ends) || count($step2Ends) !== count($step2Starts)) {
        $step2Ends = array_replace(array_fill(0, count($step2Starts), ''), is_array($step2Ends) ? $step2Ends : []);
    }
    $countryOld = old_or_prefill('country_code', $p['country_code'] ?? null);
    if ($countryOld === null || $countryOld === '') {
        $countryOld = 'tg';
    }
    $currencyOld = old_or_prefill('currency', $p['currency'] ?? null);
    if ($currencyOld === null || $currencyOld === '') {
        $currencyOld = 'XOF';
    }
@endphp
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
                            <select class="selectpicker @error('country_code') is-invalid @enderror" name="country_code" data-size="5" data-live-search="true">
                                <option value="tg" {{ $countryOld === 'tg' ? 'selected' : '' }}>Togo</option>
                                <option value="other" {{ $countryOld === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('country_code')
                            <p class="text-danger small mb-0">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-6">Currency*</label>
                            <select class="selectpicker @error('currency') is-invalid @enderror" name="currency" data-size="5">
                                <option value="XOF" {{ $currencyOld === 'XOF' ? 'selected' : '' }}>FCFA</option>
                                <option value="EUR" {{ $currencyOld === 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="USD" {{ $currencyOld === 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                            @error('currency')
                            <p class="text-danger small mb-0">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-6">City</label>
                            <input class="form-control h_50 @error('city') is-invalid @enderror" type="text" name="city" value="{{ old('city', $p['city'] ?? '') }}" placeholder="{{ __('City') }}">
                            @error('city')
                            <p class="text-danger small mb-0">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-6">Address</label>
                            <input class="form-control h_50 @error('address') is-invalid @enderror" type="text" name="address" value="{{ old_or_prefill('address', $p['address'] ?? '') }}" placeholder="{{ __('Address') }}">
                            @error('address')
                            <p class="text-danger small mb-0">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Separator before dates block --}}
                    <div class="ef-divider mb-3 mt-1"></div>

                    <div id="dates-container">
                        @foreach ($step2Starts as $idx => $startVal)
                        <div class="row g-3 mb-3 {{ $idx > 0 ? 'date-row-extra' : '' }}">
                            <div class="col-md-6">
                            <label class="form-label fs-6">Start date & time*</label>
                            <div class="loc-group position-relative">
                                <input class="form-control h_50 event-datetime-picker" type="text" name="start_dates[{{ $idx }}]" id="start_date_{{ $idx }}" placeholder="YYYY-MM-DD HH:MM" value="{{ $startVal }}" autocomplete="off">
                                <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                            </div>
                        </div>
                            <div class="col-md-6">
                            <label class="form-label fs-6">End date & time*</label>
                            <div class="loc-group position-relative">
                                <input class="form-control h_50 event-datetime-picker" type="text" name="end_dates[{{ $idx }}]" id="end_date_{{ $idx }}" placeholder="YYYY-MM-DD HH:MM" value="{{ $step2Ends[$idx] ?? '' }}" autocomplete="off">
                                <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                            </div>
                            </div>
                            @if ($idx > 0)
                            <div class="col-12 text-end mt-2">
                                <button type="button" class="btn btn-link text-danger p-0 remove-date-row">
                                    <i class="fa-solid fa-trash-can me-1"></i>{{ __('Remove this date') }}
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-outline-dark mt-2" id="add-date-row">
                        <i class="fa-solid fa-plus me-2"></i>{{ __('Add another date') }}
                    </button>
                </div>
            
        </div>
    </div>
</div>