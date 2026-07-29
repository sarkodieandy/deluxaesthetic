@extends('admin.layouts.app')
@section('title','Email logs')
@section('heading','Email logs')
@section('content')
@if (session('status'))<p class="admin-notice">{{ session('status') }}</p>@endif
<table class="admin-table">
<thead><tr><th>To</th><th>Template</th><th>Status</th><th>Sent</th><th></th></tr></thead>
<tbody>
@foreach ($logs as $log)
<tr>
<td>{{ $log->recipient_email }}</td>
<td>{{ $log->template_key }}</td>
<td>{{ $log->status }}</td>
<td>{{ $log->sent_at?->toDateTimeString() ?? '—' }}</td>
<td>
@if (in_array($log->status, ['failed','queued'], true))
<form method="POST" action="{{ route('admin.email-logs.retry', $log) }}">@csrf<button type="submit">Retry</button></form>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
{{ $logs->links() }}
@endsection
