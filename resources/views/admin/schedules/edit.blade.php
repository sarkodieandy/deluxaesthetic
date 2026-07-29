@extends('admin.layouts.app')
@section('title', 'Edit schedule')
@section('heading', 'Edit working hours')
@section('breadcrumb', 'Clinic / Schedules / Edit')
@section('content')
@if ($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="admin-form">
    @csrf
    @method('PUT')
    <div class="admin-panel mb-6">
        <div class="admin-panel__head"><h2 class="admin-panel__title">Working hours</h2></div>
        <div class="admin-panel__body grid gap-4 md:grid-cols-2">
            <div>
                <label class="admin-label" for="practitioner_profile_id">Practitioner</label>
                <select id="practitioner_profile_id" name="practitioner_profile_id" class="admin-input" required>
                    @foreach ($practitioners as $practitioner)
                        <option value="{{ $practitioner->id }}" @selected(old('practitioner_profile_id', $schedule->practitioner_profile_id) == $practitioner->id)>
                            {{ $practitioner->user?->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="branch_id">Branch</label>
                <select id="branch_id" name="branch_id" class="admin-input" required>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id', $schedule->branch_id) == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="day_of_week">Day</label>
                <select id="day_of_week" name="day_of_week" class="admin-input" required>
                    @foreach ($dayLabels as $value => $label)
                        <option value="{{ $value }}" @selected((string) old('day_of_week', $schedule->day_of_week) === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <label class="inline-flex items-center gap-2 text-sm mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $schedule->is_active))>
                    Active (offers online slots)
                </label>
            </div>
            <div>
                <label class="admin-label" for="starts_at">Starts</label>
                <input id="starts_at" name="starts_at" type="time" class="admin-input" value="{{ old('starts_at', \Illuminate\Support\Str::of((string) $schedule->starts_at)->substr(0, 5)) }}" required>
            </div>
            <div>
                <label class="admin-label" for="ends_at">Ends</label>
                <input id="ends_at" name="ends_at" type="time" class="admin-input" value="{{ old('ends_at', \Illuminate\Support\Str::of((string) $schedule->ends_at)->substr(0, 5)) }}" required>
            </div>
        </div>
    </div>
    <div class="flex flex-wrap gap-3">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">Back</a>
    </div>
</form>
@endsection
