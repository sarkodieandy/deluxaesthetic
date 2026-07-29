@extends('student.layouts.app')
@section('title','My course')
@section('heading','My course')
@section('eyebrow','Course workspace')
@section('content')
<div class="border border-[var(--color-border)] bg-white p-6 space-y-4">
    <p class="text-label">{{ $enrolment->course?->category?->name }}</p>
    <h2 class="font-display text-3xl">{{ $enrolment->course?->name }}</h2>
    <p class="text-[var(--color-soft-grey)]">{{ $enrolment->course?->description }}</p>
    <ul class="text-[var(--color-soft-grey)] space-y-1">
        <li>Trainer: {{ $enrolment->course?->trainer?->user?->name ?? 'Assigned by academy' }}</li>
        <li>Location: {{ $enrolment->course?->venue ?? config('clinic.address') }}</li>
        <li>Status: {{ ucfirst(str_replace('_',' ', $enrolment->status)) }}</li>
        <li>Enrolment date: {{ $enrolment->enrolment_date?->format('d M Y') ?? '—' }}</li>
    </ul>
    <p class="text-sm text-[var(--color-soft-grey)]">Course changes must be requested through Support — students cannot self-enrol or transfer online.</p>
</div>
@endsection
