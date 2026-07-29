@extends('admin.layouts.app')
@section('title', 'Certificate')
@section('heading', 'Certificate '.$certificate->number)
@section('breadcrumb', 'Academy / Certificates / Manage')
@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif
<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Certificate details</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div><p class="text-label mb-1">Student</p><p>{{ $certificate->student_name }}</p></div>
        <div><p class="text-label mb-1">Course</p><p>{{ $certificate->course_name }}</p></div>
        <div><p class="text-label mb-1">Completion date</p><p>{{ $certificate->completion_date?->format('d M Y') }}</p></div>
        <div><p class="text-label mb-1">Verification code</p><p>{{ $certificate->verification_code }}</p></div>
    </div>
</div>
<form method="POST" action="{{ route('admin.certificates.update', $certificate) }}" class="admin-form">
    @csrf
    @method('PUT')
    <div class="admin-panel mb-6">
        <div class="admin-panel__head"><h2 class="admin-panel__title">Certificate workflow</h2></div>
        <div class="admin-panel__body grid gap-4 md:grid-cols-2">
            <div>
                <label class="admin-label" for="status">Status</label>
                <select id="status" name="status" class="admin-input">
                    @foreach(['draft','issued','revoked'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $certificate->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="signatory">Signatory</label>
                <input id="signatory" name="signatory" class="admin-input" value="{{ old('signatory', $certificate->signatory) }}">
            </div>
        </div>
    </div>
    <div class="flex flex-wrap gap-3">
        <button type="submit" class="btn btn-primary">Save certificate</button>
        @if($certificate->isDownloadable())
            <a href="{{ route('admin.certificates.download', $certificate) }}" class="btn btn-secondary">Download PDF</a>
        @endif
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">Back</a>
    </div>
</form>
@endsection
