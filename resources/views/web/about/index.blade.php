@extends('web.layouts.app')

@section('title', __('web.about.title').' — '.config('clinic.name'))
@section('meta_description', 'Discover the story, philosophy and clinical leadership behind '.config('clinic.name').' in Accra, Ghana.')

@section('content')
<section class="about-v2-hero">
    <div class="container-site about-v2-hero__grid">
        <div class="about-v2-hero__copy reveal">
            <p class="text-label">Our story</p>
            <h1>Beauty, handled<br>with <em>intention.</em></h1>
            <p>De Luxe is an expert-led aesthetic clinic and academy where thoughtful care, professional education and a refined understanding of beauty come together.</p>
            <div class="about-v2-hero__actions">
                <a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book a consultation</a>
                <a href="#our-story" class="btn btn-secondary">Discover our story</a>
            </div>
        </div>
        <div class="about-v2-hero__visual reveal reveal-delay-2">
            <div class="about-v2-hero__image about-v2-hero__image--main"><img src="{{ $portraits['a'] }}" alt="{{ $ceo?->user?->name ?? config('clinic.ceo.name') }}"></div>
            <div class="about-v2-hero__image about-v2-hero__image--secondary"><img src="{{ asset('assets/web/images/gallery/clinic-ambiance.webp') }}" alt="The De Luxe clinic interior" decoding="async"></div>
            <div class="about-v2-hero__mark"><span>Clinic</span><i>·</i><span>Academy</span><i>·</i><span>Store</span></div>
        </div>
    </div>
</section>

<section class="about-v2-intro" id="our-story">
    <div class="container-site about-v2-intro__grid">
        <div class="reveal">
            <p class="text-label">The De Luxe difference</p>
            <h2>We believe expertise should feel personal.</h2>
        </div>
        <div class="reveal reveal-delay-2">
            <p class="about-v2-intro__lead">The clinic was built around a simple idea: people deserve aesthetic care that listens before it recommends, explains before it treats and supports long after the appointment ends.</p>
            <p>That same philosophy extends through our professional academy and curated store. Everything we offer is connected by one standard—knowledge, honesty and care delivered with intention.</p>
        </div>
    </div>
</section>

<section class="about-v2-story">
    <div class="container-site about-v2-story__grid">
        <div class="about-v2-story__media reveal">
            <img src="{{ asset('assets/web/images/hero/spa-treatment-room.webp') }}" alt="A calm treatment room at De Luxe Aesthetic Clinic" loading="lazy" decoding="async">
            <span>East Legon · Accra</span>
        </div>
        <div class="about-v2-story__content reveal reveal-delay-2">
            <p class="text-label">More than a clinic</p>
            <h2>A place to receive care—and learn how to give it.</h2>
            <p>De Luxe brings three complementary experiences under one roof. Our clinic provides personalised aesthetic and restorative treatments. Our academy prepares practitioners through hands-on, safety-led education. Our store helps clients continue thoughtful care at home.</p>
            <div class="about-v2-story__pathways">
                <a href="{{ route('web.treatments.index') }}"><span>01</span><div><strong>Clinic</strong><small>Personalised treatments and restorative care</small></div><i>→</i></a>
                <a href="{{ route('web.academy.index') }}"><span>02</span><div><strong>Academy</strong><small>Practical training, certification and mentorship</small></div><i>→</i></a>
                <a href="{{ route('web.store.index') }}"><span>03</span><div><strong>Store</strong><small>Clinic-selected skincare and beauty essentials</small></div><i>→</i></a>
            </div>
        </div>
    </div>
</section>

<section class="about-v2-founder">
    <div class="container-site about-v2-founder__grid">
        <div class="about-v2-founder__copy reveal">
            <p class="text-label">Founder & clinical leadership</p>
            <h2>{{ $ceo?->user?->name ?? config('clinic.ceo.name') }}</h2>
            <p class="about-v2-founder__title">{{ $ceo?->displayTitle() ?? config('clinic.ceo.title') }}</p>
            <blockquote>“The best aesthetic work does not ask someone to become different. It helps them feel more confident in what is already theirs.”</blockquote>
            <p>{{ $ceo?->biography ?? __('web.home.founder_bio') }}</p>
            @if(!empty($ceo?->qualifications) || !empty($ceo?->certifications))
                <div class="about-v2-founder__credentials">
                    <span>Professional credentials</span>
                    <p>{{ collect(array_merge($ceo?->qualifications ?? [], $ceo?->certifications ?? []))->map(fn($item) => is_array($item) ? ($item['name'] ?? reset($item)) : $item)->take(4)->join(' · ') }}</p>
                </div>
            @endif
            <div class="about-v2-founder__actions">
                <a href="{{ route('web.practitioners.index') }}" class="btn btn-primary">Meet our practitioners</a>
                <a href="{{ route('web.booking.create', $ceo ? ['practitioner_id' => $ceo->id] : []) }}" class="btn btn-secondary">Book consultation</a>
            </div>
        </div>
        <div class="about-v2-founder__visual reveal reveal-delay-2">
            <img src="{{ $portraits['b'] }}" alt="{{ $ceo?->user?->name ?? config('clinic.ceo.name') }}" loading="lazy">
            <div><span>Clinical vision</span><strong>Natural balance.<br>Professional care.</strong></div>
        </div>
    </div>
</section>

<section class="about-v2-values">
    <div class="container-site">
        <header class="about-v2-heading reveal">
            <div><p class="text-label">What guides us</p><h2>Values that shape<br>every experience.</h2></div>
            <p>From a first consultation to student mentorship, these principles define how De Luxe works.</p>
        </header>
        <div class="about-v2-values__grid">
            <article class="reveal"><span>01</span><h3>Listen first</h3><p>We begin with the individual—their goals, concerns, comfort and right to clear information.</p></article>
            <article class="reveal"><span>02</span><h3>Practise precisely</h3><p>Our work is grounded in assessment, safety-led protocols and considered clinical judgement.</p></article>
            <article class="reveal"><span>03</span><h3>Respect natural beauty</h3><p>We favour balance and proportion over excess, with results that remain recognisably yours.</p></article>
            <article class="reveal"><span>04</span><h3>Share knowledge</h3><p>Education strengthens our profession, our students and the quality of care clients receive.</p></article>
        </div>
    </div>
</section>

<section class="about-v2-experience">
    <div class="about-v2-experience__image"><img src="{{ asset('assets/web/images/gallery/clinic-ambiance.webp') }}" alt="The De Luxe clinic atmosphere" loading="lazy" decoding="async"></div>
    <div class="about-v2-experience__veil"></div>
    <div class="container-site about-v2-experience__content reveal">
        <p class="text-label">The De Luxe experience</p>
        <h2>Clinical clarity.<br>Quiet confidence.</h2>
        <p>Our spaces, conversations and treatments are designed to help you feel informed, comfortable and genuinely cared for.</p>
        <a href="{{ route('web.gallery') }}" class="btn btn-light">Explore our clinic</a>
    </div>
</section>

<section class="about-v2-cta">
    <div class="container-site about-v2-cta__inner reveal">
        <p class="text-label">Begin your De Luxe journey</p>
        <h2>Come as you are.<br>Leave feeling considered.</h2>
        <p>Book a consultation, explore professional training or speak with our team about the right next step.</p>
        <div><a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book consultation</a><a href="{{ route('web.contact') }}" class="btn btn-secondary">Contact us</a></div>
    </div>
</section>
@endsection
