@extends('admin.layouts.app')
@section('title', 'Edit '.$category->name)
@section('heading', 'Edit '.$category->name)
@section('breadcrumb', 'Clinic / Clinical procedures / Categories / Edit')
@section('content')
<form method="POST" action="{{ route('admin.treatment-categories.update', $category) }}" enctype="multipart/form-data" class="admin-form">
    @csrf @method('PUT')
    @include('admin.treatment-categories._form', ['category' => $category])
    <div class="mt-6 flex flex-wrap gap-3"><button class="btn btn-primary" type="submit">Update category</button><a class="btn btn-secondary" href="{{ route('admin.treatment-categories.index') }}">Cancel</a></div>
</form>
@endsection
