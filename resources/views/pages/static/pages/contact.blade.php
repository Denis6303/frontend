<div class="static-page text-body-secondary lh-lg">
    <p class="lead text-body">{{ __('static.contact.lead') }}</p>
    <p>{{ __('static.contact.p1') }}</p>
    <p class="mb-2"><strong>{{ __('Email') }}:</strong>
        <a href="mailto:{{ config('mail.from.address', 'contact@votix.com') }}">{{ config('mail.from.address', 'contact@votix.com') }}</a>
    </p>
    <p class="mb-0">{{ __('static.contact.p2') }}</p>
</div>
