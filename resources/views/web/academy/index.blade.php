@extends('web.layouts.app')
@section('title', 'Aesthetics Training & Masterclasses — '.config('clinic.name'))
@section('meta_description', 'Professional aesthetics education with clinic-led Botox, filler, skin and regenerative masterclasses delivered across West Africa.')
@section('content')

{{-- ===================== HERO ===================== --}}
<section class="academy-v2-hero">
    <div class="container-site academy-v2-hero__grid">
        <div class="academy-v2-hero__copy reveal">
            <p class="text-label">{{ $cmsPage?->hero_eyebrow ?: 'De Luxe Aesthetic Clinic & Academy' }}</p>
            <h1>@if($cmsPage?->hero_title){!! nl2br(e($cmsPage->hero_title)) !!}@else Learn the science.<br><em>Master the technique.</em>@endif</h1>
            <p class="academy-v2-hero__lead">{{ $cmsPage?->hero_body ?: 'Clinic-led aesthetics education built for confident, safe and commercially ready practitioners — from your first consultation to advanced injectable and skin procedures.' }}</p>
            <div class="academy-v2-hero__actions">
                <a href="{{ route('web.academy.student-portal.create') }}" class="btn btn-primary">Apply as a student</a>
                <a href="#course-outlines" class="btn btn-secondary">View course outlines</a>
            </div>
            <dl class="academy-v2-proof">
                <div><dt>Hands-on</dt><dd>Supervised clinical practice</dd></div>
                <div><dt>Certified</dt><dd>Recognised training pathway</dd></div>
                <div><dt>Supported</dt><dd>Lifetime mentorship</dd></div>
            </dl>
        </div>
        <div class="academy-v2-hero__visual reveal reveal-delay-2">
            <img src="{{ $cmsPage?->hero_image_url ?: asset('assets/web/images/academy/academy-hero.webp') }}" alt="A practitioner performing a professional facial treatment" width="1600" height="1067" decoding="async" fetchpriority="high">
            <div class="academy-v2-hero__badge">
                <span>Professional pathway</span>
                <strong>Aesthetics<br>Masterclass</strong>
            </div>
        </div>
    </div>
</section>

@php
    $trainingSteps = $showcase->get('training_step', collect());
    $stories = $showcase->get('student_story', collect());
    $skillReviews = $showcase->get('skill_review', collect());
    $videos = $showcase->get('student_video', collect());
    $certifications = $showcase->get('certification', collect());
    $experience = $showcase->get('experience', collect());
    $careerBenefits = $showcase->get('career_benefit', collect());
    $featuredCourses = $courses->where('is_featured', true)->values();
    $additionalCourses = $courses->where('is_featured', false)->values();
@endphp

{{-- ===================== TRAINING FORMAT ===================== --}}
@if($trainingSteps->isNotEmpty())
<section class="section bg-white" id="training-format">
    <div class="container-site">
        <header class="academy-v2-section-head">
            <div><p class="text-label">How training works</p><h2 class="text-section">A practical, clinic-led training format.</h2></div>
            <p>Each programme moves from essential theory into live demonstration, supervised practice and continued professional support.</p>
        </header>
        <div class="grid gap-px bg-[var(--color-border)] border border-[var(--color-border)] md:grid-cols-4">
            @foreach($trainingSteps as $i => $step)
                <article class="bg-white p-7">
                    <span class="text-label">{{ sprintf('%02d', $i + 1) }}</span>
                    <h3 class="mt-8 font-display text-2xl">{{ $step->title }}</h3>
                    @if($step->subtitle)<p class="mt-2 text-label">{{ $step->subtitle }}</p>@endif
                    <p class="mt-3 text-sm text-[var(--color-soft-grey)]">{{ $step->body }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== COURSE OUTLINES ===================== --}}
