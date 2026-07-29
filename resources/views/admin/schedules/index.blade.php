@extends('admin.layouts.app')
@section('title', 'Schedules')
@section('heading', 'Schedules')
@section('breadcrumb', 'Clinic / Schedules')
@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif
@if ($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="GET" action="{{ route('admin.schedules.index') }}" class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Filter</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-3 items-end">
        <div>
            <label class="admin-label" for="filter_practitioner">Practitioner</label>
            <select id="filter_practitioner" name="practitioner_profile_id" class="admin-input">
                <option value="">All practitioners</option>
                @foreach ($practitioners as $practitioner)
                    <option value="{{ $practitioner->id }}" @selected((string) $filters['practitioner_profile_id'] === (string) $practitioner->id)>
                        {{ $practitioner->user?->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label" for="filter_branch">Branch</label>
            <select id="filter_branch" name="branch_id" class="admin-input">
                <option value="">All branches</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected((string) $filters['branch_id'] === (string) $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="btn btn-secondary">Apply</button>
            <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">Clear</a>
        </div>
    </div>
</form>

<form method="POST" action="{{ route('admin.schedules.store') }}" class="admin-panel mb-6">
    @csrf
    <div class="admin-panel__head"><h2 class="admin-panel__title">Add working hours</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-3">
        <div>
            <label class="admin-label" for="practitioner_profile_id">Practitioner</label>
            <select id="practitioner_profile_id" name="practitioner_profile_id" class="admin-input" required>
                <option value="">Select</option>
                @foreach ($practitioners as $practitioner)
                    <option value="{{ $practitioner->id }}" @selected(old('practitioner_profile_id', $filters['practitioner_profile_id']) == $practitioner->id)>
                        {{ $practitioner->user?->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label" for="branch_id">Branch</label>
            <select id="branch_id" name="branch_id" class="admin-input" required>
                <option value="">Select</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id', $filters['branch_id'] ?: $branches->first()?->id) == $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label" for="day_of_week">Day</label>
            <select id="day_of_week" name="day_of_week" class="admin-input" required>
                @foreach ($dayLabels as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('day_of_week', '1') === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label" for="starts_at">Starts</label>
            <input id="starts_at" name="starts_at" type="time" class="admin-input" value="{{ old('starts_at', '09:00') }}" required>
        </div>
        <div>
            <label class="admin-label" for="ends_at">Ends</label>
            <input id="ends_at" name="ends_at" type="time" class="admin-input" value="{{ old('ends_at', '17:00') }}" required>
        </div>
            <div class="flex items-end">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                    Active (offers online slots)
                </label>
            </div>
    </div>
    <div class="admin-panel__body pt-0">
        <button type="submit" class="btn btn-primary">Save hours</button>
    </div>
</form>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Weekly hours</h2></div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Practitioner</th>
                    <th>Branch</th>
                    <th>Day</th>
                    <th>Hours</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->practitioner?->user?->name }}</td>
                        <td>{{ $schedule->branch?->name }}</td>
                        <td>{{ $dayLabels[$schedule->day_of_week] ?? $schedule->day_of_week }}</td>
                        <td>{{ \Illuminate\Support\Str::of((string) $schedule->starts_at)->substr(0, 5) }} – {{ \Illuminate\Support\Str::of((string) $schedule->ends_at)->substr(0, 5) }}</td>
                        <td>
                            @if ($schedule->is_active)
                                <span class="admin-status admin-status--success">Active</span>
                            @else
                                <span class="admin-status admin-status--warning">Inactive</span>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-secondary">Edit</a>
                            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Remove these working hours?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No working hours yet</p>
                                <p class="admin-empty__copy">Add practitioner hours so the website booking page can offer live time slots.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $schedules->links() }}

<form method="POST" action="{{ route('admin.schedules.blocked.store') }}" class="admin-panel mb-6">
    @csrf
    <div class="admin-panel__head"><h2 class="admin-panel__title">Block dates (leave / unavailable)</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div>
            <label class="admin-label" for="blocked_practitioner">Practitioner</label>
            <select id="blocked_practitioner" name="practitioner_profile_id" class="admin-input" required>
                <option value="">Select</option>
                @foreach ($practitioners as $practitioner)
                    <option value="{{ $practitioner->id }}" @selected(old('practitioner_profile_id', $filters['practitioner_profile_id']) == $practitioner->id)>
                        {{ $practitioner->user?->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label" for="reason">Reason</label>
            <input id="reason" name="reason" class="admin-input" value="{{ old('reason') }}" placeholder="Annual leave, training day…">
        </div>
        <div>
            <label class="admin-label" for="starts_on">From</label>
            <input id="starts_on" name="starts_on" type="date" class="admin-input" value="{{ old('starts_on') }}" required>
        </div>
        <div>
            <label class="admin-label" for="ends_on">To</label>
            <input id="ends_on" name="ends_on" type="date" class="admin-input" value="{{ old('ends_on') }}" required>
        </div>
    </div>
    <div class="admin-panel__body pt-0">
        <button type="submit" class="btn btn-primary">Block dates</button>
    </div>
</form>

<div class="admin-panel">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Upcoming blocked dates</h2></div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Practitioner</th>
                    <th>Range</th>
                    <th>Reason</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($blockedDates as $block)
                    <tr>
                        <td>{{ $block->practitioner?->user?->name }}</td>
                        <td>{{ $block->starts_on?->format('d M Y') }} – {{ $block->ends_on?->format('d M Y') }}</td>
                        <td>{{ $block->reason ?: '—' }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.schedules.blocked.destroy', $block) }}" method="POST" class="inline" onsubmit="return confirm('Remove this block?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No upcoming blocks</p>
                                <p class="admin-empty__copy">Blocked dates hide online booking slots for that practitioner.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
