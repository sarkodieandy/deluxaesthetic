@extends('web.layouts.app')
@section('title', __('web.pages.gallery_title').' — '.config('clinic.name'))
@section('meta_description', 'View clinic treatments, professional aesthetics work and real before-and-after results from De Luxe Aesthetic Clinic in Accra.')
@section('content')
@include('web.components.page-intro', [
    'eyebrow' => $cmsPage?->hero_eyebrow ?: __('web.pages.gallery_eyebrow'),
    'title' => $cmsPage?->hero_title ?: __('web.pages.gallery_title'),
    'lead' => $cmsPage?->hero_body ?: __('web.pages.gallery_lead'),
])
@php
    $collectionDescriptions = [
        'global' => 'Signature clinic moments, academy highlights and stories from across our international community.',
        'ghana' => 'Clinical excellence, hands-on education and community moments from our home in Ghana.',
        'cameroon' => 'A visual journal of professional training and industry connections in Cameroon.',
        'cote-divoire' => 'Training experiences, clinical artistry and memorable moments from Côte d’Ivoire.',
        'senegal' => 'Highlights from our professional education and aesthetics community in Senegal.',
        'benin-republic' => 'A collection of academy experiences and regional partnerships in Benin Republic.',
    ];
    $totalGalleryImages = (int) $collectionCounts->sum();
@endphp

