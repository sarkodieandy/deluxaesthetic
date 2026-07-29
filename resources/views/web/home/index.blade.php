@extends('web.layouts.app')

@section('title', config('clinic.name').' — Aesthetics, Academy & Beauty')
@section('meta_description', 'Premium medical-aesthetic treatments, professional training, and curated beauty care at '.config('clinic.name').' in Accra, Ghana.')

@section('content')
<section class="home-v3-hero" data-hero x-data="heroCarousel({{ count($heroSlides) }})">
    <div class="home-v3-hero__media hero-carousel" x-ref="carousel" data-hero-mask>
        @foreach($heroSlides as $index => $slide)
            <div class="hero-carousel__slide {{ $index === 0 ? 'is-active' : '' }}" data-hero-slide aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                <img src="{{ asset($slide['src']) }}" alt="{{ $slide['alt'] }}" @if($index === 0) fetchpriority="high" @else loading="lazy" @endif>
            </div>
        @endforeach
    </div>
    <div class="home-v3-hero__veil" aria-hidden="true"></div>
    <div class="container-site home-v3-hero__content">
        <div class="home-v3-hero__top" data-hero-brand>
            <span>Accra · Ghana</span>
            <span>Clinic · Academy · Store</span>
        </div>
        <div class="home-v3-hero__main">
            <p class="text-label" data-hero-brand>De Luxe Aesthetic Clinic</p>
            <h1 data-hero-headline>Where clinical<br>expertise meets<br><em>beautiful restraint.</em></h1>
            <p data-hero-support>Thoughtful aesthetic treatments, advanced professional education and carefully selected beauty essentials—all under one trusted name.</p>
            <div class="home-v3-hero__actions" data-hero-actions>
                <a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book a consultation</a>
                <a href="{{ route('web.treatments.index') }}" class="btn btn-light">Explore treatments</a>
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

<div class="home-v3-marquee" aria-label="De Luxe services">
    <div>
        <span>Clinical aesthetics</span><i>✦</i><span>Skin health</span><i>✦</i><span>Body care</span><i>✦</i><span>Professional academy</span><i>✦</i><span>Curated beauty</span><i>✦</i>
        <span aria-hidden="true">Clinical aesthetics</span><i aria-hidden="true">✦</i><span aria-hidden="true">Skin health</span><i aria-hidden="true">✦</i><span aria-hidden="true">Body care</span><i aria-hidden="true">✦</i><span aria-hidden="true">Professional academy</span><i aria-hidden="true">✦</i><span aria-hidden="true">Curated beauty</span><i aria-hidden="true">✦</i>
    </div>
</div>

<section class="home-v3-intro" id="discover">
    <div class="container-site home-v3-intro__grid">
        <div class="reveal">
            <p class="text-label">A complete aesthetics destination</p>
            <h2>Care for today.<br>Knowledge for tomorrow.</h2>
        </div>
        <div class="home-v3-intro__copy reveal reveal-delay-2">
            <p>De Luxe brings treatment, education and aftercare into one considered experience. Every client and student receives personal attention, honest guidance and a standard of care shaped by clinical knowledge.</p>
            <div>
                <span>01</span><p>Personalised consultation before treatment</p>
                <span>02</span><p>Professional, safety-led protocols</p>
                <span>03</span><p>Continued support beyond your visit</p>
            </div>
        </div>
    </div>
</section>

<section class="home-v3-worlds">
    <div class="container-site">
        <header class="home-v3-heading reveal">
            <div><p class="text-label">Explore De Luxe</p><h2>Three worlds. One standard.</h2></div>
            <p>Whether you are here to feel renewed, build a new skill or continue your care at home, your journey starts here.</p>
        </header>
        <div class="home-v3-worlds__grid">
            <a href="{{ route('web.treatments.index') }}" class="home-v3-world reveal" data-tilt-card>
                <img src="{{ asset('assets/web/images/hero/hero-facial-tech.jpg') }}" alt="Advanced facial treatment at De Luxe">
                <div><span>01 · Clinic</span><h3>Results-led treatments</h3><p>Facials, injectables, body care and restorative spa experiences.</p><strong>View treatments →</strong></div>
            </a>
            <a href="{{ route('web.academy.index') }}" class="home-v3-world reveal reveal-delay-1" data-tilt-card>
                <img src="{{ asset('assets/web/images/hero/hero-beauty-academy.jpg') }}" alt="Professional aesthetics academy training">
                <div><span>02 · Academy</span><h3>Train with confidence</h3><p>Hands-on aesthetics education, certification and lifetime mentorship.</p><strong>Explore the academy →</strong></div>
            </a>
            <a href="{{ route('web.store.index') }}" class="home-v3-world reveal reveal-delay-2" data-tilt-card>
                <img src="{{ asset('assets/web/images/treatments/skincare-ritual.jpg') }}" alt="Curated skincare and beauty products">
                <div><span>03 · Store</span><h3>Continue your care</h3><p>Clinic-selected skincare and beauty essentials for everyday routines.</p><strong>Shop the edit →</strong></div>
            </a>
        </div>
    </div>
</section>

