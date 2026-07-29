@php
    $eyebrow = $eyebrow ?? null;
    $title = $title ?? '';
    $actionHref = $actionHref ?? null;
    $actionLabel = $actionLabel ?? null;
@endphp

<div class="section-head">
    <div>
        @if($eyebrow)
            <p class="text-label mb-3">{{ $eyebrow }}</p>
        @endif
        <h2 class="text-section">{{ $title }}</h2>
    </div>
    @if($actionHref && $actionLabel)
        <a href="{{ $actionHref }}" class="btn btn-secondary">{{ $actionLabel }}</a>
    @endif
</div>
