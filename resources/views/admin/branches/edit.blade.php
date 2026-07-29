@extends('admin.layouts.app')
@section('title', 'Edit branch')
@section('heading', 'Edit branch')
@section('breadcrumb', 'Clinic / Branches / Edit')
@section('content')
<form method="POST" action="{{ route('admin.branches.update', $branch) }}" class="admin-form">
    @csrf
    @method('PUT')
    @include('admin.branches._form', ['branch' => $branch])
    <div class="flex flex-wrap gap-3 mt-6">
        <button type="submit" class="btn btn-primary">Update branch</button>
        <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
