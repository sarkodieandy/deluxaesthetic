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
        <article class="mb-4 border-b border-[var(--color-border)] pb-4 last:mb-0 last:border-0 last:pb-0"><p class="font-medium">{{ $item->reference }} · {{ $item->subject }}</p><p class="text-sm">{{ ucfirst($item->status) }}</p><p class="mt-2">{{ $item->message }}</p>@if($item->admin_response)<div class="mt-3 border-l-2 border-[var(--color-gold)] pl-4"><p class="text-sm font-medium">Academy response</p><p>{{ $item->admin_response }}</p></div>@endif</article>
    @empty
        <p class="text-[var(--color-soft-grey)]">No support requests yet.</p>
    @endforelse
    @if(method_exists($requests, 'links'))
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
