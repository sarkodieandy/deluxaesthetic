@extends('admin.layouts.app')
@section('title','Course enquiries')
@section('heading','Course enquiries')
@section('breadcrumb','Academy / Course enquiries')
@section('content')
@if(session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
<div class="admin-panel mb-6">
    <div class="admin-panel__head"><div><h2 class="admin-panel__title">Admissions enquiries</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Review, contact and qualify prospective physical-training students.</p></div></div>
    <div class="admin-panel__body">
        <form method="GET" class="grid gap-3 md:grid-cols-[1fr_14rem_auto]">
            <input name="q" value="{{ request('q') }}" class="admin-input" placeholder="Search name, email or phone">
            <select name="status" class="admin-input"><option value="">All statuses</option>@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select>
            <button class="btn btn-secondary">Filter</button>
        </form>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table"><thead><tr><th>Applicant</th><th>Course</th><th>Contact</th><th>Status</th><th>Received</th><th></th></tr></thead><tbody>
        @forelse($enquiries as $enquiry)
            <tr>
                <td><strong>{{ $enquiry->full_name }}</strong><br><span class="text-sm text-[var(--admin-text-muted)]">{{ $enquiry->email }}</span></td>
                <td>{{ $enquiry->course?->name ?? 'General academy enquiry' }}</td>
                <td>{{ $enquiry->phone }}<br><span class="text-sm text-[var(--admin-text-muted)]">{{ ucfirst($enquiry->preferred_contact_method) }}</span></td>
                <td><span class="admin-status {{ in_array($enquiry->status, ['qualified','converted'], true) ? 'admin-status--success' : ($enquiry->status === 'submitted' ? 'admin-status--warning' : 'admin-status--info') }}">{{ $statuses[$enquiry->status] ?? ucfirst($enquiry->status) }}</span></td>
                <td>{{ $enquiry->created_at->format('d M Y') }}</td>
                <td class="text-right"><a href="{{ route('admin.course-enquiries.show', $enquiry) }}" class="btn btn-secondary">Review &amp; edit</a></td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="admin-empty"><p class="admin-empty__title">No enquiries found</p><p class="admin-empty__copy">New submissions from the academy enquiry form will appear here.</p></div></td></tr>
        @endforelse
        </tbody></table>
    </div>
</div>
{{ $enquiries->links() }}
@endsection
