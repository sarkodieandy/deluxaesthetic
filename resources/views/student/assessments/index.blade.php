@extends('student.layouts.app')
@section('title', __('student.nav.assessments'))
@section('heading', __('student.nav.assessments'))
@section('content')
<div class="student-panel">
    @forelse($results as $result)
        <p class="mb-3 last:mb-0">{{ $result->assessment?->title }} — {{ $result->score }}/{{ $result->assessment?->max_score }} ({{ ucfirst($result->status) }})</p>
    @empty
        <p class="text-[var(--color-soft-grey)]">No results published yet.</p>
    @endforelse
</div>
@endsection
