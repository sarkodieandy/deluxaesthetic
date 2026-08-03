@extends('student.layouts.app')
@section('title',$assignment->title)
@section('heading',$assignment->title)
@section('content')
<div class="border border-[var(--color-border)] bg-white p-6 mb-6">
    <p>{{ $assignment->instructions }}</p>
    @if($assignment->attachment_path)
        <a class="student-action mt-4 inline-flex" href="{{ route('student.assignments.download', $assignment) }}">Download assignment file</a>
    @endif
</div>
@if($submission?->score !== null || $submission?->feedback)
    <div class="student-panel mb-6">
        <p class="font-medium">Trainer feedback</p>
        @if($submission->score !== null)
            <p>Score: {{ $submission->score }}</p>
        @endif
        @if($submission->feedback)
            <p class="mt-2">{{ $submission->feedback }}</p>
        @endif
    </div>
@endif
<form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="border border-[var(--color-border)] bg-white p-6 space-y-4">@csrf
<textarea name="notes" class="field w-full" rows="4" placeholder="Comments">{{ old('notes', $submission?->notes) }}</textarea>
<input type="file" name="file" class="field w-full">
<button type="submit" class="student-action">Submit assignment</button>
</form>
@endsection
