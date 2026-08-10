@extends('admin.layouts.app')
@section('title', 'Edit academy showcase item')
@section('heading', 'Edit academy showcase item')
@section('breadcrumb', 'Academy / Showcase / Edit')
@section('content')<form method="POST" action="{{ route('admin.academy-showcase.update', $item) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.academy-showcase._form')<div class="mt-6 flex gap-3"><button class="btn btn-primary">Save changes</button><a href="{{ route('admin.academy-showcase.index') }}" class="btn btn-secondary">Cancel</a></div></form>@endsection
