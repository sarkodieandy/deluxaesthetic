@extends('student.layouts.app')
@section('title', __('student.nav.notifications'))
@section('heading', __('student.nav.notifications'))
@section('content')
@include('shared.notifications.list', [
    'panelClass' => 'student-panel',
    'actionClass' => 'student-action',
])
@endsection
