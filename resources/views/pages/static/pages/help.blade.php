<div class="static-page text-body-secondary lh-lg">
    <p class="lead text-body">{{ __('static.help.lead') }}</p>
    <p>{{ __('static.help.p1') }}</p>
    <ul class="mb-4">
        <li><a href="{{ route('static.faq', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('FAQ') }}</a> — {{ __('static.help.link_faq') }}</li>
        <li><a href="{{ route('contact', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('Contact Us') }}</a> — {{ __('static.help.link_contact') }}</li>
        <li><a href="{{ route('static.about', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('About Us') }}</a> — {{ __('static.help.link_about') }}</li>
    </ul>
    <p class="mb-0">{{ __('static.help.p2') }}</p>
</div>
