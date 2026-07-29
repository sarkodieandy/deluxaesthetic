@extends('student.layouts.app')
@section('title', __('student.nav.profile'))
@section('heading', __('student.nav.profile'))
@section('content')
<form method="POST" action="{{ route('student.profile.update') }}" class="student-panel space-y-4 max-w-xl">
    @csrf
    @method('PUT')
    <input name="phone" class="field" value="{{ old('phone', $profile?->phone ?? $user->phone) }}" placeholder="Phone">
    <input name="address_line_1" class="field" value="{{ old('address_line_1', $profile?->address_line_1) }}" placeholder="Address">
    <input name="city" class="field" value="{{ old('city', $profile?->city) }}" placeholder="City">
    <input name="emergency_contact_name" class="field" value="{{ old('emergency_contact_name', $profile?->emergency_contact_name) }}" placeholder="Emergency contact name">
    <input name="emergency_contact_phone" class="field" value="{{ old('emergency_contact_phone', $profile?->emergency_contact_phone) }}" placeholder="Emergency contact phone">
    <button type="submit" class="student-action">Save profile</button>
</form>
@endsection
