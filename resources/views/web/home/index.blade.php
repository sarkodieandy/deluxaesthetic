@extends('web.layouts.app')

@section('title', config('clinic.name').' — Aesthetics, Academy & Beauty')
@section('meta_description', 'Premium medical-aesthetic treatments, professional training, and curated beauty care at '.config('clinic.name').' in Accra, Ghana.')
@section('meta_image', $cmsPage?->hero_image_url ?: asset('assets/web/images/hero/hero-botox.webp'))
@section('meta_image_alt', 'Expert injectable care at '.config('clinic.name'))
@push('preload')
<link rel="preload" as="image" href="{{ $cmsPage?->hero_image_url ?: asset('assets/web/images/hero/hero-botox.webp') }}" fetchpriority="high">
@endpush

@section('content')
<section class="home-v3-hero" data-hero x-data="heroCarousel({{ count($heroSlides) }})">
    <div class="home-v3-hero__media hero-carousel" x-ref="carousel" data-hero-mask>
        @foreach($heroSlides as $index => $slide)
            <div class="hero-carousel__slide {{ $index === 0 ? 'is-active' : '' }}" data-hero-slide aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                <img src="{{ $index === 0 && $cmsPage?->hero_image_url ? $cmsPage->hero_image_url : asset($slide['src']) }}" alt="{{ $slide['alt'] }}" width="1920" height="1280" decoding="async" @if($index === 0) fetchpriority="high" @else loading="lazy" @endif>
            </div>
        @endforeach
    </div>
    <div class="home-v3-hero__veil" aria-hidden="true"></div>
    <div class="container-site home-v3-hero__content">
        <div class="home-v3-hero__top" data-hero-brand>
            <span>Accra · Ghana</span>
            <span>Clinical Procedures · Academy · Products</span>
        </div>
        <div class="home-v3-hero__main">
            <p class="text-label" data-hero-brand>{{ $cmsPage?->hero_eyebrow ?: 'INJECTABLE TREATMENTS · ACADEMY · PREMIUM PRODUCTS' }}</p>
            <h1 data-hero-headline>{{ $cmsPage?->hero_title ?: 'Expert Injectable Care. Professional Training. Premium Products.' }}</h1>
            <p data-hero-support>{{ $cmsPage?->hero_body ?: 'An expert-led aesthetic clinic in Accra specialising in Botox, dermal fillers and advanced skin procedures — alongside internationally delivered academy training and curated professional products.' }}</p>
            <div class="home-v3-hero__actions" data-hero-actions>
                <a href="{{ route('web.clinical.index') }}" class="btn btn-primary">Explore Clinical Procedures</a>
                <a href="{{ route('web.academy.index') }}" class="btn btn-light">Discover the Academy</a>
                <a href="{{ route('web.store.index') }}" class="btn btn-outline-light">Shop Products</a>
            </div>
        </div>
        <div class="home-v3-hero__footer">
            <div class="home-v3-hero__labels" aria-label="Hero slides">
                @foreach($heroSlides as $index => $slide)
                    <button type="button" :class="active === {{ $index }} && 'is-active'" @click="goTo({{ $index }})">
                        <span>{{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}</span>{{ $slide['label'] }}
                    </button>
                @endforeach
            </div>
            <a href="#discover" class="home-v3-scroll">Discover <span aria-hidden="true">↓</span></a>
        </div>
    </div>
</section>

<section class="home-v3-intro" id="discover">
    <div class="container-site home-v3-intro__grid">
        <div class="reveal">
            <p class="text-label">The De Luxe Experience</p>
            <h2>Three pillars. One trusted platform.</h2>
        </div>
        <div class="home-v3-intro__copy reveal reveal-delay-2">
            <p>Whether you're seeking advanced injectable care, professional aesthetics training, or premium beauty products — De Luxe brings all three together with clinical precision and elegance.</p>
        </div>
    </div>
</section>

<section class="home-v3-worlds">
    <div class="container-site">
        <div class="home-v3-worlds__grid">
            <a href="{{ route('web.clinical.index') }}" class="home-v3-world reveal" data-tilt-card>
                <img src="{{ asset('assets/web/images/hero/hero-botox.webp') }}" alt="Expert injectable treatment — Botox and dermal fillers" loading="lazy" decoding="async">
                <div><span>01 · Clinical Procedures</span><h3>Injectable & clinical treatments</h3><p>From Botox and dermal fillers to advanced skin protocols, spa therapy and body treatments — with transparent pricing and expert consultation.</p><strong>Explore Procedures →</strong></div>
            </a>
            <a href="{{ route('web.academy.index') }}" class="home-v3-world reveal reveal-delay-1" data-tilt-card>
                <img src="{{ asset('assets/web/images/hero/hero-beauty-academy.webp') }}" alt="Professional aesthetics academy training" loading="lazy" decoding="async">
                <div><span>02 · Academy</span><h3>Professional training</h3><p>Internationally delivered aesthetics education — course outlines, training formats, student stories, certifications and admissions guidance.</p><strong>Explore the Academy →</strong></div>
            </a>
            <a href="{{ route('web.store.index') }}" class="home-v3-world reveal reveal-delay-2" data-tilt-card>
                <img src="{{ asset('assets/web/images/treatments/skincare-ritual.webp') }}" alt="Curated professional skincare and beauty products" loading="lazy" decoding="async">
                <div><span>03 · Products</span><h3>Shop premium products</h3><p>Browse clinic-selected skincare, professional supplies, treatment kits and beauty essentials — add to cart and order directly through WhatsApp.</p><strong>Browse Products →</strong></div>
            </a>
        </div>
    </div>
</section>

@if($trainingCountries->isNotEmpty())
<section class="home-v3-countries">
    <div class="container-site">
        <header class="home-v3-heading reveal">
            <div><p class="text-label">The Academy across West Africa</p><h2>{{ $trainingCountries->count() }} {{ \Illuminate\Support\Str::plural('country', $trainingCountries->count()) }}.<br>One training standard.</h2></div>
            <div class="home-v3-countries__intro">
                <p>De Luxe has travelled across West Africa to deliver practical aesthetics education in {{ $trainingCountries->pluck('title')->join(', ', ' and ') }}.</p>
                <a href="{{ route('web.academy.index') }}">Explore Academy masterclasses <span aria-hidden="true">→</span></a>
            </div>
        </header>
        <div class="home-countries-elegant reveal reveal-delay-2">
            @foreach($trainingCountries as $i => $country)
                @php
                    $countrySlug = match (\Illuminate\Support\Str::slug($country->title)) {
                        'cote-divoire', 'cote-d-ivoire' => 'cote-divoire',
                        'benin-republic' => 'benin',
                        default => \Illuminate\Support\Str::slug($country->title),
                    };
                    $knownFlag = in_array($countrySlug, ['ghana', 'cameroon', 'cote-divoire', 'senegal', 'benin'], true);
                @endphp
                <article class="home-country-card reveal" style="--reveal-delay: {{ $i * 90 }}ms">
                    <div class="home-country-card__header">
                        <span class="home-country-card__badge">{{ sprintf('%02d', $i + 1) }}</span>
                        <span class="home-country-card__flag">
                            @if($country->imageUrl())
                                <img src="{{ $country->imageUrl() }}" alt="{{ $country->title }} Academy training" width="900" height="600" loading="lazy">
                            @elseif($knownFlag)
                                <img src="{{ asset('assets/web/flags/'.$countrySlug.'.svg') }}" alt="" width="900" height="600" loading="lazy">
                            @else
                                <strong aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($country->title, 0, 2)) }}</strong>
                            @endif
                        </span>
                    </div>
                    <div class="home-country-card__body">
                        @if($country->subtitle)<p class="home-country-card__city">{{ $country->subtitle }}</p>@endif
                        <h3 class="home-country-card__name">{{ $country->title }}</h3>
                        <p class="home-country-card__note">{{ $country->body }}</p>
                    </div>
                    <div class="home-country-card__accent" aria-hidden="true"></div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($featuredTreatments->isNotEmpty())
