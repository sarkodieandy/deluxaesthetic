@extends('admin.layouts.app')
@section('title', 'Manage account')
@section('heading', 'Manage account')
@section('breadcrumb', 'Access / Users / '.$managedUser->name)
@section('content')
@if (session('status'))
    <div class="admin-callout"><div><p class="admin-callout__title">{{ session('status') === 'user-created' ? 'Account created' : 'Account updated' }}</p><p class="admin-callout__copy">The staff member’s access details are ready.</p></div></div>
@endif
<form method="POST" action="{{ route('admin.users.update', $managedUser) }}">@csrf @method('PUT') @include('admin.users._form')</form>
@endsection
