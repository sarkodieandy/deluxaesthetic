@extends('student.layouts.app')
@section('title', $title)
@section('heading', $heading)
@section('content')
<div class="student-panel max-w-2xl">
    <p class="font-display text-2xl mb-3">{{ __('student.portal.section_restricted_title') }}</p>
    <p class="text-[var(--color-soft-grey)] mb-4">{{ __('student.portal.section_restricted_copy') }}</p>
    @if($enrolment ?? null)
        <p class="text-sm text-[var(--color-soft-grey)] mb-6">
            {{ __('student.nav.course') }}: <strong>{{ $enrolment->course?->name }}</strong>
            · {{ __('student.portal.status_label') }}: <strong>{{ ucfirst(str_replace('_', ' ', $enrolment->status)) }}</strong>
        </p>
    @endif
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('student.dashboard') }}" class="student-action">{{ __('student.portal.back_dashboard') }}</a>
        <a href="{{ route('student.support.index') }}" class="student-action">{{ __('student.nav.support') }}</a>
    </div>
</div>
@endsection
