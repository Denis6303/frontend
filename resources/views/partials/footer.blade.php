<footer class="footer mt-auto">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-content">
                        <h4>{{ __('Company') }}</h4>
                        <ul class="footer-link-list">
                            <li><a href="{{ route('static.about', ['locale' => $locale ?? app()->getLocale()]) }}" class="footer-link">{{ __('About Us') }}</a></li>
                            <li><a href="{{ route('static.help', ['locale' => $locale ?? app()->getLocale()]) }}" class="footer-link">{{ __('Help Center') }}</a></li>
                            <li><a href="{{ route('static.faq', ['locale' => $locale ?? app()->getLocale()]) }}" class="footer-link">{{ __('FAQ') }}</a></li>
                            <li><a href="{{ route('contact', ['locale' => $locale ?? app()->getLocale()]) }}" class="footer-link">{{ __('Contact Us') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-content">
                        <h4>{{ __('Useful Links') }}</h4>
                        <ul class="footer-link-list">
                            <li><a href="{{ session(config('votix_api.session_access_token_key'))
                                ? route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale()])
                                : route('login', ['locale' => $locale ?? app()->getLocale(), 'redirect' => route('dashboard.events.draft.create.step1', ['locale' => $locale ?? app()->getLocale()])]) }}" class="footer-link">{{ __('Create Event') }}</a></li>
                            <li><a href="{{ route('static.sell', ['locale' => $locale ?? app()->getLocale()]) }}" class="footer-link">{{ __('Sell Tickets Online') }}</a></li>
                            <li><a href="{{ route('static.privacy', ['locale' => $locale ?? app()->getLocale()]) }}" class="footer-link">{{ __('Privacy Policy') }}</a></li>
                            <li><a href="{{ route('static.terms', ['locale' => $locale ?? app()->getLocale()]) }}" class="footer-link">{{ __('Terms & Conditions') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-content">
                        <h4>{{ __('Resources') }}</h4>
                        <ul class="footer-link-list">
                            <li><a href="{{ route('static.pricing', ['locale' => $locale ?? app()->getLocale()]) }}" class="footer-link">{{ __('Pricing') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-content">
                        <h4>{{ __('Follow Us') }}</h4>
                        <ul class="social-links">
                            <li><a href="#" class="social-link"><i class="fab fa-facebook-square"></i></a>
                            <li><a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <li><a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <li><a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            <li><a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="footer-copyright-text">
                        <p class="mb-0">© {{ date('Y') }}, <strong>Votix</strong>. {{ __('All rights reserved.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

