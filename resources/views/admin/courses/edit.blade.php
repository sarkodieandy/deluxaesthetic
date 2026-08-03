@extends('admin.layouts.app')
@section('title', 'Edit course')
@section('heading', 'Edit '.$course->name)
@section('breadcrumb', 'Academy / Courses / Edit')
@section('content')
@if(session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
@if($errors->any())<div class="mb-4 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data" class="admin-form">
    @csrf @method('PUT')
    @include('admin.courses._form', ['course' => $course, 'category' => $category])
    <div class="admin-actions mt-6">
        <button type="submit" class="btn btn-primary">Update course</button>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<div class="admin-panel mt-8">
    <div class="admin-panel__head"><div><h2 class="admin-panel__title">Add training schedule</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">A schedule is required before this course can be assigned to a student.</p></div></div>
    <div class="admin-panel__body">
        <form method="POST" action="{{ route('admin.courses.schedules.store', $course->id) }}" class="grid gap-4 md:grid-cols-[1fr_1fr_10rem_auto]">@csrf
            <div><label class="admin-label">Starts</label><input class="admin-input" type="date" name="starts_on" min="{{ now()->toDateString() }}" required></div>
            <div><label class="admin-label">Ends</label><input class="admin-input" type="date" name="ends_on" min="{{ now()->toDateString() }}" required></div>
            <div><label class="admin-label">Capacity</label><input class="admin-input" type="number" name="capacity" min="1" value="{{ $course->max_students }}" required></div>
            <div class="self-end"><button class="btn btn-primary">Add schedule</button></div>
        </form>
    </div>
</div>

@if(isset($schedules) && $schedules->isNotEmpty())
    <div class="admin-panel mt-6">
        <div class="admin-panel__head">
            <h2 class="admin-panel__title">Training schedules</h2>
        </div>
        <div class="admin-panel__body" style="padding:0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Start</th>
                        <th>End</th>
                        <th>Enrolled</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($schedule->starts_on)->format('d M Y') }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($schedule->ends_on)->format('d M Y') }}</td>
                            <td>{{ $schedule->enrolled_count }} / {{ $schedule->capacity }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.course-schedules.sessions.index', $schedule->id) }}" class="btn btn-secondary btn-sm">Manage sessions</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
