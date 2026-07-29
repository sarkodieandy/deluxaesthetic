@extends('admin.layouts.app')
@section('title', 'Training sessions')
@section('heading', $course?->name ?? 'Course schedule')
@section('breadcrumb', 'Academy / Courses / Schedule sessions')
@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Schedule</h2>
    </div>
    <div class="admin-panel__body space-y-1 text-sm">
        <p><span class="text-label">Course:</span> {{ $course?->name }}</p>
        <p><span class="text-label">Dates:</span> {{ $schedule->starts_on?->format('d M Y') }} – {{ $schedule->ends_on?->format('d M Y') }}</p>
        <p><span class="text-label">Capacity:</span> {{ $schedule->enrolled_count }} / {{ $schedule->capacity }}</p>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-[1.05fr,0.95fr]">
    <div class="admin-panel">
        <div class="admin-panel__head"><h2 class="admin-panel__title">Class sessions</h2></div>
        <div class="admin-panel__body" style="padding:0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Topic</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td>{{ $session->session_date?->format('d M Y') }}</td>
                            <td>{{ $session->starts_at ? substr($session->starts_at, 0, 5) : '—' }}@if($session->ends_at) – {{ substr($session->ends_at, 0, 5) }}@endif</td>
                            <td>{{ $session->topic ?: '—' }}</td>
                            <td>{{ $session->location ?: ($course?->venue ?: '—') }}</td>
                            <td><span class="admin-status admin-status--info">{{ ucfirst($session->status ?? 'scheduled') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="admin-empty" style="padding:1rem;">
                                    <p class="admin-empty__title">No sessions yet</p>
                                    <p class="admin-empty__copy">Add class dates so students see them on the training calendar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head"><h2 class="admin-panel__title">Add session</h2></div>
        <div class="admin-panel__body">
            <form method="POST" action="{{ route('admin.course-schedules.sessions.store', $schedule) }}" class="admin-form space-y-4">
                @csrf
                <div>
                    <label class="admin-label" for="session_date">Session date</label>
                    <input id="session_date" name="session_date" type="date" class="admin-input" value="{{ old('session_date') }}" required>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="admin-label" for="starts_at">Start time</label>
                        <input id="starts_at" name="starts_at" type="time" class="admin-input" value="{{ old('starts_at', '09:00') }}">
                    </div>
                    <div>
                        <label class="admin-label" for="ends_at">End time</label>
                        <input id="ends_at" name="ends_at" type="time" class="admin-input" value="{{ old('ends_at', '16:00') }}">
                    </div>
                </div>
                <div>
                    <label class="admin-label" for="topic">Topic</label>
                    <input id="topic" name="topic" type="text" class="admin-input" value="{{ old('topic') }}" placeholder="e.g. Skin anatomy practical">
                </div>
                <div>
                    <label class="admin-label" for="location">Location</label>
                    <input id="location" name="location" type="text" class="admin-input" value="{{ old('location', $course?->venue) }}">
                </div>
                <div>
                    <label class="admin-label" for="status">Status</label>
                    <select id="status" name="status" class="admin-input">
                        @foreach(['scheduled', 'rescheduled', 'cancelled', 'holiday'] as $status)
                            <option value="{{ $status }}" @selected(old('status', 'scheduled') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="admin-check"><input type="checkbox" name="is_practical" value="1" @checked(old('is_practical'))> Practical session</label>
                <label class="admin-check"><input type="checkbox" name="is_assessment" value="1" @checked(old('is_assessment'))> Assessment session</label>
                <div class="admin-actions">
                    <button type="submit" class="btn btn-primary">Add session</button>
                    <a href="{{ route('admin.courses.edit', $course?->id) }}" class="btn btn-secondary">Back to course</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
