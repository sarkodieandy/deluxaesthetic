@extends('admin.layouts.app')
@section('title', 'Add treatment')
@section('heading', 'Add treatment')
@section('breadcrumb', 'Clinic / Treatments / Create')
@section('content')
<form method="POST" action="{{ route('admin.treatments.store') }}" enctype="multipart/form-data" class="admin-form">@csrf @include('admin.treatments._form')<div class="flex flex-wrap gap-3 mt-6"><button type="submit" class="btn btn-primary">Save treatment</button><a href="{{ route('admin.treatments.index') }}" class="btn btn-secondary">Cancel</a></div></form>
@endsection