@if($featuredCourses->isNotEmpty())
<section class="academy-courses-section" id="course-outlines">
    <div class="container-site">
        <header class="academy-v2-section-head">
            <div>
                <p class="text-label">Course outlines & pricing</p>
                <h2 class="text-section">Choose your training level.</h2>
            </div>
            <p>Each class combines theory, live demonstration and supervised hands-on practice. Contact admissions to confirm upcoming dates in your region.</p>
        </header>


        <div class="academy-course-collection" aria-label="Featured Academy courses">
            @foreach($featuredCourses as $i => $course)
            <article class="academy-course-premium reveal">
                <a class="academy-course-premium__media" href="{{ route('web.courses.show', $course->slug) }}" aria-label="View {{ $course->name }} course details">
                    <img
                        src="{{ $course->imageUrl() ?: asset('assets/web/images/academy/academy-training.webp') }}"
                        alt="Practical training for {{ $course->name }}"
                        width="1600"
                        height="1067"
                        loading="lazy"
                        decoding="async"
                    >
                    <span class="academy-course-premium__number" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                    <span class="academy-course-premium__level">{{ $course->category?->name ?: 'Professional masterclass' }}</span>
                </a>

                <div class="academy-course-premium__content">
                    <header class="academy-course-premium__header">
                        <div>
                            <p class="text-label">Physical, clinic-led training</p>
                            <h3 id="course-title-{{ $course->id }}"><a href="{{ route('web.courses.show', $course->slug) }}">{{ $course->name }}</a></h3>
                        </div>
                        <p class="academy-course-premium__price"><span>Course fee</span>{{ $course->formattedFee() }}</p>
                    </header>

                    <p class="academy-course-premium__description">{{ $course->description }}</p>

                    <dl class="academy-course-premium__facts">
                        <div><dt>Format</dt><dd>{{ ucfirst($course->delivery_mode ?: 'physical') }}</dd></div>
                        @if($course->duration_hours > 0)<div><dt>Duration</dt><dd>{{ $course->duration_hours }} hours</dd></div>@endif
                        <div><dt>Curriculum</dt><dd>{{ count($course->learning_outcomes ?? []) }} modules</dd></div>
                    </dl>

                    @if(!empty($course->learning_outcomes))
                    <section class="academy-course-premium__curriculum" aria-labelledby="course-outline-title-{{ $course->id }}">
                        <div class="academy-course-premium__curriculum-head">
                            <p class="text-label" id="course-outline-title-{{ $course->id }}">What you will learn</p>
                            <span>{{ count($course->learning_outcomes) }} focused modules</span>
                        </div>
                        <ol class="academy-course-premium__modules">
                            @foreach($course->learning_outcomes as $m => $module)
                            <li class="academy-course-module">
                                <span class="academy-course-module__number" aria-hidden="true">{{ sprintf('%02d', $m + 1) }}</span>
                                <div>
                                    <h4>{{ $module['name'] ?? 'Training module' }}</h4>
                                    @if(!empty($module['topics']))
                                    <ul>
                                        @foreach($module['topics'] as $topic)
                                            <li>{{ $topic }}</li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ol>
                    </section>
                    @endif

                    <footer class="academy-course-premium__actions">
                        <a href="{{ route('web.courses.show', $course->slug) }}" class="btn btn-secondary">Explore course details</a>
                        <a href="{{ route('web.academy.student-portal.create', ['course' => $course->id]) }}" class="btn btn-primary">Apply for {{ $course->name }}</a>
                    </footer>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== DB COURSES (admin-managed) ===================== --}}
@if($additionalCourses->isNotEmpty())
<section class="section bg-white" id="course-fees">
    <div class="container-site">
        <header class="academy-v2-section-head">
            <div><p class="text-label">Enrol now</p><h2 class="text-section">Available courses with confirmed fees.</h2></div>
            <p>Physical training in Accra. Admissions confirms dates, requirements and availability before portal approval.</p>
        </header>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($additionalCourses as $course)
                <article class="group flex flex-col border border-[var(--color-border)] bg-white p-7 transition hover:-translate-y-1 hover:shadow-xl">
                    <p class="text-label">{{ $course->category?->name ?: 'Professional training' }}</p>
                    <h3 class="mt-5 font-display text-3xl"><a href="{{ route('web.courses.show', $course->slug) }}">{{ $course->name }}</a></h3>
                    <p class="mt-4 flex-1 text-[var(--color-soft-grey)]">{{ \Illuminate\Support\Str::limit($course->description, 150) }}</p>
                    <div class="mt-7 border-t border-[var(--color-border)] pt-5">
                        @if($course->duration_hours > 0)<p class="text-sm text-[var(--color-soft-grey)]">{{ $course->duration_hours }} hours · {{ ucfirst($course->delivery_mode) }}</p>@endif
                        <p class="mt-2 font-display text-2xl">{{ $course->formattedFee() }}</p>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3"><a class="btn btn-secondary" href="{{ route('web.courses.show', $course->slug) }}">View outline</a><a class="btn btn-primary" href="{{ route('web.academy.student-portal.create', ['course' => $course->id]) }}">Apply for this course</a></div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== PAST STUDENTS ===================== --}}
