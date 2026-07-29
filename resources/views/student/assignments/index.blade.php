@extends('student.layouts.app')
@section('title', __('student.nav.assignments'))
@section('heading', __('student.nav.assignments'))
@section('content')
<div class="student-panel">
    @forelse($assignments as $assignment)
        <p class="mb-3 last:mb-0">
            <a href="{{ route('student.assignments.show', $assignment) }}" class="underline underline-offset-2">{{ $assignment->title }}</a>
            · due {{ $assignment->due_at?->format('d M Y') ?? 'TBC' }}
        </p>
    @empty
        <p class="text-[var(--color-soft-grey)]">No assignments published.</p>
    @endforelse
</div>
@endsection
