@extends('admin.layouts.app')

@section('title', 'Add team member')
@section('heading', 'Add team member')
@section('breadcrumb', 'Clinic / Practitioners / Create')

@section('content')
<form method="POST" action="{{ route('admin.practitioners.store') }}" enctype="multipart/form-data" class="admin-form">
    @csrf
    @include('admin.practitioners._form')
    <div class="flex flex-wrap gap-3 mt-6">
        <button type="submit" class="btn btn-primary">Save member</button>
        <a href="{{ route('admin.practitioners.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
