@extends('admin.layouts.app')
@section('title', 'Add product')
@section('heading', 'Add product')
@section('breadcrumb', 'Store / Products / Create')
@section('content')
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="admin-form">@csrf @include('admin.products._form')<div class="flex flex-wrap gap-3 mt-6"><button type="submit" class="btn btn-primary">Save product</button><a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a></div></form>
@endsection
