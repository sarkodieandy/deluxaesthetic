@extends('student.layouts.app')
@section('title', __('student.nav.support'))
@section('heading', __('student.nav.support'))
@section('content')
<form method="POST" action="{{ route('student.support.store') }}" class="student-panel space-y-4 mb-6">
    @csrf
    <select name="category" class="field" required>
        <option value="course_question">Course question</option>
        <option value="payment_question">Payment question</option>
        <option value="profile_correction">Profile correction</option>
        <option value="other">Other</option>
    </select>
    <input name="subject" class="field" placeholder="Subject" required>
    <textarea name="message" class="field" rows="5" required></textarea>
    <button type="submit" class="student-action">Submit request</button>
</form>
<div class="student-panel">
    @forelse($requests as $item)
        <p class="mb-2 last:mb-0">{{ $item->reference }} · {{ $item->subject }} · {{ ucfirst($item->status) }}</p>
    @empty
        <p class="text-[var(--color-soft-grey)]">No support requests yet.</p>
    @endforelse
    @if(method_exists($requests, 'links'))
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
