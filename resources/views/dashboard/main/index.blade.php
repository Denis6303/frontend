@extends('dashboard.layouts.base')

@section('title', __('Dashboard'))

@php
    $s = is_array($stats) ? $stats : [];
    $currency = $s['currency'] ?? 'XOF';
    $currencyLabel = strtoupper((string) $currency) === 'XOF' ? 'FCFA' : (string) $currency;
    $fmtInt = static fn ($n): string => number_format((int) round((float) $n), 0, ',', ' ');
    $rev = $s['summary']['revenue'] ?? ['value' => 0, 'change_percent' => 0];
    $ord = $s['summary']['orders'] ?? ['value' => 0, 'change_percent' => 0];
    $pv = $s['summary']['page_views'] ?? ['value' => 0, 'change_percent' => 0];
    $ts = $s['summary']['ticket_sales'] ?? ['value' => 0, 'change_percent' => 0];
    $chart = $s['chart'] ?? ['labels' => [], 'values' => []];
    $chartLabels = $chart['labels'] ?? [];
    $chartValues = $chart['values'] ?? [];
    $selectedCount = $s['events_selected_count'] ?? (count($selectedEventIds) > 0 ? count($selectedEventIds) : max(0, count($events)));
    $startLabel = \Carbon\Carbon::parse($startDate)->locale($locale)->translatedFormat('j F Y');
    $endLabel = \Carbon\Carbon::parse($endDate)->locale($locale)->translatedFormat('j F Y');
    $dashboardUrl = function (array $override) use ($locale, $startDate, $endDate, $granularity, $chartMetric, $selectedEventIds) {
        $q = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'granularity' => $granularity,
            'chart_metric' => $chartMetric,
        ];
        if ($selectedEventIds !== []) {
            $q['event_ids'] = $selectedEventIds;
        }
        return route('dashboard.home', array_merge(['locale' => $locale], array_merge($q, $override)));
    };
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-card add-organisation-card p-2 mt-2">
                <div class="ocard-left">
                    <div class="ocard-avatar">
                        <img src="{{ asset('images/profile-imgs/img-13.jpg') }}" alt="">
                    </div>
                    <div class="ocard-name">
                        <h4>{{ auth_user_display_name() }}</h4>
                        <span>{{ __('My dashboard') }}</span>
                    </div>
                </div>
                <div class="ocard-right">
                    <a href="{{ route('dashboard.account', ['locale' => $locale]) }}" class="pe-4 ps-4 co-main-btn min-width d-inline-flex align-items-center justify-content-center text-decoration-none"><i class="fa-solid fa-plus me-1"></i>{{ __('Update profile') }}</a>
                </div>
            </div>
            <div class="main-card mt-4">
                <div class="dashboard-wrap-content">
                    <div class="d-flex flex-wrap justify-content-between align-items-center p-4">
                        <div class="dashboard-date-wrap d-flex flex-wrap justify-content-between align-items-center">
                            <div class="dashboard-date-arrows">
                                <a href="{{ $dashboardUrl($periodNav['prev']) }}" class="before_date"><i class="fa-solid fa-angle-left"></i></a>
                                <a href="{{ $periodNav['can_shift_next'] ? $dashboardUrl($periodNav['next']) : '#' }}" class="after_date {{ $periodNav['can_shift_next'] ? '' : 'disabled' }}"><i class="fa-solid fa-angle-right"></i></a>
                            </div>
                            <h5 class="dashboard-select-date">
                                <span>{{ $startLabel }}</span>
                                -
                                <span>{{ $endLabel }}</span>
                            </h5>
                        </div>
                        <div class="rs">
                            <form method="get" action="{{ route('dashboard.home', ['locale' => $locale]) }}" id="dashboard-event-filter" class="d-inline">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <input type="hidden" name="granularity" value="{{ $granularity }}">
                                <input type="hidden" name="chart_metric" value="{{ $chartMetric }}">
                                <div class="dropdown dropdown-text event-list-dropdown">
                                    <button class="dropdown-toggle event-list-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span>{{ __('Selected Events') }} ({{ $selectedCount }})</span>
                                    </button>
                                    <ul class="dropdown-menu p-3" style="min-width: 280px;" onclick="event.stopPropagation();">
                                        <li class="mb-2 small text-muted">{{ __('All events') }} = {{ __('Selected Events') }} ({{ count($events) }})</li>
                                        @foreach($events as $ev)
                                            @php $eid = (int) ($ev['id'] ?? 0); @endphp
                                            @if($eid > 0)
                                                <li class="form-check mb-1">
                                                    <input class="form-check-input dashboard-event-cb" type="checkbox" name="event_ids[]" value="{{ $eid }}" id="ev-{{ $eid }}"
                                                        {{ in_array($eid, $selectedEventIds, true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="ev-{{ $eid }}">{{ $ev['title'] ?? ('#'.$eid) }}</label>
                                                </li>
                                            @endif
                                        @endforeach
                                        <li class="mt-2 pt-2 border-top">
                                            <button type="submit" class="btn btn-sm btn-primary w-100">{{ __('Apply') }}</button>
                                        </li>
                                    </ul>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="dashboard-report-content">
                        <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="dashboard-report-card purple">
                                    <div class="card-content">
                                        <div class="card-content">
                                            <span class="card-title fs-6">{{ __('Revenue') }} ({{ $currencyLabel }})</span>
                                            <span class="card-sub-title fs-3">{{ $fmtInt($rev['value']) }}</span>
                                        </div>
                                        <div class="card-media">
                                            <i class="fa-solid fa-money-bill"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="dashboard-report-card red">
                                    <div class="card-content">
                                        <div class="card-content">
                                            <span class="card-title fs-6">{{ __('Orders') }}</span>
                                            <span class="card-sub-title fs-3">{{ $fmtInt($ord['value']) }}</span>
                                        </div>
                                        <div class="card-media">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="dashboard-report-card info">
                                    <div class="card-content">
                                        <div class="card-content">
                                            <span class="card-title fs-6">{{ __('Page Views') }}</span>
                                            <span class="card-sub-title fs-3">{{ $fmtInt($pv['value']) }}</span>
                                        </div>
                                        <div class="card-media">
                                            <i class="fa-solid fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="dashboard-report-card success">
                                    <div class="card-content">
                                        <div class="card-content">
                                            <span class="card-title fs-6">{{ __('Ticket Sales') }}</span>
                                            <span class="card-sub-title fs-3">{{ $fmtInt($ts['value']) }}</span>
                                        </div>
                                        <div class="card-media">
                                            <i class="fa-solid fa-ticket"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-card mt-4">
                <div class="d-flex flex-wrap justify-content-end align-items-center border_bottom p-4">
                    <div class="rs">
                        <div class="btn-group" role="group" aria-label="Granularity">
                            <a href="{{ $dashboardUrl(['granularity' => 'monthly']) }}" class="btn btn-outline-primary {{ $granularity === 'monthly' ? 'active' : '' }}">{{ __('Monthly') }}</a>
                            <a href="{{ $dashboardUrl(['granularity' => 'weekly']) }}" class="btn btn-outline-primary {{ $granularity === 'weekly' ? 'active' : '' }}">{{ __('Weekly') }}</a>
                            <a href="{{ $dashboardUrl(['granularity' => 'daily']) }}" class="btn btn-outline-primary {{ $granularity === 'daily' ? 'active' : '' }}">{{ __('Daily') }}</a>
                        </div>
                    </div>
                </div>
                <div class="item-analytics-content p-4 ps-1 pb-2">
                    <div id="views-graphic" data-votix-dashboard-chart="1"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(! empty($apiError))
    if (window.VotixFeedback) {
        VotixFeedback.show({ type: 'error', title: @json(__('Error')), message: @json($apiError) });
    }
    @endif
    var el = document.getElementById('views-graphic');
    if (!el || el.getAttribute('data-votix-dashboard-chart') !== '1') return;
    var labels = @json($chartLabels);
    var values = @json($chartValues);
    if (!labels.length) {
        labels = ['—'];
        values = [0];
    }
    if (typeof Chartist === 'undefined') return;
    new Chartist.Line('#views-graphic', {
        labels: labels,
        series: [values]
    }, {
        low: 0,
        showArea: true,
        fullWidth: true,
        distributeSeries: true
    });
});
</script>
@endpush
