<div class="static-page text-body-secondary lh-lg">
    <p class="lead text-body">{{ __('static.sell.lead') }}</p>
    <p>{{ __('static.sell.p1') }}</p>
    <ul>
        <li>{{ __('static.sell.li1') }}</li>
        <li>{{ __('static.sell.li2') }}</li>
        <li>{{ __('static.sell.li3') }}</li>
        <li>{{ __('static.sell.li4') }}</li>
    </ul>
    <p class="mb-0">
        <a href="{{ route('register', ['locale' => $locale ?? app()->getLocale()]) }}" class="fw-semibold">{{ __('Register') }}</a>
        {{ __('static.sell.cta_suffix') }}
    </p>
</div>
