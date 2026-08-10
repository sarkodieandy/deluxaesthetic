@extends('admin.layouts.app')
@section('title', 'Add academy showcase item')
@section('heading', 'Add academy showcase item')
@section('breadcrumb', 'Academy / Showcase / Add')
@section('content')<form method="POST" action="{{ route('admin.academy-showcase.store') }}" enctype="multipart/form-data">@csrf @include('admin.academy-showcase._form')<div class="mt-6 flex gap-3"><button class="btn btn-primary">Publish item</button><a href="{{ route('admin.academy-showcase.index') }}" class="btn btn-secondary">Cancel</a></div></form>@endsection
