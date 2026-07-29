@extends('admin.layouts.app')
@section('title','Enquiry')
@section('heading',$courseEnquiry->full_name)
@section('breadcrumb', 'Academy / Course enquiries')
@section('content')
<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Enquiry details</h2></div>
    <div class="admin-panel__body space-y-3">
        <p><strong>Course:</strong> {{ $courseEnquiry->course?->name ?? 'General academy enquiry' }}</p>
        <p><strong>Email:</strong> {{ $courseEnquiry->email }}</p>
        <p><strong>Phone:</strong> {{ $courseEnquiry->phone }}</p>
        @if($courseEnquiry->preferred_contact_method)<p><strong>Preferred contact:</strong> {{ ucfirst($courseEnquiry->preferred_contact_method) }}</p>@endif
        <p><strong>Message:</strong> {{ $courseEnquiry->message ?: '—' }}</p>
    </div>
</div>

<div class="admin-callout">
    <div>
        <p class="admin-callout__title">Ready for in-person registration?</p>
        <p class="admin-callout__copy">Convert this enquiry into a physical enrolment workflow. The student account and portal access are created by staff — not from the website.</p>
    </div>
    <a href="{{ route('admin.physical-enrolment.create', ['enquiry' => $courseEnquiry->id]) }}" class="btn btn-primary">Start physical enrolment</a>
</div>

<div class="admin-actions">
    <a href="{{ route('admin.course-enquiries.index') }}" class="btn btn-secondary">Back to enquiries</a>
</div>
@endsection
