@extends('web.layouts.app')

@section('title', ($treatment->seo_title ?: $treatment->name).' — '.config('clinic.name'))
@section('meta_description', $treatment->seo_description ?: $treatment->short_description)
@section('meta_image', $treatment->imageUrl() ?: ($treatment->category?->imageUrl() ?: asset(config('seo.default_image'))))
@section('meta_image_alt', $treatment->name)
@push('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => $treatment->name,
    'description' => $treatment->seo_description ?: $treatment->short_description,
    'image' => $treatment->imageUrl(),
    'serviceType' => $treatment->category?->name,
    'provider' => ['@id' => url('/').'#business'],
    'areaServed' => ['@type' => 'City', 'name' => 'Accra'],
    'offers' => [
        '@type' => 'Offer',
        'url' => route('web.treatments.show', $treatment->slug),
        'priceCurrency' => config('clinic.currency'),
        'price' => $treatment->effectivePrice(),
        'availability' => 'https://schema.org/InStock',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<section class="relative isolate overflow-hidden bg-[#171714] text-white">
    @if ($treatment->imageUrl())
        <img src="{{ $treatment->imageUrl() }}" alt="{{ $treatment->name }}" class="absolute inset-0 -z-20 h-full w-full object-cover" width="1600" height="1067" decoding="async" fetchpriority="high">
    @elseif ($treatment->category?->imageUrl())
        <img src="{{ $treatment->category->imageUrl() }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" width="1600" height="1067" decoding="async" fetchpriority="high">
    @endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/95 via-black/80 to-black/35"></div>
    <div class="container-site grid min-h-[610px] gap-10 py-16 lg:grid-cols-[1fr_360px] lg:items-end lg:py-24">
        <div class="max-w-3xl">
            <a class="text-label text-[#e6c995] no-underline" href="{{ route('web.clinical.index', ['category' => $treatment->category?->slug]) }}#procedures">← {{ $treatment->category?->name }}</a>
            <h1 class="mt-5 font-display text-5xl leading-[.95] sm:text-6xl lg:text-8xl">{{ $treatment->name }}</h1>
            <p class="mt-7 max-w-2xl text-lg leading-relaxed text-white/75">{{ $treatment->short_description }}</p>
            <div class="mt-9 flex flex-wrap gap-3"><a href="{{ route('web.booking.create', ['treatment_id' => $treatment->id]) }}" class="btn btn-primary">Book this procedure</a><a href="{{ route('web.contact') }}" class="btn btn-outline-light">Ask a question</a></div>
        </div>
        <aside class="border border-white/20 bg-black/45 p-7 backdrop-blur-sm">
            <p class="text-label text-white/55">Procedure investment</p>
            <div class="mt-3 flex flex-wrap items-baseline gap-3">
                <p class="font-display text-4xl">GHS {{ number_format((float) $treatment->effectivePrice(), 2) }}</p>
                @if ($treatment->promotional_price !== null)<p class="text-white/45 line-through">GHS {{ number_format((float) $treatment->price, 2) }}</p>@endif
            </div>
            <dl class="mt-6 divide-y divide-white/15 text-sm">
                <div class="flex justify-between gap-4 py-3"><dt class="text-white/55">Duration</dt><dd>{{ $treatment->duration_minutes }} minutes</dd></div>
                <div class="flex justify-between gap-4 py-3"><dt class="text-white/55">Recommended sessions</dt><dd>{{ $treatment->recommended_sessions }}</dd></div>
                @if ((int) $treatment->recovery_days > 0)<div class="flex justify-between gap-4 py-3"><dt class="text-white/55">Typical recovery</dt><dd>{{ $treatment->recovery_days }} {{ Str::plural('day', $treatment->recovery_days) }}</dd></div>@endif
                @if ($treatment->deposit_amount !== null)<div class="flex justify-between gap-4 py-3"><dt class="text-white/55">Booking deposit</dt><dd>GHS {{ number_format((float) $treatment->deposit_amount, 2) }}</dd></div>@endif
            </dl>
        </aside>
    </div>
</section>

<section class="section">
    <div class="container-site grid gap-12 lg:grid-cols-12">
        <article class="lg:col-span-7">
            <p class="text-label mb-3">The procedure</p>
            <h2 class="text-page-title">A plan built around you.</h2>
            @if ($treatment->description)<div class="mt-6 whitespace-pre-line text-lg leading-relaxed text-[var(--color-soft-grey)]">{{ $treatment->description }}</div>@endif

            @if (($treatment->benefits ?? []) !== [])
                <div class="mt-10 border-t border-[var(--color-border)] pt-8"><h3 class="font-display text-3xl">Potential benefits</h3><ul class="mt-5 grid gap-3 sm:grid-cols-2">@foreach($treatment->benefits as $benefit)<li class="border-l-2 border-[var(--color-bronze)] pl-4 text-[var(--color-soft-grey)]">{{ $benefit }}</li>@endforeach</ul></div>
            @endif
        </article>
        <aside class="space-y-6 lg:col-span-5">
            @if ($treatment->suitable_candidates)<div class="border border-[var(--color-border)] bg-[#f3efe8] p-7"><p class="text-label mb-3">Who it may suit</p><p class="whitespace-pre-line text-[var(--color-soft-grey)]">{{ $treatment->suitable_candidates }}</p></div>@endif
            @if ($treatment->preparation_instructions)<div class="border border-[var(--color-border)] p-7"><p class="text-label mb-3">Preparing for your visit</p><p class="whitespace-pre-line text-[var(--color-soft-grey)]">{{ $treatment->preparation_instructions }}</p></div>@endif
            @if ($treatment->aftercare_instructions)<div class="border border-[var(--color-border)] p-7"><p class="text-label mb-3">Aftercare</p><p class="whitespace-pre-line text-[var(--color-soft-grey)]">{{ $treatment->aftercare_instructions }}</p></div>@endif
            @if ($treatment->contraindications)<div class="border border-[#b59058] bg-[#fffaf1] p-7"><p class="text-label mb-3">Safety considerations</p><p class="whitespace-pre-line text-[var(--color-soft-grey)]">{{ $treatment->contraindications }}</p></div>@endif
        </aside>
    </div>
</section>

@if ($treatment->practitioners->isNotEmpty())
<section class="border-y border-[var(--color-border)] bg-[#f3efe8] py-12">
    <div class="container-site"><p class="text-label mb-5">Available practitioners</p><div class="flex flex-wrap gap-4">@foreach($treatment->practitioners as $practitioner)<div class="min-w-[240px] border border-[var(--color-border)] bg-white p-5"><strong class="font-display text-xl">{{ $practitioner->user?->name }}</strong><p class="mt-1 text-sm text-[var(--color-soft-grey)]">{{ $practitioner->displayTitle() }}</p></div>@endforeach</div></div>
</section>
@endif

@if ($beforeAfter->isNotEmpty())
<section class="section bg-[#171714] text-white">
    <div class="container-site">
        <header class="mb-10 grid gap-5 lg:grid-cols-2 lg:items-end"><div><p class="text-label text-white/55">Procedure results</p><h2 class="mt-3 font-display text-5xl">Before &amp; after</h2></div><p class="max-w-xl text-white/60 lg:justify-self-end">Individual outcomes vary. These cases do not guarantee a result; suitability and expectations are discussed during consultation.</p></header>
        <div class="grid gap-8 lg:grid-cols-2">
            @foreach ($beforeAfter as $item)
                <article>
                    @include('web.components.before-after-compare', [
                        'beforeUrl' => $item->beforeImageUrl(),
                        'afterUrl' => $item->afterImageUrl(),
                        'beforeAlt' => $item->alt_text ?: $item->title.' before',
                        'afterAlt' => $item->alt_text ?: $item->title.' after',
                        'title' => $item->title,
                    ])
                    <h3 class="mt-4 font-display text-2xl">{{ $item->title }}</h3>
                    @if ($item->description)<p class="mt-1 text-sm text-white/60">{{ $item->description }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($related->isNotEmpty())
<section class="section">
    <div class="container-site"><header class="mb-8 flex flex-wrap items-end justify-between gap-4"><div><p class="text-label mb-3">Continue exploring</p><h2 class="text-section">Related procedures</h2></div><a href="{{ route('web.clinical.index', ['category' => $treatment->category?->slug]) }}#procedures" class="btn btn-secondary">View category</a></header>
        <div class="grid gap-px border border-[var(--color-border)] bg-[var(--color-border)] md:grid-cols-3">@foreach($related as $item)<a class="group bg-white p-7 text-inherit no-underline" href="{{ route('web.treatments.show', $item->slug) }}"><p class="text-label">{{ $item->category?->name }}</p><h3 class="mt-3 font-display text-3xl">{{ $item->name }}</h3><p class="mt-5 font-display text-2xl">GHS {{ number_format((float) $item->effectivePrice(), 2) }}</p><span class="mt-5 inline-block text-sm uppercase tracking-[.15em] text-[var(--color-bronze)]">View details →</span></a>@endforeach</div>
    </div>
</section>
@endif

<section class="section bg-[#d5af72]">
    <div class="container-site grid gap-7 lg:grid-cols-[1fr_auto] lg:items-center"><div><p class="text-label text-black/55">Your next step</p><h2 class="mt-3 font-display text-5xl text-[#171714]">Book an assessment before treatment.</h2><p class="mt-4 max-w-2xl text-black/65">Your practitioner will confirm suitability, answer questions and agree the right plan before proceeding.</p></div><a href="{{ route('web.booking.create', ['treatment_id' => $treatment->id]) }}" class="btn bg-[#171714] text-white hover:bg-black">Book {{ $treatment->name }}</a></div>
</section>
@endsection
