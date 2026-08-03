@extends('admin.layouts.app')
@section('title','Create assignment') @section('heading','Create assignment') @section('breadcrumb','Academy / Assignments / Create')
@section('content')<form method="POST" action="{{ route('admin.assignments.store') }}" enctype="multipart/form-data">@csrf @include('admin.assignments._form')<div class="mt-6 flex gap-3"><button class="btn btn-primary">Publish assignment</button><a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary">Cancel</a></div></form>@endsection
