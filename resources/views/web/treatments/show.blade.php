@extends('web.layouts.app')

@section('title', $treatment->name.' — '.config('clinic.name'))
@section('meta_description', $treatment->seo_description ?: $treatment->short_description)
@section('meta_image', $treatment->imageUrl() ?: asset(config('seo.default_image')))
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
    'areaServed' => [
        '@type' => 'City',
        'name' => 'Accra',
    ],
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
<section class="section border-b border-[var(--color-border)]">
    <div class="container-site grid gap-10 lg:grid-cols-12">
        <div class="lg:col-span-7">
            @if($treatment->image_path)
                <div class="mb-8 overflow-hidden border border-[var(--color-border)] bg-[var(--color-stone)]">
                    <img src="{{ $treatment->imageUrl() ?? asset('storage/'.$treatment->image_path) }}" alt="{{ $treatment->name }}" class="h-[26rem] w-full object-cover">
                </div>
            @endif
            <p class="text-label mb-3">{{ $treatment->category?->name }}</p>
            <h1 class="text-page-title mb-4">{{ $treatment->name }}</h1>
            <div class="mb-6 h-px w-20 bg-[var(--color-bronze)]"></div>
            <p class="mb-6 max-w-2xl text-[var(--text-body-lg)] text-[var(--color-soft-grey)]">{{ $treatment->short_description }}</p>
            <p class="mb-8 max-w-2xl">{{ $treatment->description }}</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('web.booking.create', ['treatment_id' => $treatment->id]) }}" class="btn btn-primary">{{ __('web.book') }}</a>
                <a href="{{ route('web.treatments.index') }}" class="btn btn-secondary">All treatments</a>
            </div>
        </div>
        <aside class="panel h-fit p-8 lg:col-span-5">
            <p class="text-label mb-2">Investment</p>
            <p class="font-display text-4xl mb-4">GHS {{ number_format((float) $treatment->effectivePrice(), 2) }}</p>
            <p class="mb-2">Duration: {{ $treatment->duration_minutes }} minutes</p>
            <p class="mb-2">Deposit: GHS {{ number_format((float) ($treatment->deposit_amount ?? 0), 2) }}</p>
            <p class="mb-6">Sessions recommended: {{ $treatment->recommended_sessions }}</p>
            @if($treatment->practitioners->isNotEmpty())
                <p class="text-label mb-3">Practitioners</p>
                <ul class="space-y-2">
                    @foreach($treatment->practitioners as $practitioner)
                        <li>{{ $practitioner->user?->name }} — {{ $practitioner->professional_title }}</li>
                    @endforeach
                </ul>
            @endif
        </aside>
    </div>
</section>

<section class="section">
    <div class="container-site grid gap-8 md:grid-cols-3">
        <div class="panel p-6">
            <p class="text-label mb-3">Benefits</p>
            <ul class="space-y-2 text-[var(--color-soft-grey)]">
                @foreach(($treatment->benefits ?? []) as $benefit)
                    <li>{{ $benefit }}</li>
                @endforeach
            </ul>
        </div>
        <div class="panel p-6">
            <p class="text-label mb-3">Preparation</p>
            <p class="text-[var(--color-soft-grey)]">{{ $treatment->preparation_instructions }}</p>
        </div>
        <div class="panel p-6">
            <p class="text-label mb-3">Aftercare</p>
            <p class="text-[var(--color-soft-grey)]">{{ $treatment->aftercare_instructions }}</p>
        </div>
    </div>
</section>

@if($related->isNotEmpty())
<section class="pb-[var(--space-section)]">
    <div class="container-site">
        <p class="text-label mb-3">Related</p>
        <div class="grid gap-0 border border-[var(--color-border)] md:grid-cols-3">
            @foreach($related as $item)
                <a class="block border-[var(--color-border)] p-6 no-underline md:border-r" href="{{ route('web.treatments.show', $item->slug) }}">
                    <h2 class="font-display text-2xl text-[var(--color-charcoal)]">{{ $item->name }}</h2>
                    <p class="mt-2 text-[var(--color-soft-grey)]">GHS {{ number_format((float) $item->effectivePrice(), 2) }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
