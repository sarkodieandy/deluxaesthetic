@extends('client.layouts.app')
@section('title', 'Consultations')
@section('heading', 'Consultations')
@section('content')
<form method="POST" action="{{ route('client.consultations.store') }}" class="portal-panel space-y-4 mb-6 max-w-2xl">
    @csrf
    <p class="font-display text-xl">Request a consultation</p>
    <textarea name="description" class="field" rows="4" placeholder="Tell us what you need help with" required>{{ old('description') }}</textarea>
    <input type="tel" name="phone" class="field" placeholder="Phone" value="{{ old('phone', auth()->user()->phone) }}">
    <input type="date" name="preferred_date" class="field" value="{{ old('preferred_date') }}" min="{{ now()->toDateString() }}">
    <select name="preferred_channel" class="field">
        <option value="phone">Phone</option>
        <option value="whatsapp">WhatsApp</option>
        <option value="email">Email</option>
        <option value="in_person">In person</option>
    </select>
    <button type="submit" class="portal-action">Submit request</button>
</form>

<div class="portal-panel">
    <p class="font-display text-xl mb-4">Your requests</p>
    @forelse($consultations as $item)
        <div class="border-b border-[var(--color-border)] py-3 last:border-0">
            <p class="font-medium">{{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
            <p class="text-sm text-[var(--color-soft-grey)]">{{ ucfirst(str_replace('_', ' ', $item->status ?? 'new')) }} · {{ $item->created_at?->diffForHumans() }}</p>
        </div>
    @empty
        <p class="text-[var(--color-soft-grey)]">No consultation requests yet.</p>
    @endforelse
</div>
@endsection
