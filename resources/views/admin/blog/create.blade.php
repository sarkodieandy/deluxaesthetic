@extends('admin.layouts.app')
@section('title', 'Create article')
@section('heading', 'Create article')
@section('breadcrumb', 'Content / Blog / Create')
@section('content')<form method="POST" action="{{ route('admin.blog.store') }}" enctype="multipart/form-data">@csrf @include('admin.blog._form')</form>@endsection
