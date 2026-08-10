@extends('admin.layouts.app')
@section('title', 'Physical enrolment')
@section('heading', 'Record physical enrolment')
@section('breadcrumb', 'Academy / Physical enrolment')
@section('content')
@if($errors->any())<div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if(session('status'))<p class="mb-6 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif

<form method="POST" action="{{ route('admin.physical-enrolment.store') }}" class="admin-form">@csrf
    <div class="admin-panel mb-6">
        <div class="admin-panel__head"><div><h2 class="admin-panel__title">Student and confirmed course</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">When this page is opened from an enquiry, its registered student and requested course are selected automatically. Confirm both before saving.</p></div></div>
        <div class="admin-panel__body grid gap-4 md:grid-cols-2">
            <div><label class="admin-label" for="student_profile_id">Student</label><select id="student_profile_id" name="student_profile_id" class="admin-input" required data-student-select>@foreach($students as $student)<option value="{{ $student->id }}" @selected((string) old('student_profile_id', $selectedStudentId) === (string) $student->id)>{{ $student->user?->name }} ({{ $student->student_number }})</option>@endforeach</select></div>
            <div><label class="admin-label" for="course_id">Course</label><select id="course_id" name="course_id" class="admin-input" required data-course-select>@foreach($courses as $course)<option value="{{ $course->id }}" data-fee="{{ $course->fee }}" data-currency="{{ $course->currency ?? 'GHS' }}" @selected((string) old('course_id', $selectedCourseId) === (string) $course->id)>{{ $course->name }} · {{ $course->formattedFee() }}</option>@endforeach</select></div>
            <div><label class="admin-label" for="course_schedule_id">Class schedule</label><select id="course_schedule_id" name="course_schedule_id" class="admin-input" required data-schedule-select>@foreach($schedules as $schedule)<option value="{{ $schedule->id }}" data-course-id="{{ $schedule->course_id }}" @selected((string) old('course_schedule_id', $selectedScheduleId) === (string) $schedule->id)>{{ $schedule->course?->name }} · {{ $schedule->starts_on->format('d M Y') }}–{{ $schedule->ends_on->format('d M Y') }}</option>@endforeach</select><p class="mt-1 text-xs text-[var(--admin-text-muted)]" data-schedule-message></p></div>
            <div><label class="admin-label" for="course_enquiry_id">Enquiry link</label><select id="course_enquiry_id" name="course_enquiry_id" class="admin-input" data-enquiry-select><option value="">None</option>@foreach($enquiries as $enquiry)<option value="{{ $enquiry->id }}" data-student-id="{{ $enquiry->matched_student_profile_id }}" data-course-id="{{ $enquiry->course_id }}" @selected((string) old('course_enquiry_id', $selectedEnquiryId) === (string) $enquiry->id)>{{ $enquiry->full_name }}@if($enquiry->course?->name) · {{ $enquiry->course->name }}@endif</option>@endforeach</select><p class="mt-1 text-xs text-[var(--admin-text-muted)]">Only enquiries matching the selected student and course are available.</p></div>
            <div><label class="admin-label" for="currency">Currency</label><select id="currency" name="currency" class="admin-input" required data-course-currency><option value="USD" @selected(old('currency') === 'USD')>USD ($)</option><option value="GHS" @selected(old('currency') === 'GHS')>GHS</option></select></div>
            <div><label class="admin-label" for="fee">Course fee</label><input id="fee" type="number" step="0.01" min="0" name="fee" class="admin-input" value="{{ old('fee') }}" required data-course-fee></div>
            <div><label class="admin-label" for="amount_paid">Amount paid</label><input id="amount_paid" type="number" step="0.01" min="0" name="amount_paid" class="admin-input" value="{{ old('amount_paid', 0) }}"></div>
            <div><label class="admin-label" for="enrolment_date">Enrolment date</label><input id="enrolment_date" type="date" name="enrolment_date" class="admin-input" value="{{ old('enrolment_date', now()->toDateString()) }}" required></div>
            <div><label class="admin-label" for="physical_verification_date">Physical verification date</label><input id="physical_verification_date" type="date" name="physical_verification_date" class="admin-input" value="{{ old('physical_verification_date', now()->toDateString()) }}"></div>
            <label class="admin-check md:col-span-2"><input type="checkbox" name="policies_accepted" value="1" @checked(old('policies_accepted'))> Policies accepted in person</label>
            @if($canActivate)
                <label class="admin-check md:col-span-2"><input type="checkbox" name="activate_now" value="1" @checked(old('activate_now', true))> Activate enrolment now</label>
                <label class="admin-check md:col-span-2"><input type="checkbox" name="send_invitation" value="1" @checked(old('send_invitation', true))> Send portal invitation email</label>
            @else
                <p class="md:col-span-2 text-sm text-[var(--admin-text-muted)]">You can record the enrolment. A staff member with activation permission must approve portal access afterward.</p>
            @endif
        </div>
    </div>
    <button type="submit" class="btn btn-primary" data-enrolment-submit>Save physical enrolment</button>
</form>

<script>
(() => {
    const course = document.querySelector('[data-course-select]');
    const student = document.querySelector('[data-student-select]');
    const fee = document.querySelector('[data-course-fee]');
    const currency = document.querySelector('[data-course-currency]');
    const schedule = document.querySelector('[data-schedule-select]');
    const message = document.querySelector('[data-schedule-message]');
    const submit = document.querySelector('[data-enrolment-submit]');
    const enquiry = document.querySelector('[data-enquiry-select]');
    if (!course || !fee || !currency || !schedule) return;

    const sync = (courseChanged = false) => {
        const selected = course.options[course.selectedIndex];
        const preserveEnteredPricing = !courseChanged && fee.value !== '';
        if (!preserveEnteredPricing) {
            fee.value = selected?.dataset.fee ?? '';
            currency.value = selected?.dataset.currency ?? 'GHS';
        }

        const matching = Array.from(schedule.options).filter(option => option.dataset.courseId === course.value);
        Array.from(schedule.options).forEach(option => {
            const visible = option.dataset.courseId === course.value;
            option.hidden = !visible;
            option.disabled = !visible;
        });

        if (!matching.some(option => option.selected)) {
            matching[0]?.setAttribute('selected', 'selected');
            if (matching[0]) schedule.value = matching[0].value;
        }

        schedule.disabled = matching.length === 0;
        if (submit) submit.disabled = matching.length === 0;
        if (message) message.textContent = matching.length === 0 ? 'Add an active schedule to this course before enrolling the student.' : matching.length+' available schedule'+(matching.length === 1 ? '' : 's')+'.';

        if (enquiry && student) {
            Array.from(enquiry.options).forEach(option => {
                if (!option.value) return;
                const visible = option.dataset.studentId === student.value
                    && (!option.dataset.courseId || option.dataset.courseId === course.value);
                option.hidden = !visible;
                option.disabled = !visible;
            });
            if (enquiry.selectedOptions[0]?.disabled) enquiry.value = '';
        }
    };

    course.addEventListener('change', () => sync(true));
    student?.addEventListener('change', () => sync(false));
    sync(false);
})();
</script>
@endsection
