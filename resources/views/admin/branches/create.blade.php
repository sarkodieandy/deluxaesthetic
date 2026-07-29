@extends('admin.layouts.app')
@section('title', 'Add branch')
@section('heading', 'Add branch')
@section('breadcrumb', 'Clinic / Branches / Create')
@section('content')
<form method="POST" action="{{ route('admin.branches.store') }}" class="admin-form">
    @csrf
    @include('admin.branches._form')
    <div class="flex flex-wrap gap-3 mt-6">
        <button type="submit" class="btn btn-primary">Save branch</button>
        <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
