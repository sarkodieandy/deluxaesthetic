@extends('admin.layouts.app')
@section('title', 'Enrolments')
@section('heading', 'Enrolments')
@section('breadcrumb', 'Academy / Enrolments')
@section('content')
@if (session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
<div class="admin-panel mb-6"><div class="admin-panel__head"><h2 class="admin-panel__title">Course enrolments</h2></div><div class="admin-panel__body" style="padding:0;"><table class="admin-table"><thead><tr><th>Reference</th><th>Student</th><th>Course</th><th>Status</th><th>Balance</th><th></th></tr></thead><tbody>@forelse($enrolments as $enrolment)<tr><td>{{ $enrolment->reference }}</td><td>{{ $enrolment->student_name }}</td><td>{{ $enrolment->course_name }}</td><td><span class="admin-status admin-status--info">{{ ucfirst(str_replace('_', ' ', $enrolment->status)) }}</span></td><td>GHS {{ number_format((float)$enrolment->outstanding_balance,2) }}</td><td class="text-right"><a href="{{ route('admin.enrolments.edit', $enrolment->id) }}" class="btn btn-secondary">Manage</a></td></tr>@empty<tr><td colspan="6"><div class="admin-empty"><p class="admin-empty__title">No enrolments yet</p><p class="admin-empty__copy">Student enrolments will appear here.</p></div></td></tr>@endforelse</tbody></table></div></div>{{ $enrolments->links() }}
@endsection
