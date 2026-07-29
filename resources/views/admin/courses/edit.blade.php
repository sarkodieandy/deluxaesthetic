@extends('admin.layouts.app')
@section('title', 'Edit course')
@section('heading', 'Edit '.$course->name)
@section('breadcrumb', 'Academy / Courses / Edit')
@section('content')
<form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data" class="admin-form">
    @csrf @method('PUT')
    @include('admin.courses._form', ['course' => $course, 'category' => $category])
    <div class="admin-actions mt-6">
        <button type="submit" class="btn btn-primary">Update course</button>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

@if(isset($schedules) && $schedules->isNotEmpty())
    <div class="admin-panel mt-8">
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
