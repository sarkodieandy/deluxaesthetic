@extends('admin.layouts.app')
@section('title','Upload course material')
@section('heading','Upload course material')
@section('breadcrumb','Academy / Course materials / Upload')
@section('content')
<form method="POST" action="{{ route('admin.course-materials.store') }}" enctype="multipart/form-data" class="admin-form">@csrf @include('admin.course-materials._form')<div class="mt-6 flex gap-3"><button class="btn btn-primary">Upload material</button><a href="{{ route('admin.course-materials.index') }}" class="btn btn-secondary">Cancel</a></div></form>
@endsection
