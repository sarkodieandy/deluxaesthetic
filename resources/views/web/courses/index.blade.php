@extends('web.layouts.app')
@section('title', __('web.pages.courses_title').' — '.config('clinic.name'))
@section('meta_description', 'Browse professional aesthetics courses and practical beauty training programmes at De Luxe Aesthetic Academy in Accra.')
@section('content')
<section class="relative overflow-hidden bg-[#171714] py-20 text-white lg:py-28">
    <div class="absolute inset-0 opacity-25"><img src="{{ asset('assets/web/images/hero/hero-beauty-academy.webp') }}" alt="" class="h-full w-full object-cover"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
    <div class="container-site relative max-w-4xl">
        <p class="text-label text-white/70">De Luxe Academy</p>
        <h1 class="mt-5 font-display text-5xl leading-none md:text-7xl">Advanced Aesthetic Training Across Africa</h1>
        <p class="mt-6 max-w-2xl text-lg text-white/75">Develop practical knowledge, clinical confidence, and professional aesthetic skills through structured training programmes delivered by experienced practitioners.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="#courses" class="btn btn-primary">Explore classes</a>
            <a href="{{ route('web.academy.student-portal.create') }}" class="btn btn-secondary">Apply to the Academy</a>
        </div>
    </div>
</section>

<section class="section" id="courses">
    <div class="container-site">
        <div class="mb-10 max-w-3xl">
            <p class="text-label mb-3">Academy classes</p>
            <h2 class="text-page-title">Professional courses with physical enrolment and strong practical focus.</h2>
            <p class="mt-4 text-[var(--color-soft-grey)]">Course enrolment is completed physically at our academy. Browse the programmes below, then contact admissions or visit the academy to complete registration.</p>
        </div>

        @if ($courses->isEmpty())
            <div class="catalogue-empty">
                <p class="font-display text-2xl mb-2">{{ __('web.pages.coming_soon') }}</p>
                <a href="{{ route('web.academy.student-portal.create') }}" class="btn btn-primary">Apply to the Academy</a>
            </div>
        @else
            <div class="grid gap-0 border border-[var(--color-border)] md:grid-cols-2">
                @foreach ($courses as $course)
                    <article class="border-[var(--color-border)] bg-white p-8 md:border-r md:border-b">
                        @if ($course->imageUrl())
                            <div class="mb-5 overflow-hidden border border-[var(--color-border)]">
                                <img src="{{ $course->imageUrl() }}" alt="" class="h-56 w-full object-cover" loading="lazy">
                            </div>
                        @endif
                        <p class="text-label mb-2">{{ $course->category?->name }}</p>
                        <h2 class="font-display text-2xl mb-3"><a href="{{ route('web.courses.show', $course->slug) }}">{{ $course->name }}</a></h2>
                        <p class="mb-4 text-[var(--color-soft-grey)]">{{ \Illuminate\Support\Str::limit($course->description, 160) }}</p>
                        <p class="mb-4 text-lg font-semibold">{{ $course->formattedFee() }}@if($course->duration_hours > 0) · {{ $course->duration_hours }} hours @endif</p>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('web.courses.show', $course->slug) }}" class="btn btn-secondary">View course</a>
                            <a href="{{ route('web.academy.student-portal.create', ['course' => $course->id]) }}" class="btn btn-primary">Apply for this course</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
