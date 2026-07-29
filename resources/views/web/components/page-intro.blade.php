@php
    $eyebrow = $eyebrow ?? null;
    $title = $title ?? '';
    $lead = $lead ?? null;
@endphp

<header class="page-intro">
    <div class="container-site">
        @if($eyebrow)
            <p class="text-label page-intro__eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="page-intro__title">{{ $title }}</h1>
        @if($lead)
            <p class="page-intro__lead">{{ $lead }}</p>
        @endif
    </div>
</header>
