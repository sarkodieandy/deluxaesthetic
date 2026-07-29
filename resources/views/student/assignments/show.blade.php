@extends('student.layouts.app')
@section('title',$assignment->title)
@section('heading',$assignment->title)
@section('content')
<div class="border border-[var(--color-border)] bg-white p-6 mb-6"><p>{{ $assignment->instructions }}</p></div>
<form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="border border-[var(--color-border)] bg-white p-6 space-y-4">@csrf
<textarea name="notes" class="field w-full" rows="4" placeholder="Comments">{{ old('notes', $submission?->notes) }}</textarea>
<input type="file" name="file" class="field w-full">
<button type="submit" class="student-action">Submit assignment</button>
</form>
@endsection
