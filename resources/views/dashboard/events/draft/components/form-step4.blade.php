<div class="step-tab-panel active" id="tab_step4">
    <div class="tab-from-content">
        <div class="main-card">
            <div class="p_30 bp-form main-form">

                {{-- ============================================================
                     Section Résumé du brouillon
                     ============================================================ --}}
                <div class="ef-summary-section">
                    <h5 class="ef-summary-section-title">
                        <i class="fa-regular fa-file-lines"></i>
                        {{ __('Review your draft') }}
                    </h5>

                    @php
                        $draft      = $draft ?? [];
                        $event      = $draft['event'] ?? [];
                        $data       = $draft['data'] ?? [];
                        $tickets    = $data['tickets'] ?? [];
                        $startDates = $data['start_dates'] ?? [];
                        $endDates   = $data['end_dates'] ?? [];
                        $freeEvent  = filter_var($data['free_event'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    @endphp

                    @if(!empty($event) || !empty($tickets))

                        {{-- Grille 50/50 : gauche = titre + méta + image, droite = cartes (stats + tickets) --}}
                        <div class="ef-summary-layout">
                            {{-- Colonne gauche : titre, date, devise, puis image en dessous --}}
                            <div class="ef-summary-left-col">
                                <div class="ef-summary-info">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <div>
                                            @if(!empty($event['title']))
                                                <p class="ef-event-title">{{ $event['title'] }}</p>
                                            @endif
                                        </div>
                                        <span class="ef-badge-draft">{{ __('Draft') }}</span>
                                    </div>
                                    <div class="ef-event-meta">
                                        @if(!empty($startDates))
                                            <span class="ef-meta-pill">
                                                <i class="fa-regular fa-calendar"></i>
                                                @foreach($startDates as $i => $start)
                                                    {{ \Carbon\Carbon::parse($start)->translatedFormat('d M Y · H:i') }}
                                                    @if(isset($endDates[$i]))
                                                        → {{ \Carbon\Carbon::parse($endDates[$i])->translatedFormat('H:i') }}
                                                    @endif
                                                    @if($i < count($startDates) - 1) &nbsp;·&nbsp; @endif
                                                @endforeach
                                            </span>
                                        @endif
                                        @if(!empty($event['city']) || !empty($event['address']))
                                            <span class="ef-meta-pill">
                                                <i class="fa-solid fa-location-dot"></i>
                                                {{ trim(implode(', ', array_filter([$event['city'] ?? '', $event['address'] ?? '']))) }}
                                            </span>
                                        @endif
                                        @if(!empty($event['currency']))
                                            <span class="ef-meta-pill">
                                                <i class="fa-solid fa-coins"></i>
                                                {{ $event['currency'] }} &nbsp;·&nbsp; {{ $freeEvent ? __('Free') : __('Paid') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ef-summary-cover-col">
                                    @if(!empty($draft['cover_url']))
                                        <img src="{{ $draft['cover_url'] }}" alt="{{ $event['title'] ?? '' }}">
                                    @else
                                        <div class="ef-summary-cover-placeholder">
                                            <i class="fa-regular fa-image fa-2x"></i>
                                            {{ __('No banner') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Colonne droite : cartes stats + tickets --}}
                            <div class="ef-summary-right-col">
                                <div class="ef-summary-footer">

                            {{-- Statistiques rapides --}}
                            <div class="ef-stats-grid">
                                @if(!empty($event['currency']))
                                    <div class="ef-stat-card">
                                        <div class="ef-stat-label">{{ __('Currency') }}</div>
                                        <div class="ef-stat-value">{{ $event['currency'] }}</div>
                                    </div>
                                @endif
                                <div class="ef-stat-card">
                                    <div class="ef-stat-label">{{ __('Free event') }}</div>
                                    <div class="ef-stat-value">{{ $freeEvent ? __('Yes') : __('No') }}</div>
                                </div>
                                @if(!empty($tickets))
                                    <div class="ef-stat-card">
                                        <div class="ef-stat-label">{{ __('Ticket types') }}</div>
                                        <div class="ef-stat-value">{{ count($tickets) }}</div>
                                    </div>
                                    <div class="ef-stat-card">
                                        <div class="ef-stat-label">{{ __('Total online') }}</div>
                                        <div class="ef-stat-value">
                                            {{ collect($tickets)->sum(fn($t) => intval($t['online_quantity'] ?? 0)) }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Liste des tickets (3 par ligne) --}}
                            @if(!empty($tickets))
                                <p class="ef-tickets-heading">{{ __('Tickets') }}</p>
                                <div class="ef-tickets-grid">
                                @foreach($tickets as $t)
                                    <div class="ef-ticket-item">
                                        <div>
                                            <div class="ef-ticket-name">{{ $t['name'] ?? '—' }}</div>
                                            <div class="ef-ticket-detail">
                                                {{ __('Online quantity') }}: {{ $t['online_quantity'] ?? 0 }}
                                                @if(!empty($t['print_quantity']))
                                                    &nbsp;·&nbsp; {{ __('Printed quantity') }}: {{ $t['print_quantity'] }}
                                                @endif
                                            </div>
                                            @if(!empty($t['description']))
                                                <div class="ef-ticket-desc">
                                                    {{ Str::limit(strip_tags($t['description']), 90) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ef-ticket-price">
                                            @if($freeEvent)
                                                {{ __('Free') }}
                                            @else
                                                {{ number_format($t['price'] ?? 0, 0, ',', ' ') }}
                                                {{ $event['currency'] ?? '' }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            @endif

                                </div>{{-- /ef-summary-footer --}}
                            </div>{{-- /ef-summary-right-col --}}
                        </div>{{-- /ef-summary-layout --}}

                        {{-- Description seule, tout en bas --}}
                        @if(!empty($event['description']))
                            <div class="ef-summary-desc-bottom">
                                <button
                                    class="ef-desc-toggle open"
                                    id="efDescToggle"
                                    type="button"
                                    onclick="
                                        this.classList.toggle('open');
                                        document.getElementById('efDescBody').classList.toggle('open');
                                    "
                                >
                                    <span>{{ __('Description') }}</span>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <div class="ef-desc-body open" id="efDescBody">
                                    {{ strip_tags($event['description']) }}
                                </div>
                            </div>
                        @endif

                    @else
                        <p class="ef-summary-empty">
                            <i class="fa-regular fa-circle-dot me-1"></i>
                            {{ __('Complete the previous steps to see the summary here.') }}
                        </p>
                    @endif

                </div>{{-- /ef-summary-section --}}


                {{-- ============================================================
                     Section Options de publication
                     ============================================================ --}}
                <div class="ef-publish-section">
                    <h5 class="ef-publish-section-title">{{ __('Publication options') }}</h5>

                    <div class="mb-3">
                        <label class="form-label d-block mb-2">{{ __('Publish now?') }}*</label>
                        <div class="ef-attendance">
                            <label class="ef-att-card {{ old('publish_now', 'true') === 'true' ? 'selected' : '' }}">
                                <input
                                    type="radio"
                                    name="publish_now"
                                    id="publish_now_yes"
                                    value="true"
                                    {{ old('publish_now', 'true') === 'true' ? 'checked' : '' }}
                                >
                                <div class="ef-att-icon">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <div class="ef-att-info">
                                    <strong>{{ __('Yes') }}</strong>
                                    <span>{{ __('Publish immediately') }}</span>
                                </div>
                            </label>

                            <label class="ef-att-card {{ old('publish_now') === 'false' ? 'selected' : '' }}">
                                <input
                                    type="radio"
                                    name="publish_now"
                                    id="publish_now_no"
                                    value="false"
                                    {{ old('publish_now') === 'false' ? 'checked' : '' }}
                                >
                                <div class="ef-att-icon">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div class="ef-att-info">
                                    <strong>{{ __('No, schedule or save') }}</strong>
                                    <span>{{ __('Plan publication later') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="schedule-at-wrapper" class="mb-3" style="display:none">
                        <label class="form-label">{{ __('Schedule at') }}</label>
                        <div class="loc-group position-relative">
                            <input
                                type="text"
                                class="form-control h_50"
                                name="scheduled_at"
                                id="scheduled_at"
                                placeholder="YYYY-MM-DD HH:MM"
                                value="{{ old('scheduled_at') }}"
                                autocomplete="off"
                            >
                            <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label d-block mb-2">{{ __('Private event?') }}*</label>
                        <div class="ef-attendance">
                            <label class="ef-att-card {{ old('is_private', 'false') !== 'true' ? 'selected' : '' }}">
                                <input
                                    type="radio"
                                    name="is_private"
                                    id="is_private_no"
                                    value="false"
                                    {{ old('is_private', 'false') !== 'true' ? 'checked' : '' }}
                                >
                                <div class="ef-att-icon">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div class="ef-att-info">
                                    <strong>{{ __('No') }}</strong>
                                    <span>{{ __('Public event') }}</span>
                                </div>
                            </label>

                            <label class="ef-att-card {{ old('is_private') === 'true' ? 'selected' : '' }}">
                                <input
                                    type="radio"
                                    name="is_private"
                                    id="is_private_yes"
                                    value="true"
                                    {{ old('is_private') === 'true' ? 'checked' : '' }}
                                >
                                <div class="ef-att-icon">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <div class="ef-att-info">
                                    <strong>{{ __('Yes') }}</strong>
                                    <span>{{ __('Only invited people') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>{{-- /ef-publish-section --}}

            </div>
        </div>
    </div>
</div>