@extends('admin.layouts.app')
@section('title', 'Students')
@section('heading', 'Student applications')
@section('breadcrumb', 'Academy / Students')
@section('content')
@if(session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
@if($errors->any())<div class="mb-4 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">{{ $errors->first() }}</div>@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div><h2 class="admin-panel__title">Admissions queue</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $pendingCount }} applicant{{ $pendingCount === 1 ? '' : 's' }} waiting for contact and approval.</p></div>
    </div>
    <div class="admin-panel__body">
        <form method="GET" class="grid gap-3 md:grid-cols-[1fr_12rem_auto]">
            <input name="q" value="{{ request('q') }}" class="admin-input" placeholder="Search name, email or phone">
            <select name="status" class="admin-input"><option value="pending" @selected($status === 'pending')>Pending approval</option><option value="active" @selected($status === 'active')>Active students</option><option value="all" @selected($status === 'all')>All students</option></select>
            <button class="btn btn-secondary">Filter</button>
        </form>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead><tr><th>Student</th><th>Contact</th><th>Applied</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($students as $profile)
                @php($student = $profile->user)
                <tr>
                    <td><strong>{{ $student?->name }}</strong><br><span class="text-sm text-[var(--admin-text-muted)]">{{ $profile->student_number }}</span></td>
                    <td><a href="mailto:{{ $student?->email }}">{{ $student?->email }}</a><br>@if($student?->phone)<a href="https://wa.me/{{ preg_replace('/\D+/', '', $student->phone) }}" target="_blank" rel="noopener">WhatsApp {{ $student->phone }}</a>@endif</td>
                    <td>{{ $profile->created_at->format('d M Y, H:i') }}</td>
                    <td><span class="admin-status {{ $student?->is_active ? 'admin-status--success' : 'admin-status--warning' }}">{{ $student?->is_active ? 'Portal active' : 'Awaiting approval' }}</span></td>
                    <td class="text-right">
                        @if($student && !$student->is_active)
                            @can('students.activate')
                            <form method="POST" action="{{ route('admin.students.approve', $student) }}" class="inline" onsubmit="return confirm('Confirm that admissions contacted this applicant and approve portal access?')">@csrf<input type="hidden" name="contact_confirmed" value="1"><button class="btn btn-primary btn-sm">Contacted · Approve access</button></form>
                            @endcan
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="admin-empty"><p class="admin-empty__title">No students in this view</p><p class="admin-empty__copy">New applications submitted from the Academy page appear here automatically.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $students->links() }}
@endsection
