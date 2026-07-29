@extends('student.layouts.app')
@section('title', __('student.nav.calendar'))
@section('heading', __('student.nav.calendar'))
@section('content')
<div class="student-panel student-table-wrap">
    <table class="student-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Topic</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
                <tr>
                    <td>{{ $session->session_date?->format('d M Y') }}</td>
                    <td>{{ $session->starts_at }} – {{ $session->ends_at }}</td>
                    <td>{{ $session->topic ?: 'Session' }}</td>
                    <td>{{ ucfirst($session->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No sessions scheduled yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
