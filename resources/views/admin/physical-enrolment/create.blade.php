@extends('admin.layouts.app')
@section('title','Physical enrolment')
@section('heading','Record physical enrolment')
@section('breadcrumb','Academy / Physical enrolment')
@section('content')
<form method="POST" action="{{ route('admin.physical-enrolment.store') }}" class="admin-form">@csrf
<div class="admin-panel mb-6"><div class="admin-panel__body grid gap-4 md:grid-cols-2">
<div><label class="admin-label">Student</label><select name="student_profile_id" class="admin-input" required>@foreach($students as $student)<option value="{{ $student->id }}" @selected($selectedStudentId == $student->id)>{{ $student->user?->name }} ({{ $student->student_number }})</option>@endforeach</select></div>
<div><label class="admin-label">Course</label><select name="course_id" class="admin-input" required>@foreach($courses as $course)<option value="{{ $course->id }}">{{ $course->name }}</option>@endforeach</select></div>
<div><label class="admin-label">Schedule</label><select name="course_schedule_id" class="admin-input" required>@foreach($schedules as $schedule)<option value="{{ $schedule->id }}">#{{ $schedule->id }} · {{ $schedule->starts_on }}</option>@endforeach</select></div>
<div><label class="admin-label">Enquiry link</label><select name="course_enquiry_id" class="admin-input"><option value="">None</option>@foreach($enquiries as $enquiry)<option value="{{ $enquiry->id }}" @selected($selectedEnquiryId == $enquiry->id)>{{ $enquiry->full_name }}</option>@endforeach</select></div>
<div><label class="admin-label">Fee (GHS)</label><input type="number" step="0.01" name="fee" class="admin-input" required></div>
<div><label class="admin-label">Amount paid</label><input type="number" step="0.01" name="amount_paid" class="admin-input" value="0"></div>
<div><label class="admin-label">Enrolment date</label><input type="date" name="enrolment_date" class="admin-input" value="{{ now()->toDateString() }}" required></div>
<div><label class="admin-label">Physical verification date</label><input type="date" name="physical_verification_date" class="admin-input" value="{{ now()->toDateString() }}"></div>
<label class="admin-check md:col-span-2"><input type="checkbox" name="policies_accepted" value="1"> Policies accepted in person</label>
<label class="admin-check md:col-span-2"><input type="checkbox" name="activate_now" value="1" checked> Activate enrolment now</label>
<label class="admin-check md:col-span-2"><input type="checkbox" name="send_invitation" value="1" checked> Send portal invitation email</label>
</div></div>
<button type="submit" class="btn btn-primary">Save physical enrolment</button>
</form>
@endsection
