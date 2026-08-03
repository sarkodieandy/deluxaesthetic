@extends('admin.layouts.app')
@section('title','Edit course material')
@section('heading','Edit course material')
@section('breadcrumb','Academy / Course materials / Edit')
@section('content')
@if(session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
<form method="POST" action="{{ route('admin.course-materials.update', $material) }}" enctype="multipart/form-data" class="admin-form">@csrf @method('PUT') @include('admin.course-materials._form')<div class="mt-6 flex gap-3"><button class="btn btn-primary">Save changes</button><a href="{{ route('admin.course-materials.index') }}" class="btn btn-secondary">Back</a></div></form>
@endsection
