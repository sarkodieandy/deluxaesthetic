@extends('admin.layouts.app')

@section('title', 'Inbox')
@section('breadcrumb', 'Communication')
@section('heading', 'Notifications')

@section('content')
@include('shared.notifications.list', [
    'panelClass' => 'admin-panel',
    'actionClass' => 'btn btn-secondary btn-sm',
])
@endsection
