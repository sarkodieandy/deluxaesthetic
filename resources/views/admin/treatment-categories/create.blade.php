@extends('admin.layouts.app')
@section('title', 'Add procedure category')
@section('heading', 'Add procedure category')
@section('breadcrumb', 'Clinic / Clinical procedures / Categories / Add')
@section('content')
<form method="POST" action="{{ route('admin.treatment-categories.store') }}" enctype="multipart/form-data" class="admin-form">
    @csrf
    @include('admin.treatment-categories._form')
    <div class="mt-6 flex flex-wrap gap-3"><button class="btn btn-primary" type="submit">Save category</button><a class="btn btn-secondary" href="{{ route('admin.treatment-categories.index') }}">Cancel</a></div>
</form>
@endsection