@if($stories->isNotEmpty())
<section class="section bg-white" id="past-students">
    <div class="container-site">
        <header class="academy-v2-section-head">
            <div><p class="text-label">Past students</p><h2 class="text-section">Practitioners shaped through real, supervised training.</h2></div>
            <p>Verified student stories and Academy milestones published by the De Luxe admissions team.</p>
        </header>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($stories as $story)
                <article class="border border-[var(--color-border)] bg-[var(--color-stone)] p-7">
                    @if($story->imageUrl())<img src="{{ $story->imageUrl() }}" alt="{{ $story->title }}" class="mb-6 h-64 w-full object-cover" loading="lazy">@endif
                    <p class="font-display text-2xl">{{ $story->body }}</p>
                    <p class="mt-6 text-label">{{ $story->title }}@if($story->subtitle) · {{ $story->subtitle }}@endif</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== SKILL REVIEWS ===================== --}}
@if($skillReviews->isNotEmpty())
<section class="section bg-[var(--color-stone)]" id="skill-reviews">
    <div class="container-site">
        <header class="academy-v2-section-head">
            <div><p class="text-label">Skill reviews</p><h2 class="text-section">Confidence built one practical skill at a time.</h2></div>
            <p>First-hand reflections from students about technique, supervision and professional growth.</p>
        </header>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($skillReviews as $review)
                <blockquote class="border border-[var(--color-border)] bg-white p-7">
                    <p class="font-display text-2xl">“{{ $review->body }}”</p>
                    <footer class="mt-6 text-label">{{ $review->title }}@if($review->subtitle) · {{ $review->subtitle }}@endif</footer>
                </blockquote>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== STUDENT VIDEOS ===================== --}}
@if($videos->isNotEmpty())
<section class="section bg-white" id="student-videos">
    <div class="container-site">
        <header class="academy-v2-section-head">
            <div><p class="text-label">Student videos</p><h2 class="text-section">See the Academy experience in motion.</h2></div>
            <p>Training moments, student progress and practical reflections selected by the Academy team.</p>
        </header>
        <div class="grid gap-6 md:grid-cols-2">
            @foreach($videos as $video)
                @php($embedUrl = $video->videoEmbedUrl())
                <article class="overflow-hidden border border-[var(--color-border)] bg-[#171714] text-white">
                    @if($embedUrl)
                        <div class="aspect-video bg-black"><iframe src="{{ $embedUrl }}" title="{{ $video->title }}" class="h-full w-full" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>
                    @else
                        <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer" class="group relative block aspect-video overflow-hidden bg-black">
                            @if($video->imageUrl())<img src="{{ $video->imageUrl() }}" alt="{{ $video->title }}" class="absolute inset-0 h-full w-full object-cover opacity-70 transition group-hover:scale-105" loading="lazy">@endif
                            <span class="absolute inset-0 grid place-items-center"><span class="grid h-16 w-16 place-items-center rounded-full border border-white/70 bg-black/30 text-xl">▶</span></span>
                        </a>
                    @endif
                    <div class="p-7"><h3 class="font-display text-3xl">{{ $video->title }}</h3>@if($video->subtitle)<p class="mt-2 text-white/70">{{ $video->subtitle }}</p>@endif @if($video->body)<p class="mt-4 text-sm leading-6 text-white/60">{{ $video->body }}</p>@endif</div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
{{-- ===================== CERTIFICATIONS & EXPERIENCE ===================== --}}
@if($certifications->isNotEmpty() || $experience->isNotEmpty())
<section class="section bg-[#171714] text-white" id="academy-credentials">
    <div class="container-site">
        @if($certifications->isNotEmpty())
            <header class="academy-v2-section-head academy-v2-section-head--light">
                <div><p class="text-label">Certifications</p><h2 class="text-section">Professional achievement, clearly presented.</h2></div>
                <p>Academy certifications and recognition published and maintained by the De Luxe team.</p>
            </header>
            <div class="grid gap-px border border-white/20 bg-white/20 md:grid-cols-3">
                @foreach($certifications as $proof)
                    <article class="bg-[#171714] p-7">
                        @if($proof->imageUrl())<img src="{{ $proof->imageUrl() }}" alt="{{ $proof->title }}" class="mb-6 h-40 w-full object-cover" loading="lazy">@endif
                        <p class="text-label text-white/60">Certification</p>
                        <h3 class="mt-3 font-display text-3xl">{{ $proof->title }}</h3>
                        @if($proof->subtitle)<p class="mt-3 font-medium text-white/85">{{ $proof->subtitle }}</p>@endif
                        @if($proof->body)<p class="mt-3 text-white/70">{{ $proof->body }}</p>@endif
                    </article>
                @endforeach
            </div>
        @endif

        @if($experience->isNotEmpty())
            <header class="academy-v2-section-head academy-v2-section-head--light {{ $certifications->isNotEmpty() ? 'mt-20' : '' }}">
                <div><p class="text-label">Industry experience</p><h2 class="text-section">Experience that supports every student.</h2></div>
                <p>Verified training milestones and practical experience from the Academy team.</p>
            </header>
            <div class="grid gap-px border border-white/20 bg-white/20 md:grid-cols-3">
                @foreach($experience as $proof)
                    <article class="bg-[#171714] p-7">
                        @if($proof->imageUrl())<img src="{{ $proof->imageUrl() }}" alt="{{ $proof->title }}" class="mb-6 h-40 w-full object-cover" loading="lazy">@endif
                        <p class="text-label text-white/60">Training experience</p>
                        <h3 class="mt-3 font-display text-3xl">{{ $proof->title }}</h3>
                        @if($proof->subtitle)<p class="mt-3 font-medium text-white/85">{{ $proof->subtitle }}</p>@endif
                        @if($proof->body)<p class="mt-3 text-white/70">{{ $proof->body }}</p>@endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif
{{-- ===================== CEO / LEAD TRAINER ===================== --}}
<section class="academy-v2-botox">
    <div class="container-site academy-v2-botox__grid">
        <div class="academy-v2-botox__visual reveal">
            <img src="{{ $ceo?->photo_path ? $ceo->photoUrl() : asset('assets/web/images/team/ceo-academy-portrait.webp') }}" alt="{{ $ceo?->user?->name ?: config('clinic.ceo.name', 'Lead trainer at De Luxe Academy') }}" loading="lazy" decoding="async" style="object-position: 50% 18%;">
            <div class="academy-v2-botox__stamp">
                <span>Lead trainer</span>
                <strong>De Luxe<br>Academy</strong>
            </div>
        </div>
        <div class="academy-v2-botox__content reveal reveal-delay-2">
            <p class="text-label">Led by experience</p>
            <h2 class="text-section">Training you can trust, delivered by a practitioner you can follow.</h2>
            <p class="academy-v2-botox__lead">{{ $ceo?->biography ?: 'Every masterclass is led by the founder of De Luxe, combining hands-on clinical experience with practical aesthetics education and continued student support.' }}</p>
            <div class="academy-v2-botox__topics">
                <article><span>01</span><div><h3>Injectable expertise</h3><p>Botox, dermal fillers, PDO threads and advanced body techniques.</p></div></article>
                <article><span>02</span><div><h3>Skin & regeneration</h3><p>Microneedling, PRP, mesotherapy, chemical peels and hyperpigmentation.</p></div></article>
                <article><span>03</span><div><h3>Safety-led practice</h3><p>Assessment, anatomy, complication prevention and aftercare built into every pathway.</p></div></article>
                <article><span>04</span><div><h3>Lifetime support</h3><p>Mentorship, referral network and internationally recognised certifications.</p></div></article>
            </div>
            <a href="{{ route('web.academy.student-portal.create') }}" class="btn btn-primary">Apply to train with us</a>
        </div>
    </div>
