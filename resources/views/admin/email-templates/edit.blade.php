@extends('admin.layouts.app')
@section('title','Edit template')
@section('heading','Edit email template')
@section('content')
<form method="POST" action="{{ route('admin.email-templates.update', $template) }}" class="admin-form">@csrf @method('PUT')
<label class="admin-label">Subject<input class="admin-input" name="subject" value="{{ old('subject', $template->subject) }}" required></label>
<label class="admin-label">Preheader<input class="admin-input" name="preheader" value="{{ old('preheader', $template->preheader) }}"></label>
<label class="admin-label">HTML body<textarea class="admin-input" name="body_html" rows="12" required>{{ old('body_html', $template->body_html) }}</textarea></label>
<label class="admin-label">Text body<textarea class="admin-input" name="body_text" rows="6">{{ old('body_text', $template->body_text) }}</textarea></label>
<label class="admin-check"><input type="checkbox" name="active" value="1" @checked(old('active', $template->active))> Active</label>
<button type="submit" class="btn btn-primary">Save</button>
</form>
@endsection
