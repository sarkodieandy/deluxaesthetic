@extends('admin.layouts.app')
@section('title','Course enquiries')
@section('heading','Course enquiries')
@section('breadcrumb','Academy / Course enquiries')
@section('content')
<div class="admin-panel"><div class="admin-panel__body" style="padding:0;"><table class="admin-table"><thead><tr><th>Name</th><th>Course</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($enquiries as $enquiry)
<tr><td>{{ $enquiry->full_name }}</td><td>{{ $enquiry->course?->name ?? 'General' }}</td><td>{{ ucfirst($enquiry->status) }}</td><td><a href="{{ route('admin.course-enquiries.show', $enquiry) }}" class="btn btn-secondary">Review</a></td></tr>
@endforeach
</tbody></table></div></div>{{ $enquiries->links() }}
@endsection
