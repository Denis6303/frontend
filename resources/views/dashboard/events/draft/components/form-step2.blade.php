<div class="step-tab-panel step-tab-gallery active" id="tab_step2">
    <div class="tab-from-content">
        <div class="main-card">
            <div class="bp-title">
                <h4><i class="fa-solid fa-ticket step_icon me-3"></i>Tickets</h4>
            </div>
            <div class="bp-form main-form">
                <div class="p-4 form-group border_bottom pb_30">
                    <div class="ticket-section">
                        <label class="form-label fs-16">Let's create tickets!</label>
                        <p class="mt-2 fs-14 d-block mb-3 pe_right">
                            Create tickets for your event by clicking on the 'Add Tickets' button below.
                        </p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-4 pb-3 full-width">
                        <h3 class="fs-18 mb-0">Tickets (<span class="venue-event-ticket-counter">3</span>)</h3>
                        <div class="dropdown dropdown-default dropdown-normal btn-ticket-type-top">
                            <button class="dropdown-toggle main-btn btn-hover h_40 pe-4 ps-4" type="button" id="dropdownMenuButton"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span>Add Tickets</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#singleTicketModal">
                                    <i class="fa-solid fa-ticket me-2"></i>
                                    Single Ticket
                                </a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#groupTicketModal">
                                    <i class="fa-solid fa-ticket me-2"></i>
                                    Group Ticket
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="ticket-type-item-empty d-none text-center p_30">
                        <div class="ticket-list-icon d-inline-block">
                            <img src="{{ asset('template/images/ticket.png') }}" alt="">
                        </div>
                        <h4 class="color-black mt-4 mb-3 fs-18">You have no tickets yet.</h4>
                        <p class="mb-0">
                            You have not created a ticket yet. Please click the button above to create your event ticket.
                        </p>
                    </div>

                    <div class="ticket-type-item-list mt-4">
                        <div class="price-ticket-card mt-4">
                            <div class="price-ticket-card-head d-md-flex flex-wrap align-items-start justify-content-between position-relative p-4">
                                <div class="d-flex align-items-center top-name">
                                    <div class="icon-box">
                                        <span class="icon-big rotate-icon icon icon-purple">
                                            <i class="fa-solid fa-ticket"></i>
                                        </span>
                                        <h5 class="fs-16 mb-1 mt-1">New Small - $10.00</h5>
                                        <p class="text-gray-50 m-0"><span class="visitor-date-time">May 3, 2022</span></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="price-badge">
                                        <img src="{{ asset('template/images/discount.png') }}" alt="">
                                    </div>
                                    <label class="btn-switch tfs-8 mb-0 me-4 mt-1">
                                        <input type="checkbox" value="" checked>
                                        <span class="checkbox-slider"></span>
                                    </label>
                                    <div class="dropdown dropdown-default dropdown-text dropdown-icon-item">
                                        <button class="option-btn-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="#" class="dropdown-item"><i class="fa-solid fa-pen me-3"></i>Edit</a>
                                            <a href="#" class="dropdown-item"><i class="fa-solid fa-trash-can me-3"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="price-ticket-card-body border_top p-4">
                                <div class="full-width d-flex flex-wrap justify-content-between align-items-center">
                                    <div class="icon-box">
                                        <div class="icon me-3">
                                            <i class="fa-solid fa-ticket"></i>
                                        </div>
                                        <span class="text-145">Total tickets</span>
                                        <h6 class="coupon-status">20</h6>
                                    </div>
                                    <div class="icon-box">
                                        <div class="icon me-3">
                                            <i class="fa-solid fa-users"></i>
                                        </div>
                                        <span class="text-145">Ticket limit per customer</span>
                                        <h6 class="coupon-status">2</h6>
                                    </div>
                                    <div class="icon-box">
                                        <div class="icon me-3">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </div>
                                        <span class="text-145">Discount</span>
                                        <h6 class="coupon-status">5%</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="price-ticket-card mt-4">
                            <div class="price-ticket-card-head d-md-flex flex-wrap align-items-start justify-content-between position-relative p-4">
                                <div class="d-flex align-items-center top-name">
                                    <div class="icon-box">
                                        <span class="icon-big rotate-icon icon icon-yellow">
                                            <i class="fa-solid fa-ticket"></i>
                                        </span>
                                        <h5 class="fs-16 mb-1 mt-1">Group - $10.00</h5>
                                        <p class="text-gray-50 m-0"><span class="visitor-date-time">May 3, 2022</span></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="price-badge">
                                        <img src="{{ asset('template/images/discount.png') }}" alt="">
                                    </div>
                                    <label class="btn-switch tfs-8 mb-0 me-4 mt-1">
                                        <input type="checkbox" value="" checked>
                                        <span class="checkbox-slider"></span>
                                    </label>
                                    <div class="dropdown dropdown-default dropdown-text dropdown-icon-item">
                                        <button class="option-btn-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="#" class="dropdown-item"><i class="fa-solid fa-pen me-3"></i>Edit</a>
                                            <a href="#" class="dropdown-item"><i class="fa-solid fa-trash-can me-3"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="price-ticket-card-body border_top p-4">
                                <div class="full-width d-flex flex-wrap justify-content-between align-items-center">
                                    <div class="icon-box">
                                        <div class="icon me-3">
                                            <i class="fa-solid fa-ticket"></i>
                                        </div>
                                        <span class="text-145">Total tickets</span>
                                        <h6 class="coupon-status">Unlimited</h6>
                                    </div>
                                    <div class="icon-box">
                                        <div class="icon me-3">
                                            <i class="fa-solid fa-users"></i>
                                        </div>
                                        <span class="text-145">Ticket limit per customer</span>
                                        <h6 class="coupon-status">Unlimited</h6>
                                    </div>
                                    <div class="icon-box">
                                        <div class="icon me-3">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </div>
                                        <span class="text-145">Discount</span>
                                        <h6 class="coupon-status">2%</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>