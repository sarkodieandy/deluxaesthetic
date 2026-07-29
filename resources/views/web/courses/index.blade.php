@extends('web.layouts.app')
@section('title', __('web.pages.courses_title').' — '.config('clinic.name'))
@section('content')
@include('web.components.page-intro', [
    'eyebrow' => __('web.pages.courses_eyebrow'),
    'title' => __('web.pages.courses_title'),
    'lead' => __('web.pages.courses_lead'),
])
<section class="catalogue-shell">
    <div class="container-site">
        <p class="mb-8 max-w-3xl text-[var(--color-soft-grey)]">
            Course enrolment is completed physically at our academy. Browse programmes below, then contact admissions or visit our location to register.
        </p>
        @if ($courses->isEmpty())
            <div class="catalogue-empty">
                <p class="font-display text-2xl mb-2">{{ __('web.pages.coming_soon') }}</p>
                <a href="{{ route('web.enrol') }}" class="btn btn-primary">Request course information</a>
            </div>
        @else
            <div class="grid gap-0 border border-[var(--color-border)] md:grid-cols-2">
                @foreach ($courses as $course)
                    <article class="border-[var(--color-border)] bg-white p-6 md:border-r md:border-b">
                        @if ($course->imageUrl())
                            <div class="mb-5 overflow-hidden border border-[var(--color-border)]">
                                <img src="{{ $course->imageUrl() }}" alt="" class="h-56 w-full object-cover" loading="lazy">
                            </div>
                        @endif
                        <p class="text-label mb-2">{{ $course->category?->name }}</p>
                        <h2 class="font-display text-2xl mb-3"><a href="{{ route('web.courses.show', $course->slug) }}">{{ $course->name }}</a></h2>
                        <p class="mb-4 text-[var(--color-soft-grey)]">{{ \Illuminate\Support\Str::limit($course->description, 160) }}</p>
                        <p class="mb-4">GHS {{ number_format((float) $course->fee, 2) }} · {{ $course->duration_hours }} hours</p>
                        <a href="{{ route('web.courses.show', $course->slug) }}" class="btn btn-secondary">View course</a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
