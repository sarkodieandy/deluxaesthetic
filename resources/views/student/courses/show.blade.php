@extends('student.layouts.app')
@section('title','My courses')
@section('heading','My courses')
@section('eyebrow','Course workspace')
@section('content')
<div class="grid gap-5 lg:grid-cols-2">
    @foreach($enrolments as $courseEnrolment)
        <article class="border border-[var(--color-border)] bg-white p-6 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-label">{{ $courseEnrolment->course?->category?->name }}</p>
                <span class="student-badge">{{ ucfirst(str_replace('_',' ', $courseEnrolment->status)) }}</span>
            </div>
            <h2 class="font-display text-3xl">{{ $courseEnrolment->course?->name }}</h2>
            <p class="text-[var(--color-soft-grey)]">{{ $courseEnrolment->course?->description }}</p>
            <ul class="text-[var(--color-soft-grey)] space-y-1">
                <li>Trainer: {{ $courseEnrolment->course?->trainer?->user?->name ?? 'Assigned by academy' }}</li>
                <li>Location: {{ $courseEnrolment->course?->venue ?? config('clinic.address') }}</li>
                <li>Enrolment date: {{ $courseEnrolment->enrolment_date?->format('d M Y') ?? '—' }}</li>
                <li>Reference: {{ $courseEnrolment->reference }}</li>
            </ul>
        </article>
    @endforeach
</div>
<p class="mt-5 text-sm text-[var(--color-soft-grey)]">Course changes must be requested through Support — students cannot self-enrol or transfer online.</p>
@endsection
