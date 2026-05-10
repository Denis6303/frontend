@php
    $cover = votix_media_url($event['cover_url'] ?? null) ?? ($defaultCover ?? asset('images/event-imgs/img-7.jpg'));
    $occ = $event['occurrences'][0] ?? null;
    $start = $occ['start_date'] ?? null;
    $city = $event['city'] ?? null;
    $address = $event['address'] ?? null;
    $views = $event['nb_visites'] ?? $event['views_count'] ?? $event['view_count'] ?? null;
    $privateRaw = $event['is_private'] ?? false;
    $isPrivate = filter_var($privateRaw, FILTER_VALIDATE_BOOLEAN);
    $locale = $locale ?? app()->getLocale();
    $activeTab = $activeTab ?? request('tab', 'upcoming');
    $eventId = $event['id'] ?? null;
    $status = $event['status'] ?? '';
    $cardContext = $cardContext ?? 'upcoming';

    $categoryLabels = [];
    $categoryLocale = $locale ?? app()->getLocale();
    if (! empty($event['categories']) && is_array($event['categories'])) {
        foreach ($event['categories'] as $cat) {
            if (is_array($cat)) {
                $n = $categoryLocale === 'en'
                    ? ($cat['name_en'] ?? $cat['name'] ?? null)
                    : ($cat['name'] ?? $cat['name_en'] ?? null);
                if ($n !== null && $n !== '') {
                    $categoryLabels[] = $n;
                }
            } elseif (is_string($cat) && $cat !== '') {
                $categoryLabels[] = $cat;
            }
        }
    }
    $categoriesDisplay = count($categoryLabels) ? implode(', ', $categoryLabels) : null;
    if ($categoriesDisplay === null) {
        $categoriesDisplay = $categoryLocale === 'en'
            ? ($event['category']['name_en'] ?? $event['category']['name'] ?? null)
            : ($event['category']['name'] ?? $event['category']['name_en'] ?? null);
    }

    $canUnpublish = $cardContext === 'upcoming' && $status === 'upcoming' && $eventId;
    $canCancel = $cardContext === 'upcoming' && $status === 'upcoming' && $eventId;
@endphp

