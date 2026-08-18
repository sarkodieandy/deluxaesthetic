@extends('student.layouts.app')
@section('title', __('student.nav.attendance'))
@section('heading', __('student.nav.attendance'))
@section('content')
@if($enrolments->count() > 1)
<nav class="student-panel mb-6" aria-label="Select course attendance">
    <p class="text-label mb-3">Course attendance</p>
    <div class="flex flex-wrap gap-2">
        @foreach($enrolments as $item)
            <a class="student-action {{ $item->is($enrolment) ? 'student-action--active' : '' }}" href="{{ route('student.attendance.index', ['enrolment' => $item->id]) }}">{{ $item->course?->name }}</a>
        @endforeach
    </div>
</nav>
@endif
<p class="mb-4 text-sm text-[var(--color-soft-grey)]">Attendance for <strong>{{ $enrolment->course?->name }}</strong></p>
<div class="student-metric-grid mb-6">
    <div class="student-metric"><p class="student-metric__label">Present</p><p class="student-metric__value">{{ $summary['present'] }}/{{ $summary['total'] }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Percentage</p><p class="student-metric__value">{{ $summary['percentage'] ?? '—' }}@if($summary['percentage'] !== null)%@endif</p></div>
</div>
<div class="student-panel">
    <ul class="m-0 p-0 list-none">
        @forelse($records as $record)
            <li class="py-2 border-b border-[var(--color-border)] last:border-0"><span class="font-medium">{{ $record->session_date?->format('d M Y') }}</span> — {{ ucfirst($record->status) }}@if($record->notes)<p class="text-sm text-[var(--color-soft-grey)]">{{ $record->notes }}</p>@endif</li>
        @empty
            <li class="text-[var(--color-soft-grey)]">No attendance records yet.</li>
        @endforelse
    </ul>
</div>
@endsection
