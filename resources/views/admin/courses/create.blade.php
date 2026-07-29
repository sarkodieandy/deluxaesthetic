@extends('admin.layouts.app')
@section('title', 'Add course')
@section('heading', 'Add course')
@section('breadcrumb', 'Academy / Courses / Create')
@section('content')
<form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="admin-form">@csrf @include('admin.courses._form')<div class="flex flex-wrap gap-3 mt-6"><button type="submit" class="btn btn-primary">Save course</button><a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a></div></form>
@endsection
