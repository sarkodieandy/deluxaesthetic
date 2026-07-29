@extends('student.layouts.app')
@section('title', __('student.nav.attendance'))
@section('heading', __('student.nav.attendance'))
@section('content')
<div class="student-metric-grid mb-6">
    <div class="student-metric"><p class="student-metric__label">Present</p><p class="student-metric__value">{{ $summary['present'] }}/{{ $summary['total'] }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Percentage</p><p class="student-metric__value">{{ $summary['percentage'] ?? '—' }}@if($summary['percentage'] !== null)%@endif</p></div>
</div>
<div class="student-panel">
    <ul class="m-0 p-0 list-none">
        @forelse($records as $record)
            <li class="py-2 border-b border-[var(--color-border)] last:border-0">{{ $record->session_date?->format('d M Y') }} — {{ ucfirst($record->status) }}</li>
        @empty
            <li class="text-[var(--color-soft-grey)]">No attendance records yet.</li>
        @endforelse
    </ul>
</div>
@endsection
