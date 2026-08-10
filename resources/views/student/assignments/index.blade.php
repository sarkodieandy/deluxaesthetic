@extends('student.layouts.app')
@section('title', __('student.nav.assignments'))
@section('heading', __('student.nav.assignments'))
@section('content')
<div class="student-panel">
    @forelse($assignments as $assignment)
        <div class="mb-4 border-b border-[var(--color-border)] pb-4 last:mb-0 last:border-0 last:pb-0">
            <p class="text-label mb-1">{{ $assignment->course?->name }}</p>
            <p>
                <a href="{{ route('student.assignments.show', $assignment) }}" class="underline underline-offset-2">{{ $assignment->title }}</a>
                · due {{ $assignment->due_at?->format('d M Y') ?? 'TBC' }}
            </p>
        </div>
    @empty
        <p class="text-[var(--color-soft-grey)]">No assignments published.</p>
    @endforelse
</div>
@endsection
