@extends('client.layouts.app')
@section('title', 'Notifications')
@section('heading', 'Notifications')
@section('content')
@include('shared.notifications.list', [
    'panelClass' => 'portal-panel',
    'actionClass' => 'portal-action',
])
@endsection
