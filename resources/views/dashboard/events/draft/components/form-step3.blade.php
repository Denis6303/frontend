<div class="step-tab-panel step-tab-tickets active" id="tab_step3">
    <div class="tab-from-content">
        <div class="main-card">
            <div class="p_30 bp-form main-form">
                <div class="form-group">
                    <div class="ticket-section mb-4">
                        <label class="form-label fs-16">Define your tickets*</label>
                        <p class="mt-2 fs-14 d-block mb-3 pe_right">
                            {{ __('Create tickets for your event by clicking on the Add Tickets button.') }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-6 d-block mb-2">{{ __('Is this a free event?') }}*</label>
                        <div class="ef-attendance">
                            <label class="ef-att-card {{ old('free_event', 'false') === 'true' ? 'selected' : '' }}">
                                <input
                                    type="radio"
                                    name="free_event"
                                    value="true"
                                    {{ old('free_event', 'false') === 'true' ? 'checked' : '' }}
                                >
                                <div class="ef-att-icon">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="ef-att-info">
                                    <strong>{{ __('Yes') }}</strong>
                                    <span>{{ __('No ticket price') }}</span>
                                </div>
                            </label>

                            <label class="ef-att-card {{ old('free_event', 'false') !== 'true' ? 'selected' : '' }}">
                                <input
                                    type="radio"
                                    name="free_event"
                                    value="false"
                                    {{ old('free_event', 'false') !== 'true' ? 'checked' : '' }}
                                >
                                <div class="ef-att-icon">
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <div class="ef-att-info">
                                    <strong>{{ __('No') }}</strong>
                                    <span>{{ __('You will set ticket prices') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="tickets-shell">
                        <div class="d-flex align-items-center justify-content-between pt-4 pb-3 full-width">
                            <h3 class="fs-18 mb-0">{{ __('Tickets') }} (<span id="ticket-counter">0</span>)</h3>
                            <button
                                type="button"
                                class="ef-btn-secondary h_40 pe-4 ps-4"
                                id="addTicketBtn"
                                data-bs-toggle="modal"
                                data-bs-target="#addTicketModal"
                            >
                                <i class="fa-solid fa-plus me-2"></i>
                                <span>{{ __('Add Tickets') }}</span>
                            </button>
                        </div>

                        <div id="ticket-empty" class="ticket-type-item-empty text-center p_30">
                            <div class="ticket-list-icon d-inline-block">
                                <img src="{{ asset('template/images/ticket.png') }}" alt="">
                            </div>
                            <h4 class="color-black mt-4 mb-3 fs-18">{{ __('You have no tickets yet.') }}</h4>
                            <p class="mb-0">{{ __('Click Add Tickets above to create your first ticket.') }}</p>
                        </div>

                        <div id="ticket-list" class="ticket-type-item-list mt-4"></div>

                        <div id="ticket-hidden-inputs"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Add Ticket --}}
<div class="modal fade" id="addTicketModal" tabindex="-1" aria-labelledby="addTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTicketModalLabel">{{ __('Add Ticket') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">{{ __('Name') }}*</label>
                        <input type="text" class="form-control" id="ticket-name" maxlength="50">
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ __('Price') }}*</label>
                        <input type="number" class="form-control" id="ticket-price" step="0.01" value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ __('Online quantity') }}*</label>
                        <input type="number" class="form-control" id="ticket-online-qty" min="1" value="1">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Printed quantity') }}</label>
                        <input type="number" class="form-control" id="ticket-print-qty" min="0" value="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea class="form-control" id="ticket-description" rows="3" maxlength="200"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('General conditions') }}</label>
                        <textarea class="form-control" id="ticket-conditions" rows="3" maxlength="1000"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-dark" id="ticket-modal-add">{{ __('Add') }}</button>
            </div>
        </div>
    </div>
</div>
