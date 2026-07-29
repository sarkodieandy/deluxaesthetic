@extends('admin.layouts.app')
@section('title', 'Create certificate')
@section('heading', 'Create certificate')
@section('breadcrumb', 'Academy / Certificates / Create')
@section('content')
<form method="POST" action="{{ route('admin.certificates.store') }}" class="admin-form">
    @csrf
    @if ($errors->any())
        <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="admin-panel mb-6">
        <div class="admin-panel__head"><h2 class="admin-panel__title">Course completion</h2></div>
        <div class="admin-panel__body grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="admin-label" for="enrolment_id">Student enrolment</label>
                <select id="enrolment_id" name="enrolment_id" class="admin-input" required>
                    <option value="">Select completed enrolment</option>
                    @foreach ($enrolments as $enrolment)
                        <option value="{{ $enrolment->id }}" @selected(old('enrolment_id') == $enrolment->id)>
                            {{ $enrolment->studentProfile?->user?->name ?? 'Student' }}
                            — {{ $enrolment->course?->name ?? 'Course' }}
                            ({{ ucfirst(str_replace('_', ' ', $enrolment->status)) }})
                        </option>
                    @endforeach
                </select>
                @if($enrolments->isEmpty())
                    <p class="admin-empty__copy mt-3">No eligible enrolments yet. Mark an enrolment as completed in Academy → Enrolments first.</p>
                @endif
            </div>
            <div>
                <label class="admin-label" for="completion_date">Completion date</label>
                <input id="completion_date" name="completion_date" type="date" class="admin-input" value="{{ old('completion_date', now()->toDateString()) }}" required>
            </div>
            <div>
                <label class="admin-label" for="signatory">Signatory</label>
                <input id="signatory" name="signatory" class="admin-input" value="{{ old('signatory', $defaultSignatory) }}">
            </div>
            <div class="md:col-span-2">
                <label class="admin-check">
                    <input type="checkbox" name="issue_now" value="1" @checked(old('issue_now', true))>
                    Issue immediately and generate PDF for download
                </label>
            </div>
        </div>
    </div>
    <div class="flex flex-wrap gap-3">
        <button type="submit" class="btn btn-primary" @disabled($enrolments->isEmpty())>Create certificate</button>
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
