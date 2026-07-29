@extends('admin.layouts.app')
@section('title','Create student')
@section('heading','Create student account')
@section('content')
<form method="POST" action="{{ route('admin.physical-enrolment.students.store') }}" class="admin-form">@csrf
<div class="admin-panel"><div class="admin-panel__body grid gap-4 md:grid-cols-2">
<input class="admin-input" name="name" placeholder="Full name" required>
<input class="admin-input" name="email" type="email" placeholder="Email" required>
<input class="admin-input" name="phone" placeholder="Phone">
<input class="admin-input" name="student_number" placeholder="Student number (optional)">
</div></div>
<button type="submit" class="btn btn-primary">Create student</button>
</form>
@endsection
