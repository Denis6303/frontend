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
                            <input class="form-control h_50" type="text" placeholder="Enter event name here" value="">
                        </div>

                        <div class="form-group border_bottom pt_30 pb_30">
                            <label class="form-label fs-16">Choose a category for your event.*</label>
                            <p class="mt-2 d-block fs-14 mb-3">
                                Choosing relevant categories helps to improve the discoverability of your event.
                                <a href="#" class="a-link">Learn more</a>
                            </p>
                            <select class="selectpicker" multiple data-selected-text-format="count > 4" data-size="5" title="Select category" data-live-search="true">
                                <option value="01">Arts</option>
                                <option value="02">Business</option>
                                <option value="03">Community and Culture</option>
                                <option value="04">Education and Training</option>
                                <option value="05">Food and Drink</option>
                                <option value="06">Music and Theater</option>
                                <option value="07">Sports and Fitness</option>
                                <option value="08">Others</option>
                            </select>
                        </div>

                        <div class="form-group border_bottom pt_30 pb_30">
                            <label class="form-label fs-16">When is your event?*</label>
                            <p class="mt-2 fs-14 d-block mb-3">Tell your attendees when your event starts so they can get ready to attend.</p>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label mt-3 fs-6">Event Date.*</label>
                                    <div class="loc-group position-relative">
                                        <input class="form-control h_50 datepicker-here" data-language="en" type="text" placeholder="MM/DD/YYYY" value="">
                                        <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="clock-icon">
                                                <label class="form-label mt-3 fs-6">Time</label>
                                                <select class="selectpicker" data-size="5" data-live-search="true">
                                                    <option value="10:00" selected>10:00 AM</option>
                                                    <option value="11:00">11:00 AM</option>
                                                    <option value="12:00">12:00 PM</option>
                                                    <option value="13:00">01:00 PM</option>
                                                    <option value="14:00">02:00 PM</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label mt-3 fs-6">Duration</label>
                                            <select class="selectpicker" data-size="5" data-live-search="true">
                                                <option value="60" selected>1h</option>
                                                <option value="75">1h 15m</option>
                                                <option value="90">1h 30m</option>
                                                <option value="120">2h</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                            <input type="file" id="thumb-img">
                                            <label for="thumb-img">Change Image</label>
                                        </div>
                                    </div>
                                    <img src="{{ asset('template/images/banner.jpg') }}" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="form-group border_bottom pb_30">
                            <label class="form-label fs-16">Please describe your event.</label>
                            <p class="mt-2 fs-14 d-block mb-3">
                                Write a few words below to describe your event and provide any extra information such as schedules, itinerary or any special instructions required to attend your event.
                            </p>
                            <div class="text-editor mt-4">
                                <div id="pd_editor"></div>
                            </div>
                        </div>

                        <div class="form-group pt_30 pb-2">
                            <label class="form-label fs-16">Where is your event taking place? *</label>
                            <p class="mt-2 fs-14 d-block mb-3">Add a venue to your event to tell your attendees where to join the event.</p>
                            <div class="stepper-data-set">
                                <div class="content-holder template-selector">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <div class="venue-event">
                                                <div class="map">
                                                    <iframe src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d27382.59422947023!2d75.84077125074462!3d30.919535510612153!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1534312417365" style="border:0" allowfullscreen></iframe>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mt-1">
                                                <label class="form-label fs-6">Venue*</label>
                                                <input class="form-control h_50" type="text" placeholder="" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mt-1">
                                                <label class="form-label fs-6">Address line 1*</label>
                                                <input class="form-control h_50" type="text" placeholder="" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mt-1">
                                                <label class="form-label fs-6">Address line 2*</label>
                                                <input class="form-control h_50" type="text" placeholder="" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group main-form mt-1">
                                                <label class="form-label">Country*</label>
                                                <select class="selectpicker" data-size="5" title="Nothing selected" data-live-search="true">
                                                    <option value="France">France</option>
                                                    <option value="Germany">Germany</option>
                                                    <option value="United Kingdom">United Kingdom</option>
                                                    <option value="United States">United States</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mt-1">
                                                <label class="form-label">State*</label>
                                                <input class="form-control h_50" type="text" placeholder="" value="">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-1">
                                                <label class="form-label">City/Suburb*</label>
                                                <input class="form-control h_50" type="text" placeholder="" value="">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-1">
                                                <label class="form-label">Zip/Post Code*</label>
                                                <input class="form-control h_50" type="text" placeholder="" value="">
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
    </div>
</div>