@extends('student.layouts.app')
@section('title', __('student.nav.calendar'))
@section('heading', __('student.nav.calendar'))
@section('content')
<div class="student-panel mb-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-label">Physical class schedule</p><h2 class="font-display text-2xl mt-1">{{ $enrolment->course?->name }}</h2><p class="mt-2 text-[var(--color-soft-grey)]">Your academy class appointments, practical sessions and assessments.</p></div>
        <div class="text-right"><p class="text-label">Upcoming</p><p class="font-display text-3xl">{{ $upcomingSessions->count() }}</p></div>
    </div>
</div>

<div class="grid gap-4 mb-8">
    @forelse($upcomingSessions as $session)
        <article class="student-panel grid gap-4 md:grid-cols-[8rem_1fr_auto] md:items-center">
            <div><p class="text-label">{{ $session->session_date?->format('M') }}</p><p class="font-display text-4xl">{{ $session->session_date?->format('d') }}</p><p class="text-sm">{{ $session->session_date?->format('l') }}</p></div>
            <div><div class="flex flex-wrap gap-2 mb-2">@if($session->is_practical)<span class="student-badge">Practical</span>@endif @if($session->is_assessment)<span class="student-badge">Assessment</span>@endif <span class="student-badge">{{ ucfirst($session->status) }}</span></div><h3 class="font-display text-xl">{{ $session->topic ?: 'Training session' }}</h3><p class="mt-1">{{ $session->starts_at ? substr($session->starts_at,0,5) : 'Time TBC' }}@if($session->ends_at) – {{ substr($session->ends_at,0,5) }}@endif</p>@if($session->location)<p class="text-sm text-[var(--color-soft-grey)] mt-1">Location: {{ $session->location }}</p>@endif @if($session->announcement)<p class="mt-3">{{ $session->announcement }}</p>@endif</div>
            <div class="md:text-right"><p class="text-sm {{ $session->status === 'cancelled' ? 'text-[var(--color-error)]' : 'text-[var(--color-soft-grey)]' }}">{{ $session->session_date?->isToday() ? 'Today' : $session->session_date?->diffForHumans() }}</p></div>
        </article>
    @empty
        <div class="student-panel"><p class="font-display text-xl">No upcoming classes</p><p class="mt-2 text-[var(--color-soft-grey)]">The academy will notify you when your next physical class is scheduled.</p></div>
    @endforelse
</div>

@if($pastSessions->isNotEmpty())
<div class="student-panel student-table-wrap"><h2 class="font-display text-xl mb-4">Previous classes</h2><table class="student-table"><thead><tr><th>Date</th><th>Time</th><th>Topic</th><th>Status</th></tr></thead><tbody>@foreach($pastSessions as $session)<tr><td>{{ $session->session_date?->format('d M Y') }}</td><td>{{ $session->starts_at ? substr($session->starts_at,0,5) : '—' }}</td><td>{{ $session->topic ?: 'Training session' }}</td><td>{{ ucfirst($session->status) }}</td></tr>@endforeach</tbody></table></div>
@endif
@endsection
