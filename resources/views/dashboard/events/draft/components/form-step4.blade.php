<div class="step-tab-panel active" id="tab_step4">
    <div class="tab-from-content">
        <div class="main-card">
            <div class="bp-title">
                <h4><i class="fa-solid fa-list-check step_icon me-3"></i>Summary</h4>
            </div>
            <div class="p_30 bp-form main-form">
                <div class="main-card p-4 mb-4">
                    <h5 class="mb-2">Review your draft</h5>
                    <p class="mb-0 text-light3">
                        Cette étape affichera le résumé (détails, tickets, settings) avant publication.
                    </p>
                </div>

                <div class="main-card p-4">
                    <h5 class="mb-3">{{ __('Publication options') }}</h5>
                    <div class="mb-3">
                        <label class="form-label d-block mb-2">{{ __('Publish now?') }}*</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="publish_now" id="publish_now_yes" value="true" checked>
                            <label class="form-check-label" for="publish_now_yes">{{ __('Yes') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="publish_now" id="publish_now_no" value="false">
                            <label class="form-check-label" for="publish_now_no">{{ __('No, schedule or save') }}</label>
                        </div>
                    </div>

                    <div id="schedule-at-wrapper" class="mb-3" style="display:none">
                        <label class="form-label">{{ __('Schedule at') }}</label>
                        <div class="loc-group position-relative">
                            <input type="text" class="form-control h_50" name="scheduled_at" id="scheduled_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('scheduled_at') }}" autocomplete="off">
                            <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label d-block mb-2">Private event?*</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_private" id="is_private_no" value="false" checked>
                            <label class="form-check-label" for="is_private_no">No</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_private" id="is_private_yes" value="true">
                            <label class="form-check-label" for="is_private_yes">Yes</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>