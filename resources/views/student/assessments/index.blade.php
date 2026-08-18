@extends('student.layouts.app')
@section('title', __('student.nav.assessments'))
@section('heading', __('student.nav.assessments'))
@section('content')
@if($enrolments->count() > 1)
<nav class="student-panel mb-6" aria-label="Select course results">
    <p class="text-label mb-3">Course results</p>
    <div class="flex flex-wrap gap-2">
        @foreach($enrolments as $item)
            <a class="student-action {{ $item->is($enrolment) ? 'student-action--active' : '' }}" href="{{ route('student.assessments.index', ['enrolment' => $item->id]) }}">{{ $item->course?->name }}</a>
        @endforeach
    </div>
</nav>
@endif
<div class="student-panel">
    <p class="font-display text-xl mb-4">{{ $enrolment->course?->name }}</p>
    @forelse($results as $result)
        <p class="mb-3 last:mb-0">{{ $result->assessment?->title }} — {{ $result->score }}/{{ $result->assessment?->max_score }} ({{ ucfirst($result->status) }})</p>
    @empty
        <p class="text-[var(--color-soft-grey)]">No results published yet.</p>
    @endforelse
</div>
@endsection
