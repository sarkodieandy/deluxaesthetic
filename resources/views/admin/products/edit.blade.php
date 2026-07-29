@extends('admin.layouts.app')
@section('title', 'Edit product')
@section('heading', 'Edit '.$product->name)
@section('breadcrumb', 'Store / Products / Edit')
@section('content')
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="admin-form">@csrf @method('PUT') @include('admin.products._form', ['product' => $product])<div class="flex flex-wrap gap-3 mt-6"><button type="submit" class="btn btn-primary">Update product</button><a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a></div></form>
@endsection