</section>

{{-- ===================== CAREER / BEYOND TRAINING ===================== --}}
@if($careerBenefits->isNotEmpty())
<section class="academy-v2-career">
    <div class="container-site academy-v2-career__grid">
        <div class="academy-v2-career__image reveal">
            <img src="{{ $careerBenefits->first(fn ($benefit) => $benefit->imageUrl())?->imageUrl() ?: asset('assets/web/images/academy/academy-training.webp') }}" alt="Beauty professional receiving hands-on training" loading="lazy" decoding="async">
        </div>
        <div class="academy-v2-career__copy reveal reveal-delay-2">
            <p class="text-label">Beyond the treatment room</p>
            <h2 class="text-section">We help you prepare for the industry.</h2>
            <p>Technical ability matters, but a sustainable practice needs confidence, connections and professional presentation. Your academy experience can include:</p>
            <div class="academy-v2-career__list">
                @foreach($careerBenefits as $i => $benefit)
                    <p><span>{{ sprintf('%02d', $i + 1) }}</span><span><strong class="block text-[var(--color-ink)]">{{ $benefit->title }}</strong><small class="mt-1 block leading-6 text-[var(--color-soft-grey)]">{{ $benefit->body }}</small></span></p>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ===================== FINAL CTA ===================== --}}
<section class="academy-v2-final">
    <div class="container-site academy-v2-final__inner">
        <p class="text-label">Your next chapter</p>
        <h2>Ready to train with De Luxe?</h2>
        <p>Submit one student application and tell us which procedures interest you. Admissions will contact you before approving portal access.</p>
        <p class="academy-v2-final__portal">Already approved? Use Student login to enter your portal.</p>
        <div>
            <a href="{{ route('web.academy.student-portal.create') }}" class="btn btn-primary">Submit student application</a>
            <a href="{{ route('login') }}" class="btn btn-secondary">Student login</a>
        </div>
    </div>
</section>
@endsection