<section class="home-v3-treatments">
    <div class="container-site">
        <header class="home-v3-heading home-v3-heading--light reveal">
            <div><p class="text-label">Signature care</p><h2>Treatments with intention.</h2></div>
            <a href="{{ route('web.clinical.index') }}">View all treatments →</a>
        </header>
        @php
            $fallbackImages = ['assets/web/images/treatments/facial-care.webp','assets/web/images/treatments/skincare-ritual.webp','assets/web/images/treatments/body-massage.webp'];
            $cards = $featuredTreatments;
        @endphp
        <div class="home-v3-treatments__grid">
            @foreach($cards as $index => $treatment)
                @php
                    $href = $treatment->slug ? route('web.treatments.show', $treatment->slug) : route('web.clinical.index');
                    $bookHref = !empty($treatment->id) ? route('web.booking.create', ['treatment_id'=>$treatment->id]) : route('web.booking.create');
                    $price = method_exists($treatment, 'effectivePrice') ? $treatment->effectivePrice() : ($treatment->price ?? null);
                    $hasDuration = !empty($treatment->duration_minutes);
                    $hasPrice = $price !== null && (float) $price > 0;
                    $isPublishedTreatment = $treatment instanceof \App\Models\Treatment;
                    $img = $isPublishedTreatment ? $treatment->imageUrl() : null;
                    $img = $img ?: asset($fallbackImages[$index % 3]);
                @endphp
                <article class="home-v3-treatment reveal" data-parallax-card>
                    <a href="{{ $href }}" class="home-v3-treatment__media"><img src="{{ $img }}" alt="{{ $treatment->name }}" loading="lazy"></a>
                    <div class="home-v3-treatment__body">
                        <p><span>{{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}</span>{{ $treatment->category->name ?? 'Treatment' }}</p>
                        <h3>{{ $treatment->name }}</h3>
                        <p>{{ $treatment->short_description }}</p>
                        <div>
                            <span>
                                @if($hasDuration || $hasPrice)
                                    @if($hasDuration)
                                        {{ $treatment->duration_minutes }} min
                                    @endif
                                    @if($hasDuration && $hasPrice)
                                        ·
                                    @endif
                                    @if($hasPrice)
                                        GHS {{ number_format((float) $price, 2) }}
                                    @endif
                                @else
                                    View care options
                                @endif
                            </span>
                            <a href="{{ $isPublishedTreatment ? $bookHref : $href }}">{{ $isPublishedTreatment ? 'Book' : 'Explore' }} →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="home-v3-founder">
    <div class="container-site home-v3-founder__grid">
        <div class="home-v3-founder__visual reveal">
            <div class="home-v3-founder__image home-v3-founder__image--main" data-parallax-image><img src="{{ $ceo?->photo_path ? $ceo->photoUrl() : asset('assets/web/images/team/ceo-academy-portrait.webp') }}" alt="{{ $ceo?->user?->name ?? config('clinic.ceo.name') }}" width="896" height="1152"></div>
            <div class="home-v3-founder__image home-v3-founder__image--small" data-parallax-image><img src="{{ asset('assets/web/images/academy/academy-training.webp') }}" alt="Hands-on professional aesthetics training at De Luxe Academy" loading="lazy"></div>
            <span class="home-v3-founder__seal">Expert led<br>since day one</span>
        </div>
        <div class="home-v3-founder__copy reveal reveal-delay-2">
            <p class="text-label">Our clinical philosophy</p>
            <h2>Enhancement should still feel like you.</h2>
            <blockquote>“Our work begins with listening. The most beautiful result is one that respects the individual.”</blockquote>
            <p>Led by {{ $ceo?->user?->name ?? config('clinic.ceo.name') }}, De Luxe combines clinical discipline with an understanding of beauty that is personal, balanced and never excessive.</p>
            <div><a href="{{ route('web.about') }}" class="btn btn-primary">Meet the team</a><a href="{{ route('web.booking.create') }}" class="btn btn-secondary">Book consultation</a></div>
        </div>
    </div>
</section>

@include('web.components.our-work', [
    'featuredBeforeAfter' => $featuredBeforeAfter ?? null,
    'ourWorkGallery' => $ourWorkGallery ?? collect(),
])

<section class="home-v3-duo">
    <article class="home-v3-duo__panel home-v3-duo__panel--academy reveal">
        <img src="{{ asset('assets/web/images/hero/hero-beauty-academy.webp') }}" alt="De Luxe professional aesthetics academy" loading="lazy" decoding="async">
        <div><p class="text-label">Academy masterclasses</p><h2>{{ $academyPathwayCount }} {{ \Illuminate\Support\Str::plural('pathway', $academyPathwayCount) }}.<br>Hands-on confidence.</h2><p>Explore physical, admissions-led masterclasses with published course outlines, fees and supervised practical training.</p><a href="{{ route('web.academy.index') }}#course-outlines" class="btn btn-light">View courses &amp; pricing</a></div>
    </article>
    <article class="home-v3-duo__panel home-v3-duo__panel--store reveal reveal-delay-2">
        <img src="{{ asset('assets/web/images/treatments/skincare-ritual.webp') }}" alt="Curated De Luxe skincare products" loading="lazy" decoding="async">
        <div><p class="text-label">The De Luxe Edit</p><h2>Your routine,<br>carefully considered.</h2><p>Discover clinic-selected skincare and beauty essentials, available to order directly through WhatsApp.</p><a href="{{ route('web.store.index') }}" class="btn btn-light">Shop products</a></div>
    </article>
</section>

<section class="home-v3-proof">
    <div class="container-site">
        <div class="home-v3-proof__stats reveal">
            <div><strong data-count="3">0</strong><span>Business divisions</span></div>
            <div><strong data-count="{{ $trainingCountries->count() }}">0</strong><span>Training countries</span></div>
            <div><strong data-count="{{ $academyPathwayCount }}">0</strong><span>Academy pathways</span></div>
            <div><strong data-count="1">0</strong><span>Standard of care</span></div>
        </div>
        <header class="home-v3-heading reveal"><div><p class="text-label">Client words</p><h2>Care people remember.</h2></div></header>
        <div class="home-v3-testimonials">
            @php($testimonialFeed = $managedTestimonials->isNotEmpty() ? $managedTestimonials->map(fn($item) => ['quote' => $item->quote, 'name' => $item->name, 'context' => $item->context]) : collect(__('web.home.testimonials')))
            @foreach($testimonialFeed as $index => $quote)
                <blockquote class="reveal">
                    <span>0{{ $index + 1 }}</span>
                    <p>“{{ $quote['quote'] }}”</p>
                    <footer><strong>{{ $quote['name'] }}</strong><small>{{ $quote['context'] }}</small></footer>
                </blockquote>
            @endforeach
        </div>
    </div>
</section>

<section class="home-v3-faq" x-data="{ open: 0 }">
    <div class="container-site home-v3-faq__grid">
        <div class="reveal"><p class="text-label">{{ __('web.home.faq_label') }}</p><h2>{{ __('web.home.faq_title') }}</h2><p>{{ __('web.home.faq_intro') }}</p><a href="{{ route('web.contact') }}" class="btn btn-secondary">Contact us</a></div>
        <div class="home-v3-faq__list reveal reveal-delay-2">
            @foreach(__('web.home.faqs') as $i => $faq)
                <article :class="open === {{ $i }} && 'is-open'">
                    <button type="button" @click="open = open === {{ $i }} ? -1 : {{ $i }}" :aria-expanded="(open === {{ $i }}).toString()"><span>0{{ $i + 1 }}</span>{{ $faq['q'] }}<i x-text="open === {{ $i }} ? '−' : '+'"></i></button>
                    <div x-show="open === {{ $i }}" x-transition.opacity.duration.250ms><p>{{ $faq['a'] }}</p></div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="home-v3-cta">
    <div class="home-v3-cta__image" data-parallax-image><img src="{{ asset('assets/web/images/gallery/clinic-ambiance.webp') }}" alt="The welcoming De Luxe clinic environment" loading="lazy" decoding="async"></div>
    <div class="home-v3-cta__veil"></div>
    <div class="container-site home-v3-cta__content reveal">
        <p class="text-label">Begin with a conversation</p>
        <h2>Your next step<br>can feel beautifully simple.</h2>
        <p>Book a consultation and let our team guide you toward the treatment, training or care that suits you best.</p>
        <div><a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book consultation</a><a href="{{ route('web.contact') }}" class="btn btn-light">Contact De Luxe</a></div>
    </div>
</section>
@endsection
