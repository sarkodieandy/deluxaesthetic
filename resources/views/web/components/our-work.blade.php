@php
    $compareItem = $featuredBeforeAfter ?? null;
    $hasCompare = $compareItem && $compareItem->hasBeforeAfterPair();

    $gridItems = ($ourWorkGallery ?? collect())
        ->filter(fn ($item) => $item->type === 'gallery' && $item->imageUrl())
        ->take(3);

    $fallbackGrid = [
        ['src' => asset('assets/web/images/hero/hero-spa-massage.webp'), 'alt' => __('web.home.our_work_fallback_1')],
        ['src' => asset('assets/web/images/hero/hero-nails.webp'), 'alt' => __('web.home.our_work_fallback_2')],
        ['src' => asset('assets/web/images/hero/hero-facial-tech.webp'), 'alt' => __('web.home.our_work_fallback_3')],
    ];
@endphp

<section class="section our-work" id="our-work" data-section="our-work">
    <div class="container-site">
        <header class="our-work__head reveal">
            <div class="our-work__head-text">
                <p class="text-label our-work__eyebrow">{{ __('web.home.ba_label') }}</p>
                <h2 class="our-work__title">{{ __('web.home.ba_title') }}</h2>
            </div>
            <a href="{{ route('web.gallery') }}" class="btn btn-primary our-work__cta">
                {{ __('web.home.ba_cta') }}
            </a>
        </header>

        <div class="our-work__compare reveal reveal-delay-1">
            @if ($hasCompare)
                @include('web.components.before-after-compare', [
                    'beforeUrl' => $compareItem->beforeImageUrl(),
                    'afterUrl' => $compareItem->afterImageUrl(),
                    'beforeAlt' => $compareItem->alt_text ?: $compareItem->title.' — '.__('web.home.ba_before'),
                    'afterAlt' => $compareItem->alt_text ?: $compareItem->title.' — '.__('web.home.ba_after'),
                    'title' => $compareItem->title,
                ])
                <p class="our-work__disclaimer">{{ __('web.home.ba_disclaimer') }}</p>
            @else
                <div class="our-work__placeholder">
                    <p class="text-label mb-2">{{ __('web.home.ba_before') }} / {{ __('web.home.ba_after') }}</p>
                    <p>{{ __('web.home.ba_upload_hint') }}</p>
                </div>
            @endif
        </div>

        <div class="our-work__grid">
            @if ($gridItems->isNotEmpty())
                @foreach ($gridItems as $index => $item)
                    <a
                        href="{{ route('web.gallery') }}"
                        class="our-work__tile reveal reveal-delay-{{ min($index + 1, 3) }}"
                    >
                        <span class="our-work__tile-media img-zoom">
                            <img
                                src="{{ $item->imageUrl() }}"
                                alt="{{ $item->alt_text ?: $item->title }}"
                                loading="lazy"
                                decoding="async"
                            >
                        </span>
                        @if ($item->title)
                            <span class="our-work__tile-caption">{{ $item->title }}</span>
                        @endif
                    </a>
                @endforeach
            @else
                @foreach ($fallbackGrid as $index => $photo)
                    <a
                        href="{{ route('web.gallery') }}"
                        class="our-work__tile reveal reveal-delay-{{ min($index + 1, 3) }}"
                    >
                        <span class="our-work__tile-media img-zoom">
                            <img src="{{ $photo['src'] }}" alt="{{ $photo['alt'] }}" loading="lazy" decoding="async">
                        </span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</section>