<div class="main-card dashboard-my-event-card h-100 d-flex flex-column">
    <div class="contact-list flex-grow-1 d-flex flex-column">
        <div class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between flex-grow-1">
            <div class="d-md-flex align-items-start event-top-info w-100">
                <div class="card-event-img flex-shrink-0">
                    <img src="{{ $cover }}" alt="">
                </div>
                <div class="card-event-dt flex-grow-1 min-w-0 ps-md-3 mt-3 mt-md-0">
                    <h5 class="mb-0">{{ $event['title'] ?? '—' }}</h5>

                    <div class="dashboard-event-meta-row small mt-3">
                        <div class="dashboard-event-meta-item">
                            <div class="dashboard-event-meta-label">{{ __('Start') }}</div>
                            <div class="dashboard-event-meta-value" title="{{ $start ? \Carbon\Carbon::parse($start)->translatedFormat('d M Y H:i') : '' }}">
                                @if($start)
                                    {{ \Carbon\Carbon::parse($start)->translatedFormat('d M Y H:i') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="dashboard-event-meta-item">
                            <div class="dashboard-event-meta-label">{{ __('City') }}</div>
                            <div class="dashboard-event-meta-value" title="{{ $city }}">{{ $city !== null && $city !== '' ? $city : '—' }}</div>
                        </div>
                        <div class="dashboard-event-meta-item">
                            <div class="dashboard-event-meta-label">{{ __('Address') }}</div>
                            <div class="dashboard-event-meta-value" title="{{ $address }}">{{ $address !== null && $address !== '' ? $address : '—' }}</div>
                        </div>
                        <div class="dashboard-event-meta-item">
                            <div class="dashboard-event-meta-label">{{ __('Category') }}</div>
                            <div class="dashboard-event-meta-value" title="{{ $categoriesDisplay }}">{{ $categoriesDisplay !== null && $categoriesDisplay !== '' ? $categoriesDisplay : '—' }}</div>
                        </div>
                        <div class="dashboard-event-meta-item">
                            <div class="dashboard-event-meta-label">{{ __('Visibility') }}</div>
                            <div class="dashboard-event-meta-value">{{ $isPrivate ? __('Private') : __('Public') }}</div>
                        </div>
                        @if($cardContext !== 'draft')
                            <div class="dashboard-event-meta-item">
                                <div class="dashboard-event-meta-label">{{ __('Views') }}</div>
                                <div class="dashboard-event-meta-value">{{ is_numeric($views) ? number_format((int) $views, 0, ',', ' ') : '—' }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom dashboard-event-actions p-4 d-flex flex-wrap align-items-center gap-2 mt-auto">
            @if($cardContext === 'past')
                @if($eventId)
                    <a href="{{ route('dashboard.events.revenues', ['locale' => $locale, 'event' => $eventId, 'tab' => $activeTab, 'page_completed' => request('page_completed')]) }}" class="btn btn-sm btn-outline-dark dashboard-event-action-link dashboard-event-action-btn">{{ __('Receipts') }}</a>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">{{ __('Receipts') }}</span>
                @endif
            @elseif($cardContext === 'draft')
                @if($eventId)
                    <a href="{{ route('dashboard.events.resume-draft', ['locale' => $locale, 'event' => $eventId, 'tab' => $activeTab, 'page_saved' => request('page_saved')]) }}" class="btn btn-sm btn-outline-dark dashboard-event-action-link dashboard-event-action-btn">{{ __('Continue') }}</a>
                    <form method="post" action="{{ route('dashboard.events.destroy-draft', ['locale' => $locale, 'event' => $eventId, 'tab' => $activeTab, 'page_saved' => request('page_saved')]) }}" class="d-inline" onsubmit="return confirm(@json(__('Are you sure you want to delete this draft?')));">
                        @csrf
                        <input type="hidden" name="tab" value="{{ $activeTab }}">
                        <input type="hidden" name="page_saved" value="{{ request('page_saved', 1) }}">
                        <input type="hidden" name="query" value="{{ request('query') }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                    </form>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">{{ __('Continue') }}</span>
                @endif
            @else
                {{-- À venir --}}
                @if($canUnpublish)
                    <form method="post" action="{{ route('dashboard.events.unpublish', ['locale' => $locale, 'event' => $eventId, 'tab' => $activeTab]) }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="tab" value="{{ $activeTab }}">
                        <input type="hidden" name="page_upcoming" value="{{ request('page_upcoming', 1) }}">
                        <input type="hidden" name="query" value="{{ request('query') }}">
                        <button type="submit" class="btn btn-sm btn-outline-dark dashboard-event-action-btn">{{ __('Unpublish') }}</button>
                    </form>
                @endif

                @if($eventId)
                    <a href="{{ route('dashboard.events.revenues', ['locale' => $locale, 'event' => $eventId, 'tab' => $activeTab, 'page_upcoming' => request('page_upcoming')]) }}" class="btn btn-sm btn-outline-dark dashboard-event-action-link dashboard-event-action-btn">{{ __('Receipts') }}</a>
                    <a href="{{ route('dashboard.events.edit', ['locale' => $locale, 'event' => $eventId, 'tab' => $activeTab, 'page_upcoming' => request('page_upcoming')]) }}" class="btn btn-sm btn-outline-dark dashboard-event-action-link dashboard-event-action-btn">{{ __('Edit') }}</a>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">{{ __('Receipts') }}</span>
                    <span class="btn btn-sm btn-outline-secondary disabled">{{ __('Edit') }}</span>
                @endif

                @if($canCancel)
                    <form method="post" action="{{ route('dashboard.events.cancel', ['locale' => $locale, 'event' => $eventId, 'tab' => $activeTab]) }}" class="d-inline" onsubmit="return confirm(@json(__('Are you sure you want to cancel this event?')));">
                        @csrf
                        <input type="hidden" name="tab" value="{{ $activeTab }}">
                        <input type="hidden" name="page_upcoming" value="{{ request('page_upcoming', 1) }}">
                        <input type="hidden" name="query" value="{{ request('query') }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Cancel') }}</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
