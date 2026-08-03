@extends('student.layouts.app')
@section('title', 'Student overview')
@section('heading', 'Welcome, '.$user->name)
@section('eyebrow', 'Student portal')
@section('content')
@if($enrolment)
<div class="student-metric-grid mb-8">
    <div class="student-metric"><p class="student-metric__label">Course</p><p class="student-metric__value" style="font-size:1.1rem;">{{ $enrolment->course?->name }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Status</p><p class="student-metric__value" style="font-size:1.1rem;">{{ ucfirst(str_replace('_',' ', $enrolment->status)) }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Attendance</p><p class="student-metric__value">{{ $metrics['attendance_percentage'] !== null ? $metrics['attendance_percentage'].'%' : '—' }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Outstanding</p><p class="student-metric__value">GHS {{ number_format($metrics['outstanding_balance'], 2) }}</p></div>
</div>
<div class="student-metric-grid mb-8" style="grid-template-columns: repeat(3, 1fr);">
    <div class="student-metric"><p class="student-metric__label">Pending assignments</p><p class="student-metric__value">{{ $metrics['pending_assignments'] }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Certificates</p><p class="student-metric__value">{{ $metrics['certificates'] }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Unread</p><p class="student-metric__value">{{ $metrics['unread_notifications'] }}</p></div>
</div>
<div class="grid gap-6 lg:grid-cols-2 mb-8">
    <div class="student-panel">
        <p class="font-display text-xl mb-2">Next class</p>
        @if($metrics['upcoming_session'])
            <p class="font-medium">{{ $metrics['upcoming_session']->session_date?->format('l, d M Y') }}</p>
            <p>{{ $metrics['upcoming_session']->starts_at ? substr($metrics['upcoming_session']->starts_at, 0, 5) : 'Time TBC' }} · {{ $metrics['upcoming_session']->topic ?: 'Training session' }}</p>
            @if($metrics['upcoming_session']->location)<p class="text-sm text-[var(--color-soft-grey)] mt-1">{{ $metrics['upcoming_session']->location }}</p>@endif
            <a href="{{ route('student.calendar.index') }}" class="student-action mt-4">Open calendar</a>
        @else
            <p class="text-[var(--color-soft-grey)]">No upcoming session scheduled.</p>
        @endif
    </div>
    <div class="student-panel">
        <p class="font-display text-xl mb-2">Quick actions</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('student.course.show') }}" class="student-action">My course</a>
            <a href="{{ route('student.materials.index') }}" class="student-action">Materials</a>
            <a href="{{ route('student.assignments.index') }}" class="student-action">Assignments</a>
            <a href="{{ route('student.payments.index') }}" class="student-action">Payments</a>
            <a href="{{ route('student.certificates.index') }}" class="student-action">Certificates</a>
            <a href="{{ route('student.support.index') }}" class="student-action">Support</a>
        </div>
    </div>
</div>
@else
<div class="student-panel mb-6">
    <p class="font-display text-2xl mb-2">Portal pending activation</p>
    <p class="text-[var(--color-soft-grey)] mb-4">Your academy enrolment is not active yet. Complete your profile, then contact admissions after in-person registration so they can activate your course.</p>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('student.profile.edit') }}" class="student-action">Complete profile</a>
        <a href="{{ route('student.support.index') }}" class="student-action">Contact support</a>
        <a href="{{ route('student.certificates.index') }}" class="student-action">Certificates</a>
        <a href="{{ route('web.courses.index') }}" class="student-action">Browse courses</a>
    </div>
</div>
<div class="student-metric-grid">
    <div class="student-metric"><p class="student-metric__label">Student no.</p><p class="student-metric__value" style="font-size:1.1rem;">{{ $profile?->student_number ?? '—' }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Certificates</p><p class="student-metric__value">{{ $metrics['certificates'] ?? 0 }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Support</p><p class="student-metric__value" style="font-size:1.1rem;">Open</p></div>
    <div class="student-metric"><p class="student-metric__label">Status</p><p class="student-metric__value" style="font-size:1.1rem;">Awaiting enrolment</p></div>
</div>
@endif
@endsection
