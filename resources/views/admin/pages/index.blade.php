@extends('admin.layouts.app')
@section('title', 'Website pages')
@section('heading', 'Website pages')
@section('breadcrumb', 'Content / Website pages')
@section('content')
<div class="admin-callout">
    <div><p class="admin-callout__title">Public website content</p><p class="admin-callout__copy">Manage page hero content, search appearance, publishing and flexible content sections without changing code.</p></div>
</div>
@if(session('status'))<div class="admin-callout"><div><p class="admin-callout__title">Page saved</p></div></div>@endif
<div class="admin-panel">
    <div class="admin-panel__head"><div><h2 class="admin-panel__title">Pages</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $pages->count() }} managed public pages</p></div></div>
    <div class="admin-panel__body" style="padding:0;overflow-x:auto">
        <table class="admin-table">
            <thead><tr><th>Page</th><th>Route</th><th>Status</th><th>Last updated</th><th></th></tr></thead>
            <tbody>
            @foreach($pages as $page)
                <tr>
                    <td><strong>{{ $page->name }}</strong><p class="mt-1 text-xs text-[var(--admin-text-muted)]">/{{ $page->slug === 'home' ? '' : $page->slug }}</p></td>
                    <td><code class="text-xs">{{ $page->route_name }}</code></td>
                    <td><span class="admin-status {{ $page->is_published ? 'admin-status--success' : 'admin-status--warning' }}">{{ $page->is_published ? 'Published' : 'Draft / hidden' }}</span></td>
                    <td><span>{{ $page->updated_at->diffForHumans() }}</span>@if($page->editor)<p class="mt-1 text-xs text-[var(--admin-text-muted)]">by {{ $page->editor->name }}</p>@endif</td>
                    <td class="text-right whitespace-nowrap"><a href="{{ route('admin.pages.preview', $page) }}" class="btn btn-secondary btn-sm" target="_blank">Preview</a> <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-primary btn-sm">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