<section class="home-v3-treatments">
    <div class="container-site">
        <header class="home-v3-heading home-v3-heading--light reveal">
            <div><p class="text-label">Signature care</p><h2>Treatments with intention.</h2></div>
            <a href="{{ route('web.treatments.index') }}">View all treatments →</a>
        </header>
        @php
            $fallbackImages = ['assets/web/images/treatments/facial-care.jpg','assets/web/images/treatments/skincare-ritual.jpg','assets/web/images/treatments/body-massage.jpg'];
            $cards = $featuredTreatments->isNotEmpty() ? $featuredTreatments : collect([
                (object)['id'=>null,'slug'=>null,'name'=>'Signature Clinical Facial','short_description'=>'Barrier-focused facial care with a considered clinical assessment.','duration_minutes'=>60,'image_path'=>$fallbackImages[0],'category'=>(object)['name'=>'Facial'],'price'=>450],
                (object)['id'=>null,'slug'=>null,'name'=>'Skin Clarity Protocol','short_description'=>'Targeted care designed around texture, congestion and clarity.','duration_minutes'=>75,'image_path'=>$fallbackImages[1],'category'=>(object)['name'=>'Skin'],'price'=>520],
                (object)['id'=>null,'slug'=>null,'name'=>'Body Contour Care','short_description'=>'Measured body treatment protocols with restorative support.','duration_minutes'=>90,'image_path'=>$fallbackImages[2],'category'=>(object)['name'=>'Body'],'price'=>680],
            ]);
        @endphp
        <div class="home-v3-treatments__grid">
            @foreach($cards as $index => $treatment)
                @php
                    $href = $treatment->slug ? route('web.treatments.show', $treatment->slug) : route('web.treatments.index');
                    $bookHref = !empty($treatment->id) ? route('web.booking.create', ['treatment_id'=>$treatment->id]) : route('web.booking.create');
                    $price = method_exists($treatment, 'effectivePrice') ? $treatment->effectivePrice() : ($treatment->price ?? 0);
                    $img = $treatment->image_path ?: $fallbackImages[$index % 3];
                    $img = str_starts_with($img, 'assets/') ? asset($img) : asset('storage/'.$img);
                @endphp
                <article class="home-v3-treatment reveal" data-parallax-card>
                    <a href="{{ $href }}" class="home-v3-treatment__media"><img src="{{ $img }}" alt="{{ $treatment->name }}" loading="lazy"></a>
                    <div class="home-v3-treatment__body">
                        <p><span>{{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}</span>{{ $treatment->category->name ?? 'Treatment' }}</p>
                        <h3>{{ $treatment->name }}</h3>
                        <p>{{ $treatment->short_description }}</p>
                        <div><span>{{ $treatment->duration_minutes }} min · GHS {{ number_format((float)$price, 2) }}</span><a href="{{ $bookHref }}">Book →</a></div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="home-v3-founder">
    <div class="container-site home-v3-founder__grid">
        <div class="home-v3-founder__visual reveal">
            <div class="home-v3-founder__image home-v3-founder__image--main" data-parallax-image><img src="{{ asset($ceo?->photo_path ?? config('clinic.ceo.portrait_a')) }}" alt="{{ $ceo?->user?->name ?? config('clinic.ceo.name') }}"></div>
            <div class="home-v3-founder__image home-v3-founder__image--small" data-parallax-image><img src="{{ asset(config('clinic.ceo.portrait_b')) }}" alt="{{ config('clinic.ceo.name') }}" loading="lazy"></div>
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
        <img src="{{ asset('assets/web/images/hero/hero-beauty-academy.jpg') }}" alt="De Luxe professional aesthetics academy" loading="lazy">
        <div><p class="text-label">De Luxe Academy</p><h2>Learn the science.<br>Master the technique.</h2><p>Clinic-led training in Botox, fillers, skin science and advanced procedures—with practical support beyond certification.</p><a href="{{ route('web.academy.index') }}" class="btn btn-light">Explore training</a></div>
    </article>
    <article class="home-v3-duo__panel home-v3-duo__panel--store reveal reveal-delay-2">
        <img src="{{ asset('assets/web/images/treatments/skincare-ritual.jpg') }}" alt="Curated De Luxe skincare products" loading="lazy">
        <div><p class="text-label">The De Luxe Edit</p><h2>Your routine,<br>carefully considered.</h2><p>Discover clinic-selected skincare and beauty essentials, available through secure Paystack checkout.</p><a href="{{ route('web.store.index') }}" class="btn btn-light">Shop products</a></div>
    </article>
</section>

<section class="home-v3-proof">
    <div class="container-site">
        <div class="home-v3-proof__stats reveal">
            <div><strong data-count="1200">0</strong><span>Clients cared for</span></div>
            <div><strong data-count="35">0</strong><span>Treatment protocols</span></div>
            <div><strong data-count="180">0</strong><span>Students trained</span></div>
            <div><strong data-count="8">0</strong><span>Years of expertise</span></div>
        </div>
        <header class="home-v3-heading reveal"><div><p class="text-label">Client words</p><h2>Care people remember.</h2></div></header>
        <div class="home-v3-testimonials">
            @foreach(__('web.home.testimonials') as $index => $quote)
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
    <div class="home-v3-cta__image" data-parallax-image><img src="{{ asset('assets/web/images/gallery/clinic-ambiance.jpg') }}" alt="The welcoming De Luxe clinic environment" loading="lazy"></div>
    <div class="home-v3-cta__veil"></div>
    <div class="container-site home-v3-cta__content reveal">
        <p class="text-label">Begin with a conversation</p>
        <h2>Your next step<br>can feel beautifully simple.</h2>
        <p>Book a consultation and let our team guide you toward the treatment, training or care that suits you best.</p>
        <div><a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book consultation</a><a href="{{ route('web.contact') }}" class="btn btn-light">Contact De Luxe</a></div>
    </div>
</section>
@endsection
