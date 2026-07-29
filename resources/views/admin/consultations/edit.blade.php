@extends('admin.layouts.app')
@section('title', 'Consultation request')
@section('heading', 'Consultation request')
@section('breadcrumb', 'Clinic / Consultation Requests / Review')
@section('content')
@if (session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
@if ($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
<div class="grid gap-6 lg:grid-cols-[0.9fr,1.1fr]">
    <div class="admin-panel">
        <div class="admin-panel__head"><h2 class="admin-panel__title">Request details</h2></div>
        <div class="admin-panel__body space-y-4">
            <div><p class="text-label mb-1">Name</p><p>{{ $consultation->name }}</p></div>
            <div><p class="text-label mb-1">Email</p><p><a href="mailto:{{ $consultation->email }}">{{ $consultation->email }}</a></p></div>
            <div><p class="text-label mb-1">Phone</p><p>{{ $consultation->phone ?: '—' }}</p></div>
            <div><p class="text-label mb-1">Preferred contact</p><p>{{ ucfirst(str_replace('_', ' ', $consultation->preferred_channel)) }}</p></div>
            <div><p class="text-label mb-1">Preferred date</p><p>{{ $consultation->preferred_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-label mb-1">Submitted</p><p>{{ $consultation->created_at?->timezone(config('clinic.timezone'))->format('d M Y H:i') }}</p></div>
            <div><p class="text-label mb-1">Enquiry</p><div class="border border-[var(--admin-border)] bg-[var(--admin-surface-muted)] p-4 whitespace-pre-line">{{ $consultation->description }}</div></div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.consultations.update', $consultation) }}" class="admin-form">
        @csrf
        @method('PUT')
        <div class="admin-panel mb-6">
            <div class="admin-panel__head"><h2 class="admin-panel__title">Follow-up workflow</h2></div>
            <div class="admin-panel__body grid gap-4 md:grid-cols-2">
                <div>
                    <label class="admin-label" for="status">Status</label>
                    <select id="status" name="status" class="admin-input">
                        @foreach (['submitted' => 'Submitted', 'in_review' => 'In review', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $consultation->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label" for="assigned_to">Assign to</label>
                    <select id="assigned_to" name="assigned_to" class="admin-input">
                        <option value="">Unassigned</option>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}" @selected((string) old('assigned_to', $consultation->assigned_to) === (string) $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label" for="follow_up_date">Follow-up date</label>
                    <input id="follow_up_date" name="follow_up_date" type="date" class="admin-input" value="{{ old('follow_up_date', $consultation->follow_up_date?->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
        <div class="admin-panel mb-6">
            <div class="admin-panel__head"><h2 class="admin-panel__title">Internal notes</h2></div>
            <div class="admin-panel__body">
                <textarea id="internal_notes" name="internal_notes" rows="5" class="admin-input">{{ old('internal_notes', $consultation->internal_notes) }}</textarea>
            </div>
        </div>
        <div class="admin-panel">
            <div class="admin-panel__head"><h2 class="admin-panel__title">Client response summary</h2></div>
            <div class="admin-panel__body">
                <textarea id="client_response" name="client_response" rows="5" class="admin-input">{{ old('client_response', $consultation->client_response) }}</textarea>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 mt-6">
            <button type="submit" class="btn btn-primary">Save update</button>
            <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>
@endsection
