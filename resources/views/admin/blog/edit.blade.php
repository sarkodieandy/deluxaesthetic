@extends('admin.layouts.app')
@section('title', 'Edit article')
@section('heading', 'Edit article')
@section('breadcrumb', 'Content / Blog / Edit')
@section('content')<form method="POST" action="{{ route('admin.blog.update', $post) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.blog._form', ['post' => $post])</form>@endsection
