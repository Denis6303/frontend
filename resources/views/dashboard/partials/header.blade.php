{{-- Add Organisation Modal --}}
<div class="modal fade" id="addorganisationModal" tabindex="-1" aria-labelledby="addorganisationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addorganisationLabel">{{ __('Organisation details') }}</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="uil uil-multiply"></i></button>
            </div>
            <div class="modal-body">
                <div class="model-content main-form">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group text-center mt-4">
                                <label class="form-label">Avatar*</label>
                                <span class="org_design_button btn-file">
                                    <span><i class="fa-solid fa-camera"></i></span>
                                    <input type="file" id="org_avatar" accept="image/*" name="Organisation_avatar">
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('Name') }}*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('Profile Link') }}*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="https://www.barren.com/b/organiser/" disabled>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('About') }}*</label>
                                <textarea class="form-textarea" placeholder="">{{ __('About') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('Email') }}*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('Phone') }}*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('Website') }}*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">Facebook*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">Instagram*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">Twitter*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">LinkedIn*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">Youtube*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <h4 class="address-title">{{ __('Address') }}</h4>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('Address') }} 1*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('Address') }} 2*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group main-form mt-4">
                                <label class="form-label">{{ __('Country') }}*</label>
                                <select class="selectpicker" data-size="5" title="{{ __('Nothing selected') }}" data-live-search="true">
                                    <option value="France">France</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                    <option value="Germany">Germany</option>
                                    <option value="Belgium">Belgium (België)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('State') }}*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('City') }}/{{ __('Suburb') }}*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{ __('Zip') }}/{{ __('Post Code') }}*</label>
                                <input class="form-control h_40" type="text" placeholder="" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="co-main-btn min-width btn-hover h_40" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="main-btn min-width btn-hover h_40">{{ __('Add') }}</button>
            </div>
        </div>
    </div>
</div>

<header class="header">
    <div class="header-inner">
        <nav class="navbar navbar-expand-lg bg-barren barren-head navbar fixed-top justify-content-sm-start pt-0 pb-0 ps-lg-0 pe-2">
            <div class="container-fluid ps-0">
                <button type="button" id="toggleMenu" class="toggle_menu">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <button id="collapse_menu" class="collapse_menu me-4">
                    <i class="fa-solid fa-bars collapse_menu--icon"></i>
                    <span class="collapse_menu--label"></span>
                </button>
                <button class="navbar-toggler order-3 ms-2 pe-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                    <span class="navbar-toggler-icon">
                        <i class="fa-solid fa-bars"></i>
                    </span>
                </button>
                <a class="navbar-brand order-1 order-lg-0 ml-lg-0 ml-2 me-auto" href="{{ route('home', ['locale' => $locale ?? app()->getLocale()]) }}">
                    <div class="res-main-logo">
                        <img src="{{ asset('template/images/logo-icon.svg') }}" alt="">
                    </div>
                    <div class="main-logo" id="logo">
                        <img src="{{ asset('template/images/logo.svg') }}" alt="">
                        <img class="logo-inverse" src="{{ asset('template/images/dark-logo.svg') }}" alt="">
                    </div>
                </a>
                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <div class="offcanvas-logo" id="offcanvasNavbarLabel">
                            <img src="{{ asset('template/images/logo-icon.svg') }}" alt="">
                        </div>
                        <button type="button" class="close-btn" data-bs-dismiss="offcanvas" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="offcanvas-top-area">
                            <div class="create-bg">
                                <a href="#" class="offcanvas-create-btn">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>{{ __('Create Event') }}</span>
                                </a>
                            </div>
                        </div>
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe_5">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home', ['locale' => $locale ?? app()->getLocale()]) }}">
                                    <i class="fa-solid fa-right-left me-2"></i>{{ __('My Home') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('ticketing.events', ['locale' => $locale ?? app()->getLocale()]) }}">
                                    <i class="fa-solid fa-compass me-2"></i>{{ __('Explore Events') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="offcanvas-footer">
                        <div class="offcanvas-social">
                            <h5>{{ __('Follow Us') }}</h5>
                            <ul class="social-links">
                                <li><a href="#" class="social-link"><i class="fab fa-facebook-square"></i></a></li>
                                <li><a href="#" class="social-link"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="#" class="social-link"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="#" class="social-link"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="right-header order-2">
                    <ul class="align-self-stretch">
                        <li>
                            <a href="#" class="create-btn btn-hover">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>{{ __('Create Event') }}</span>
                            </a>
                        </li>
                        <li class="dropdown account-dropdown order-3">
                            <a href="#" class="account-link" role="button" id="accountClick" data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset('template/images/profile-imgs/img-13.jpg') }}" alt="">
                                <i class="fas fa-caret-down arrow-icon"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-account dropdown-menu-end" aria-labelledby="accountClick">
                                <li>
                                    <div class="dropdown-account-header">
                                        <div class="account-holder-avatar">
                                            <img src="{{ asset('template/images/profile-imgs/img-13.jpg') }}" alt="">
                                        </div>
                                        <h5>John Doe</h5>
                                        <p>johndoe@example.com</p>
                                    </div>
                                </li>
                                <li class="profile-link">
                                    <a href="#" class="link-item">{{ __('My Profile') }}</a>
                                    <form method="POST" action="{{ route('logout', ['locale' => $locale ?? app()->getLocale()]) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="link-item border-0 bg-transparent p-0">{{ __('Sign Out') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="night_mode_switch__btn">
                                <div id="night-mode" class="fas fa-moon fa-sun"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="overlay"></div>
    </div>
</header>
