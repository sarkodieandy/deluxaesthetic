@extends('admin.layouts.app')
@section('title','Review enquiry')
@section('heading',$courseEnquiry->full_name)
@section('breadcrumb', 'Academy / Course enquiries / Review')
@section('content')
@if(session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
@if($errors->any())<div class="mb-4 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
    @can('course_enquiries.manage')
    <form method="POST" action="{{ route('admin.course-enquiries.update', $courseEnquiry) }}" class="space-y-6">@csrf @method('PUT')
        <div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Applicant and training interest</h2></div><div class="admin-panel__body grid gap-4 md:grid-cols-2">
            <div><label class="admin-label" for="full_name">Full name</label><input id="full_name" name="full_name" class="admin-input" required value="{{ old('full_name', $courseEnquiry->full_name) }}"></div>
            <div><label class="admin-label" for="course_id">Course</label><select id="course_id" name="course_id" class="admin-input"><option value="">General enquiry</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected((string)old('course_id', $courseEnquiry->course_id) === (string)$course->id)>{{ $course->name }}</option>@endforeach</select></div>
            <div><label class="admin-label" for="email">Email</label><input id="email" name="email" type="email" class="admin-input" required value="{{ old('email', $courseEnquiry->email) }}"></div>
            <div><label class="admin-label" for="phone">Phone / WhatsApp</label><input id="phone" name="phone" class="admin-input" required value="{{ old('phone', $courseEnquiry->phone) }}"></div>
            <div><label class="admin-label" for="preferred_contact_method">Preferred contact</label><select id="preferred_contact_method" name="preferred_contact_method" class="admin-input">@foreach(['whatsapp'=>'WhatsApp','phone'=>'Phone call','email'=>'Email'] as $value=>$label)<option value="{{ $value }}" @selected(old('preferred_contact_method', $courseEnquiry->preferred_contact_method) === $value)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="admin-label" for="preferred_training_date">Preferred training date</label><input id="preferred_training_date" name="preferred_training_date" type="date" class="admin-input" value="{{ old('preferred_training_date', $courseEnquiry->preferred_training_date?->format('Y-m-d')) }}"></div>
            <div class="md:col-span-2"><label class="admin-label" for="professional_background">Professional background</label><textarea id="professional_background" name="professional_background" rows="3" class="admin-input">{{ old('professional_background', $courseEnquiry->professional_background) }}</textarea></div>
            <div class="md:col-span-2"><label class="admin-label" for="message">Applicant message</label><textarea id="message" name="message" rows="5" class="admin-input">{{ old('message', $courseEnquiry->message) }}</textarea></div>
        </div></div>
        <div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Admissions review</h2></div><div class="admin-panel__body grid gap-4 md:grid-cols-2">
            <div><label class="admin-label" for="status">Status</label><select id="status" name="status" class="admin-input">@foreach($statuses as $value=>$label)<option value="{{ $value }}" @selected(old('status', $courseEnquiry->status) === $value)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="admin-label" for="assigned_to">Assigned staff member</label><select id="assigned_to" name="assigned_to" class="admin-input"><option value="">Unassigned</option>@foreach($staff as $member)<option value="{{ $member->id }}" @selected((string)old('assigned_to', $courseEnquiry->assigned_to) === (string)$member->id)>{{ $member->name }}</option>@endforeach</select></div>
            <div class="md:col-span-2"><label class="admin-label" for="internal_notes">Private admissions notes</label><textarea id="internal_notes" name="internal_notes" rows="6" class="admin-input" placeholder="Record contact attempts, decisions and next steps. These notes are never shown publicly.">{{ old('internal_notes', $courseEnquiry->internal_notes) }}</textarea></div>
            <div class="md:col-span-2"><button class="btn btn-primary">Save review</button></div>
        </div></div>
    </form>
    @else
        <div class="admin-panel"><div class="admin-panel__body space-y-3"><p><strong>Course:</strong> {{ $courseEnquiry->course?->name ?? 'General academy enquiry' }}</p><p><strong>Email:</strong> {{ $courseEnquiry->email }}</p><p><strong>Phone:</strong> {{ $courseEnquiry->phone }}</p><p><strong>Message:</strong> {{ $courseEnquiry->message ?: '—' }}</p></div></div>
    @endcan

    <aside class="space-y-6">
        <div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Contact applicant</h2></div><div class="admin-panel__body space-y-3">
            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $courseEnquiry->phone) }}" target="_blank" rel="noopener" class="btn btn-primary w-full">Open WhatsApp</a>
            <a href="tel:{{ $courseEnquiry->phone }}" class="btn btn-secondary w-full">Call applicant</a>
            <a href="mailto:{{ $courseEnquiry->email }}" class="btn btn-secondary w-full">Send email</a>
        </div></div>
        <div class="admin-callout"><div><p class="admin-callout__title">Ready for registration?</p><p class="admin-callout__copy">Start the physical enrolment workflow after verifying the applicant.</p></div><a href="{{ route('admin.physical-enrolment.create', ['enquiry' => $courseEnquiry->id]) }}" class="btn btn-primary">Start physical enrolment</a></div>
        @can('course_enquiries.manage')
        <div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Delete enquiry</h2></div><div class="admin-panel__body"><p class="mb-4 text-sm text-[var(--admin-text-muted)]">Deletes this enquiry record only. It does not delete a registered student account.</p><form method="POST" action="{{ route('admin.course-enquiries.destroy', $courseEnquiry) }}" onsubmit="return confirm('Permanently remove this enquiry from the active admissions list?')">@csrf @method('DELETE')<button class="btn btn-secondary">Delete enquiry</button></form></div></div>
        @endcan
        <a href="{{ route('admin.course-enquiries.index') }}" class="btn btn-secondary w-full">Back to enquiries</a>
    </aside>
</div>
@endsection
