@extends('client.layouts.app')
@section('title', 'Profile')
@section('heading', 'Profile')
@section('content')
<form method="POST" action="{{ route('client.profile.update') }}" class="portal-panel space-y-4 max-w-xl">
    @csrf
    @method('PUT')
    <p class="text-sm text-[var(--color-soft-grey)]">{{ $user->name }} · {{ $user->email }}</p>
    <input name="phone" class="field" value="{{ old('phone', $user->phone) }}" placeholder="Phone">
    <input name="emergency_contact_name" class="field" value="{{ old('emergency_contact_name', $profile?->emergency_contact_name) }}" placeholder="Emergency contact name">
    <input name="emergency_contact_phone" class="field" value="{{ old('emergency_contact_phone', $profile?->emergency_contact_phone) }}" placeholder="Emergency contact phone">
    <textarea name="notes" class="field" rows="3" placeholder="Notes for the clinic (allergies, preferences)">{{ old('notes', $profile?->notes) }}</textarea>
    <button type="submit" class="portal-action">Save profile</button>
</form>
@endsection
