@extends('web.layouts.app')
@section('title', $course->name.' — '.config('clinic.name'))
@section('meta_description', $course->seo_description ?: \Illuminate\Support\Str::limit(strip_tags($course->description), 155, ''))
@section('meta_image', $course->imageUrl() ?: asset(config('seo.default_image')))
@section('meta_image_alt', $course->name)
@push('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Course',
    'name' => $course->name,
    'description' => $course->seo_description ?: strip_tags($course->description),
    'provider' => [
        '@type' => 'EducationalOrganization',
        'name' => config('clinic.name').' Academy',
        'sameAs' => url('/'),
    ],
    'url' => route('web.courses.show', $course->slug),
    'image' => $course->imageUrl(),
    'inLanguage' => str_replace('_', '-', app()->getLocale()),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@section('content')
@php
    $whatsapp = preg_replace('/\D+/', '', (string) config('clinic.whatsapp'));
    $whatsappText = rawurlencode('Hello '.__('web.pages.academy_title').', I would like course information about '.$course->name.'.');
@endphp
<section class="section">
    <div class="container-site max-w-5xl">
        <p class="text-label mb-3">{{ $course->category?->name }}</p>
        <h1 class="text-page-title mb-4">{{ $course->name }}</h1>
        <div class="panel mb-8 border-[var(--color-bronze)] p-5">
            <p class="font-medium mb-1">Physical enrolment only</p>
            <p class="text-[var(--color-soft-grey)]">
                Course enrolment is completed physically at our academy. Please contact our admissions team or visit our location to complete registration.
            </p>
        </div>
        <div class="grid gap-8 lg:grid-cols-[1.1fr,0.9fr]">
            <div class="space-y-6">
                <p class="text-[var(--color-soft-grey)]">{{ $course->description }}</p>
                @if($course->entry_requirements)
                    <div>
                        <p class="text-label mb-2">Entry requirements</p>
                        <p class="text-[var(--color-soft-grey)]">{{ $course->entry_requirements }}</p>
                    </div>
                @endif
                <ul class="text-[var(--color-soft-grey)] space-y-2">
                    <li>Duration: {{ $course->duration_hours }} hours</li>
                    <li>Delivery: {{ ucfirst($course->delivery_mode) }}</li>
                    @if($course->venue)<li>Location: {{ $course->venue }}</li>@endif
                    <li>Fee: GHS {{ number_format((float) $course->fee, 2) }}</li>
                </ul>
            </div>
            <aside class="panel p-6 space-y-4">
                <h2 class="text-section">Admissions actions</h2>
                <a href="{{ route('web.enrol', ['course' => $course->id]) }}" class="btn btn-primary w-full text-center">Request course information</a>
                @if($whatsapp)
                    <a class="btn btn-whatsapp w-full text-center" href="https://wa.me/{{ $whatsapp }}?text={{ $whatsappText }}" target="_blank" rel="noopener">Chat on WhatsApp</a>
                @endif
                <a href="tel:{{ preg_replace('/\s+/', '', (string) config('clinic.phone')) }}" class="btn btn-secondary w-full text-center">Call academy</a>
                <a href="{{ config('clinic.map_link') }}" target="_blank" rel="noopener" class="btn btn-secondary w-full text-center">View academy location</a>
            </aside>
        </div>
    </div>
</section>
@endsection
