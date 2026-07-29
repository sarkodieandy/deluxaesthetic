@extends('admin.layouts.app')
@section('title', 'Edit gallery item')
@section('heading', 'Edit '.$item->title)
@section('breadcrumb', 'Content / Gallery / Edit')
@section('content')
<form method="POST" action="{{ route('admin.gallery.update', $item) }}" enctype="multipart/form-data" class="admin-form">@csrf @method('PUT') @include('admin.gallery._form', ['item' => $item])<div class="flex flex-wrap gap-3 mt-6"><button type="submit" class="btn btn-primary">Update item</button><a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Cancel</a></div></form>
@endsection
