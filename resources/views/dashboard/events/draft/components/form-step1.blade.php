@php
    $p = $prefill ?? [];
    $bannerUrl = banner_display_url_for_draft($p);
@endphp
<div class="ef-body">

    {{-- LEFT COLUMN --}}
    <div class="ef-col">
        <p class="ef-section-label">{{ __('Identity & description') }}</p>

        {{-- Event name --}}
        <div class="ef-field">
            <label class="ef-label" for="event-title">
                {{ __('Event name') }} <span class="ef-required">*</span>
            </label>
            <input
                id="event-title"
                class="ef-input @error('title') is-invalid @enderror"
                type="text"
                name="title"
                placeholder="{{ __('Enter event name') }}"
                value="{{ old_or_prefill('title', $p['title'] ?? '') }}"
            >
            @error('title')
            <p class="ef-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Category (under name) --}}
        <div class="ef-field">
            <label class="ef-label" for="event-category">
                {{ __('Category') }} <span class="ef-required">*</span>
            </label>
            <select
                id="event-category"
                class="selectpicker @error('category_id') is-invalid @enderror"
                name="category_id"
                data-size="6"
                data-live-search="true"
            >
                <option value="">{{ __('Select category') }}</option>
                @forelse($categories ?? [] as $cat)
                    <option
                        value="{{ $cat['id'] }}"
                        {{ (string) old_or_prefill('category_id', $p['category_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' }}
                    >
                        {{ app()->getLocale() === 'fr'
                            ? ($cat['name'] ?? $cat['name_en'])
                            : ($cat['name_en'] ?? $cat['name']) }}
                    </option>
                @empty
                    <option value="" disabled>{{ __('No category available') }}</option>
                @endforelse
            </select>
            @error('category_id')
            <p class="ef-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ef-divider"></div>

        {{-- Description (moved to left) --}}
        <p class="ef-section-label">{{ __('Description') }}</p>

        <div class="ef-field" style="margin-bottom: 4px;">
            <label class="ef-label" for="pd_editor">{{ __('Description') }}</label>
            <p class="ef-hint">
                {{ __('Schedules, special instructions, itinerary — anything attendees need to know.') }}
            </p>
            <div class="event-description-editor">
                <textarea
                    name="description"
                    id="pd_editor"
                >{{ old_or_prefill('description', $p['description'] ?? '') }}</textarea>
            </div>
            @error('description')
            <p class="ef-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="ef-col">
        <p class="ef-section-label">{{ __('Visuals & format') }}</p>

        {{-- Banner --}}
        <div class="ef-field">
            <label class="ef-label">{{ __('Banner image') }}</label>
            <p class="ef-hint">{{ __('Recommended size 1600 × 700 px — PNG or JPG, max 5 MB.') }}</p>
            <div class="ef-banner-zone" id="banner-zone">
                <input type="file" id="thumb-img" name="image"
                       accept="image/png,image/jpeg,image/jpg" style="display:none">
                <img id="banner-preview"
                     src="{{ $bannerUrl }}"
                     class="{{ $bannerUrl !== '' ? 'has-img' : '' }}"
                     alt="{{ __('Banner preview') }}"
                     loading="lazy"
                     decoding="async">
                <div class="ef-banner-overlay">
                    <span>{{ __('Change image') }}</span>
                </div>
                <div class="ef-banner-placeholder" id="banner-placeholder"
                     style="{{ $bannerUrl !== '' ? 'display:none' : '' }}">
                    <p><strong>{{ __('Click to upload') }}</strong><br>{{ __('PNG / JPG, max 5 MB') }}</p>
                </div>
            </div>
            @error('image')
            <p class="ef-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Attendance type (moved to right) --}}
        <div class="ef-divider"></div>

        <p class="ef-section-label">{{ __('Attendance format') }}</p>

        <div class="ef-field" style="margin-bottom: 0;">
            <p class="ef-hint" style="margin-bottom: 12px;">
                {{ __('How will people join your event?') }}
            </p>
            <div class="ef-attendance" id="ef-attendance">

                <label class="ef-att-card {{ old_or_prefill('attendance_type', $p['attendance_type'] ?? 'in_person') === 'in_person' ? 'selected' : '' }}">
                    <input type="radio" name="attendance_type" value="in_person"
                        {{ old_or_prefill('attendance_type', $p['attendance_type'] ?? 'in_person') === 'in_person' ? 'checked' : '' }}>
                    <div class="ef-att-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="ef-att-info">
                        <strong>{{ __('In person') }}</strong>
                        <span>{{ __('Physical venue') }}</span>
                    </div>
                </label>

                <label class="ef-att-card {{ old_or_prefill('attendance_type', $p['attendance_type'] ?? 'in_person') === 'online' ? 'selected' : '' }}">
                    <input type="radio" name="attendance_type" value="online"
                        {{ old_or_prefill('attendance_type', $p['attendance_type'] ?? 'in_person') === 'online' ? 'checked' : '' }}>
                    <div class="ef-att-icon">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    <div class="ef-att-info">
                        <strong>{{ __('Online') }}</strong>
                        <span>{{ __('Virtual event') }}</span>
                    </div>
                </label>

            </div>
            @error('attendance_type')
            <p class="ef-error">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>