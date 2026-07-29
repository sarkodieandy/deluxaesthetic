@extends('admin.layouts.app')
@section('title','Email templates')
@section('heading','Email templates')
@section('content')
@if (session('status'))<p class="admin-notice">{{ session('status') }}</p>@endif
<table class="admin-table">
<thead><tr><th>Key</th><th>Locale</th><th>Subject</th><th>Active</th><th></th></tr></thead>
<tbody>
@foreach ($templates as $template)
<tr>
<td>{{ $template->key }}</td>
<td>{{ $template->locale }}</td>
<td>{{ $template->subject }}</td>
<td>{{ $template->active ? 'Yes' : 'No' }}</td>
<td><a href="{{ route('admin.email-templates.edit', $template) }}">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
{{ $templates->links() }}
@endsection
