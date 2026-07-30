@extends('admin.layouts.app')
@section('title', 'Add staff account')
@section('heading', 'Add staff account')
@section('breadcrumb', 'Access / Users / New')
@section('content')
<form method="POST" action="{{ route('admin.users.store') }}">@csrf @include('admin.users._form')</form>
@endsection
