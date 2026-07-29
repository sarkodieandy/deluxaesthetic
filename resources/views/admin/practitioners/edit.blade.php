@extends('admin.layouts.app')

@section('title', 'Edit team member')
@section('heading', 'Edit '.$practitioner->user?->name)
@section('breadcrumb', 'Clinic / Practitioners / Edit')

@section('content')
<form method="POST" action="{{ route('admin.practitioners.update', $practitioner) }}" enctype="multipart/form-data" class="admin-form">
    @csrf
    @method('PUT')
    @include('admin.practitioners._form', ['practitioner' => $practitioner])
    <div class="flex flex-wrap gap-3 mt-6">
        <button type="submit" class="btn btn-primary">Update member</button>
        <a href="{{ route('admin.practitioners.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
