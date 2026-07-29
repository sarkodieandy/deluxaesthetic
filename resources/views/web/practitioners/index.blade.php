@extends('web.layouts.app')

@section('title', __('web.pages.practitioners_title').' — '.config('clinic.name'))
@section('meta_description', __('web.pages.practitioners_lead'))

@section('content')
<section class="practitioners-v2-hero">
    <div class="container-site practitioners-v2-hero__grid">
        <div class="practitioners-v2-hero__copy reveal">
            <p class="text-label">The people behind your care</p>
            <h1>Expert hands.<br><em>Considered care.</em></h1>
            <p>Meet the practitioners who bring clinical knowledge, thoughtful consultation and an exacting standard of care to every De Luxe experience.</p>
            <div class="practitioners-v2-hero__actions">
                <a href="#team" class="btn btn-primary">Meet the team</a>
                <a href="{{ route('web.booking.create') }}" class="btn btn-secondary">Book a consultation</a>
            </div>
            <div class="practitioners-v2-hero__proof">
                <div><strong>{{ max(1, $practitioners->count()) }}</strong><span>Expert practitioners</span></div>
                <div><strong>1:1</strong><span>Personal consultation</span></div>
                <div><strong>360°</strong><span>Continuity of care</span></div>
            </div>
        </div>
        <div class="practitioners-v2-hero__visual reveal reveal-delay-2">
            <img src="{{ $practitioners->first()?->photoUrl() ?? asset(config('clinic.ceo.portrait_a')) }}" alt="{{ $practitioners->first()?->user?->name ?? config('clinic.ceo.name') }}">
            <div class="practitioners-v2-hero__caption">
                <span>Clinical leadership</span>
                <strong>{{ $practitioners->first()?->user?->name ?? config('clinic.ceo.name') }}</strong>
                <small>{{ $practitioners->first()?->displayTitle() ?? config('clinic.ceo.title') }}</small>
            </div>
        </div>
    </div>
</section>

<section class="practitioners-v2-philosophy">
    <div class="container-site practitioners-v2-philosophy__grid">
        <div class="reveal">
            <p class="text-label">Our approach</p>
            <h2>Care begins before the treatment does.</h2>
        </div>
        <div class="reveal reveal-delay-2">
            <p>Every appointment begins with listening. Our practitioners take time to understand your goals, explain suitable options and create a plan that feels proportionate, safe and genuinely personal.</p>
            <div>
                <span>Consult</span><i aria-hidden="true">→</i>
                <span>Plan</span><i aria-hidden="true">→</i>
                <span>Treat</span><i aria-hidden="true">→</i>
                <span>Review</span>
            </div>
        </div>
    </div>
</section>

<section class="practitioners-v2-team" id="team">
    <div class="container-site">
        <header class="practitioners-v2-heading reveal">
            <div><p class="text-label">De Luxe practitioners</p><h2>Choose the expertise<br>that suits your journey.</h2></div>
            <p>Each member of our team contributes a distinct clinical perspective while working within one shared standard of assessment, safety and aftercare.</p>
        </header>

        <div class="practitioners-v2-list">
            @forelse($practitioners as $index => $member)
                <article class="practitioners-v2-profile reveal {{ $index % 2 ? 'is-reverse' : '' }}">
                    <div class="practitioners-v2-profile__media">
                        <img src="{{ $member->photoUrl() }}" alt="{{ $member->user?->name }}" loading="lazy">
                        <span>{{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="practitioners-v2-profile__content">
                        <div class="practitioners-v2-profile__title">
                            <div>
                                <p class="text-label">{{ $member->is_ceo ? 'Founder & clinical lead' : 'De Luxe practitioner' }}</p>
                                <h3>{{ $member->user?->name }}</h3>
                                <p>{{ $member->displayTitle() }}</p>
                            </div>
                            @if($member->years_experience)
                                <div class="practitioners-v2-profile__experience"><strong>{{ $member->years_experience }}+</strong><span>Years<br>experience</span></div>
                            @endif
                        </div>

                        @if($member->biography)
                            <p class="practitioners-v2-profile__bio">{{ $member->biography }}</p>
                        @else
                            <p class="practitioners-v2-profile__bio">Providing thoughtful, personalised aesthetic care with a focus on natural-looking results, client comfort and professional aftercare.</p>
                        @endif

                        @if(!empty($member->specialities))
                            <div class="practitioners-v2-profile__specialities">
                                <span>Areas of focus</span>
                                <ul>
                                    @foreach(array_slice($member->specialities, 0, 6) as $speciality)
                                        <li>{{ is_array($speciality) ? ($speciality['name'] ?? reset($speciality)) : $speciality }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($member->qualifications) || !empty($member->certifications))
                            <div class="practitioners-v2-profile__credentials">
                                <span>Professional credentials</span>
                                <p>{{ collect(array_merge($member->qualifications ?? [], $member->certifications ?? []))->map(fn($item) => is_array($item) ? ($item['name'] ?? reset($item)) : $item)->take(3)->join(' · ') }}</p>
                            </div>
                        @endif

                        <div class="practitioners-v2-profile__actions">
                            <a href="{{ route('web.booking.create', ['practitioner_id' => $member->id]) }}" class="btn btn-primary">Book with {{ \Illuminate\Support\Str::before($member->user?->name ?? 'practitioner', ' ') }}</a>
                            @if($member->social('instagram'))
                                <a href="{{ $member->social('instagram') }}" target="_blank" rel="noopener noreferrer" class="practitioners-v2-profile__social">Instagram ↗</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="practitioners-v2-empty">
                    <h3>{{ __('web.team.empty') }}</h3>
                    <p>{{ __('web.team.empty_copy') }}</p>
                    <a href="{{ route('web.contact') }}" class="btn btn-primary">Contact the clinic</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="practitioners-v2-standards">
    <div class="container-site">
        <header class="practitioners-v2-heading practitioners-v2-heading--light reveal">
            <div><p class="text-label">The De Luxe standard</p><h2>What you can expect<br>from every appointment.</h2></div>
        </header>
        <div class="practitioners-v2-standards__grid">
            <article class="reveal"><span>01</span><h3>Honest consultation</h3><p>Clear recommendations shaped around your goals, suitability and comfort—not pressure.</p></article>
            <article class="reveal"><span>02</span><h3>Clinical precision</h3><p>Measured protocols, hygienic practice and careful attention throughout your treatment.</p></article>
            <article class="reveal"><span>03</span><h3>Natural balance</h3><p>A refined approach that respects your features and prioritises results that still feel like you.</p></article>
            <article class="reveal"><span>04</span><h3>Continued aftercare</h3><p>Practical guidance, review and support beyond the treatment room.</p></article>
        </div>
    </div>
</section>

<section class="practitioners-v2-cta">
    <div class="practitioners-v2-cta__image"><img src="{{ asset('assets/web/images/gallery/clinic-ambiance.jpg') }}" alt="The De Luxe clinic environment" loading="lazy"></div>
    <div class="practitioners-v2-cta__veil"></div>
    <div class="container-site practitioners-v2-cta__content reveal">
        <p class="text-label">Your care starts here</p>
        <h2>Meet the right practitioner<br>for your goals.</h2>
        <p>Choose a practitioner during booking, or begin with a consultation and let our team guide you.</p>
        <div><a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book an appointment</a><a href="{{ route('web.contact') }}" class="btn btn-light">Ask a question</a></div>
    </div>
</section>
@endsection