@if($galleryShowcaseItems->isNotEmpty())
    <section
        class="gallery-cinema"
        :class="{ 'is-paused': paused }"
        x-data="heroCarousel({{ $galleryShowcaseItems->count() }})"
        @mouseenter="stopAutoplay()"
        @mouseleave="startAutoplay()"
        @focusin="stopAutoplay()"
        @focusout="startAutoplay()"
        @keydown.left.window="prev()"
        @keydown.right.window="next()"
        aria-roledescription="carousel"
        aria-label="De Luxe gallery highlights"
    >
        <div class="gallery-cinema__stage hero-carousel" x-ref="carousel">
            @foreach($galleryShowcaseItems as $item)
                <article
                    class="gallery-cinema__slide hero-carousel__slide {{ $loop->first ? 'is-active' : '' }}"
                    data-hero-slide
                    aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                    aria-label="Slide {{ $loop->iteration }} of {{ $galleryShowcaseItems->count() }}"
                >
                    @if($item->imageUrl())
                        <img
                            src="{{ $item->imageUrl() }}"
                            alt="{{ $item->alt_text ?: $item->title }}"
                            width="1800"
                            height="1100"
                            decoding="async"
                            @if($loop->first) fetchpriority="high" @else loading="lazy" @endif
                        >
                    @endif
                    <div class="gallery-cinema__shade"></div>
                    <div class="container-site gallery-cinema__content">
                        <p>{{ $item->locationGroupLabel() }} <span aria-hidden="true">/</span> Visual journal</p>
                        <h2>{{ $item->title }}</h2>
                        @if($item->description)
                            <div>{{ $item->description }}</div>
                        @endif
                    </div>
                    <span class="gallery-cinema__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                </article>
            @endforeach
        </div>

        <div class="container-site gallery-cinema__navigation">
            <div class="gallery-cinema__counter" aria-live="polite">
                <span x-text="String(active + 1).padStart(2, '0')">01</span>
                <i></i>
                <span>{{ str_pad((string) $galleryShowcaseItems->count(), 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="gallery-cinema__progress" aria-label="Choose gallery slide">
                @foreach($galleryShowcaseItems as $item)
                    <button
                        type="button"
                        @click="goTo({{ $loop->index }})"
                        :class="{ 'is-active': active === {{ $loop->index }} }"
                        aria-label="Show {{ $item->title }}"
                    ><span></span></button>
                @endforeach
            </div>
            <div class="gallery-cinema__arrows">
                <button type="button" @click="prev()" aria-label="Previous gallery image">←</button>
                <button type="button" @click="next()" aria-label="Next gallery image">→</button>
            </div>
        </div>
    </section>
@endif

<section class="gallery-archive">
    <div class="container-site">
        <header class="gallery-archive__intro">
            <div>
                <p class="text-label">Our visual journal</p>
                <h2>Stories from every destination.</h2>
            </div>
            <p>Explore our work at home and across West Africa. Every collection is organised by destination, with general clinic and brand moments held in our Global Gallery.</p>
        </header>

        <nav class="gallery-filters" aria-label="Filter gallery by destination">
            <a href="{{ route('web.gallery', ['collection' => 'all']) }}" class="gallery-filter {{ $selectedCollection === 'all' ? 'is-active' : '' }}" @if($selectedCollection === 'all') aria-current="page" @endif>
                <span>All collections</span>
                <small>{{ str_pad((string) $totalGalleryImages, 2, '0', STR_PAD_LEFT) }}</small>
            </a>
            @foreach($locationGroups as $key => $label)
                <a href="{{ route('web.gallery', ['collection' => $key]) }}" class="gallery-filter {{ $selectedCollection === $key ? 'is-active' : '' }}" @if($selectedCollection === $key) aria-current="page" @endif>
                    <span>{{ $label }}</span>
                    <small>{{ str_pad((string) ((int) ($collectionCounts[$key] ?? 0)), 2, '0', STR_PAD_LEFT) }}</small>
                </a>
            @endforeach
        </nav>

        @if ($galleryCollections->isEmpty())
            <div class="gallery-empty">
                <span>Collection coming soon</span>
                <h3>New stories are being prepared.</h3>
                <p>Gallery photographs uploaded from the admin area will appear in this collection.</p>
                @if($selectedCollection !== 'all')
                    <a href="{{ route('web.gallery', ['collection' => 'all']) }}" class="btn btn-secondary">View all collections</a>
                @endif
            </div>
        @else
            <div class="gallery-collections">
                @foreach($galleryCollections as $collection)
                    <section class="gallery-collection" id="collection-{{ $collection['key'] }}">
                        <header class="gallery-collection__header reveal">
                            <div class="gallery-collection__number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div>
                                <p class="text-label">Destination collection</p>
                                <h3>{{ $collection['label'] }}</h3>
                            </div>
                            <p>{{ $collectionDescriptions[$collection['key']] ?? 'A curated collection from the De Luxe Aesthetic Clinic and Academy community.' }}</p>
                            <span class="gallery-collection__count">{{ $collection['items']->count() }} {{ Str::plural('image', $collection['items']->count()) }}</span>
                        </header>

                        <div class="gallery-grid">
                            @foreach ($collection['items'] as $item)
                                <article class="gallery-card reveal" style="--reveal-delay: {{ ($loop->index % 3) * 90 }}ms">
                                    <div class="gallery-card__media">
                                        @if ($item->imageUrl())
                                            <img src="{{ $item->imageUrl() }}" alt="{{ $item->alt_text ?: $item->title }}" loading="lazy" decoding="async">
                                        @endif
                                        <span>{{ $collection['label'] }}</span>
                                    </div>
                                    <div class="gallery-card__body">
                                        <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <div>
                                            <h4>{{ $item->title }}</h4>
                                            @if($item->description)<p>{{ $item->description }}</p>@endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif

        <section class="gallery-results">
            <header class="gallery-results__header">
                <p class="text-label">Clinical results</p>
                <h2>{{ __('web.home.ba_title') }}</h2>
                @if($selectedCollection !== 'all')
                    <span>Showing {{ $locationGroups[$selectedCollection] }}</span>
                @endif
            </header>
            @if ($beforeAfterItems->isEmpty())
                <div class="gallery-empty gallery-empty--compact">
                    <p>Before-and-after cases for this collection will appear here once published.</p>
                </div>
            @else
                <div class="gallery-results__list">
                    @foreach ($beforeAfterItems as $item)
                        @if ($item->hasBeforeAfterPair())
                            <article class="gallery-result reveal">
                                <div class="gallery-result__copy">
                                    <p class="text-label">{{ $item->locationGroupLabel() }}</p>
                                    <h3>{{ $item->title }}</h3>
                                    @if($item->description)<p>{{ $item->description }}</p>@endif
                                </div>
                                @include('web.components.before-after-compare', [
                                    'beforeUrl' => $item->beforeImageUrl(),
                                    'afterUrl' => $item->afterImageUrl(),
                                    'beforeAlt' => $item->alt_text ?: $item->title.' — '.__('web.home.ba_before'),
                                    'afterAlt' => $item->alt_text ?: $item->title.' — '.__('web.home.ba_after'),
                                    'title' => $item->title,
                                ])
                            </article>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</section>
@endsection
