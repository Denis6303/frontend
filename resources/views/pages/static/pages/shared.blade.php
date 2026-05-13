<div class="static-page text-body-secondary lh-lg">
    @if (!empty($summary))
        <p class="lead text-body mb-4">{{ $summary }}</p>
    @endif

    @if (!empty($cards) && is_array($cards))
        <div class="row g-3 mb-4">
            @foreach ($cards as $card)
                @php
                    $title = $card['title'] ?? null;
                    $text = $card['text'] ?? null;
                    $href = $card['href'] ?? null;
                    $linkLabel = $card['link_label'] ?? null;
                    $icon = $card['icon'] ?? 'fa-circle-info';
                @endphp
                <div class="col-12 col-md-6">
                    <div class="h-100 p-3 border rounded-3 bg-white">
                        <div class="d-flex align-items-start gap-2">
                            <span class="text-dark" aria-hidden="true"><i class="fa-solid {{ $icon }}"></i></span>
                            <div class="flex-grow-1">
                                @if (is_string($title) && $title !== '')
                                    <p class="fw-semibold text-body mb-1">{{ $title }}</p>
                                @endif
                                @if (is_string($text) && $text !== '')
                                    <p class="mb-2">{{ $text }}</p>
                                @endif
                                @if (is_string($href) && $href !== '')
                                    <a href="{{ $href }}" class="fw-semibold">
                                        {{ (is_string($linkLabel) && $linkLabel !== '') ? $linkLabel : $href }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if (!empty($sections) && is_array($sections))
        @foreach ($sections as $section)
            @php
                $heading = $section['heading'] ?? null;
                $body = $section['body'] ?? null;
                $list = $section['list'] ?? null;
            @endphp
            @if (is_string($heading) && $heading !== '')
                <h2 class="h5 text-body mt-4">{{ $heading }}</h2>
            @endif
            @if (is_string($body) && $body !== '')
                <p>{{ $body }}</p>
            @endif
            @if (is_array($list) && count($list))
                <ul class="mb-3">
                    @foreach ($list as $item)
                        @if (is_string($item) && $item !== '')
                            <li>{{ $item }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif
        @endforeach
    @endif

    @if (!empty($cta) && is_array($cta))
        <div class="mt-4 p-3 border rounded-3 bg-light-subtle">
            @if (!empty($cta['title']))
                <p class="fw-semibold text-body mb-2">{{ $cta['title'] }}</p>
            @endif
            @if (!empty($cta['text']))
                <p class="mb-3">{{ $cta['text'] }}</p>
            @endif
            @if (!empty($cta['url']) && !empty($cta['label']))
                <a href="{{ $cta['url'] }}" class="main-btn btn-hover">{{ $cta['label'] }}</a>
            @endif
        </div>
    @endif
</div>

