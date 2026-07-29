@extends('admin.layouts.app')
@section('title', 'Add gallery item')
@section('heading', 'Add gallery item')
@section('breadcrumb', 'Content / Gallery / Create')
@section('content')
<form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="admin-form">
    @csrf
    @include('admin.gallery._form', ['defaultType' => $defaultType ?? null])<div class="flex flex-wrap gap-3 mt-6"><button type="submit" class="btn btn-primary">Save item</button><a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Cancel</a></div></form>
@endsection
