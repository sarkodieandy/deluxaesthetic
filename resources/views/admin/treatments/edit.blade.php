@extends('admin.layouts.app')
@section('title', 'Edit treatment')
@section('heading', 'Edit '.$treatment->name)
@section('breadcrumb', 'Clinic / Treatments / Edit')
@section('content')
<form method="POST" action="{{ route('admin.treatments.update', $treatment) }}" enctype="multipart/form-data" class="admin-form">@csrf @method('PUT') @include('admin.treatments._form', ['treatment' => $treatment])<div class="flex flex-wrap gap-3 mt-6"><button type="submit" class="btn btn-primary">Update treatment</button><a href="{{ route('admin.treatments.index') }}" class="btn btn-secondary">Cancel</a></div></form>
@endsection
