@extends('admin.layouts.app')
@section('title', 'Enrolment')
@section('heading', 'Enrolment '.$enrolment->reference)
@section('breadcrumb', 'Academy / Enrolments / Manage')
@section('content')
@if (session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif

@if (! in_array($enrolment->status, ['active', 'graduated', 'completed', 'certificate_issued'], true))
    @can('enrolments.activate')
        <div class="admin-callout">
            <div>
                <p class="admin-callout__title">Activate physical enrolment</p>
                <p class="admin-callout__copy">Activates the student account, marks enrolment active, and sends a secure portal invitation.</p>
            </div>
            <form method="POST" action="{{ route('admin.physical-enrolment.activate', $enrolment) }}">
                @csrf
                <button type="submit" class="btn btn-primary">Activate &amp; invite to portal</button>
            </form>
        </div>
    @endcan
@endif

<form method="POST" action="{{ route('admin.enrolments.update', $enrolment) }}" class="admin-form">@csrf @method('PUT')
<div class="grid gap-6 lg:grid-cols-[0.95fr,1.05fr]">
    <div class="admin-panel">
        <div class="admin-panel__head"><h2 class="admin-panel__title">Enrolment details</h2></div>
        <div class="admin-panel__body space-y-4">
            <div><p class="text-label mb-1">Student</p><p>{{ $studentName }} · {{ $studentEmail }}</p></div>
            <div><p class="text-label mb-1">Course</p><p>{{ $courseName }}</p></div>
            <div><p class="text-label mb-1">Reference</p><p>{{ $enrolment->reference }}</p></div>
            @if($enrolment->enrolment_date)<div><p class="text-label mb-1">Enrolment date</p><p>{{ $enrolment->enrolment_date->format('d M Y') }}</p></div>@endif
        </div>
    </div>
    <div class="admin-panel">
        <div class="admin-panel__head"><h2 class="admin-panel__title">Workflow</h2></div>
        <div class="admin-panel__body grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="admin-label" for="status">Status</label>
                <select id="status" name="status" class="admin-input">
                    @foreach(\App\Enums\EnrolmentStatus::cases() as $statusCase)
                        <option value="{{ $statusCase->value }}" @selected(old('status', $enrolment->status) === $statusCase->value)>{{ ucfirst(str_replace('_', ' ', $statusCase->value)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="amount_paid">Amount paid</label>
                <input id="amount_paid" name="amount_paid" type="number" step="0.01" min="0" class="admin-input" value="{{ old('amount_paid', $enrolment->amount_paid) }}">
            </div>
            <div>
                <label class="admin-label" for="outstanding_balance">Outstanding balance</label>
                <input id="outstanding_balance" name="outstanding_balance" type="number" step="0.01" min="0" class="admin-input" value="{{ old('outstanding_balance', $enrolment->outstanding_balance) }}">
            </div>
        </div>
    </div>
</div>
<div class="admin-actions mt-6">
    <button type="submit" class="btn btn-primary">Save enrolment</button>
    <a href="{{ route('admin.enrolments.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('admin.physical-enrolment.create', ['student' => $enrolment->student_profile_id]) }}" class="btn btn-secondary">New enrolment for student</a>
</div>
</form>
@endsection
