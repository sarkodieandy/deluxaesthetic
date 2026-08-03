@extends('admin.layouts.app')
@section('title', 'Blog')
@section('heading', 'Blog publishing')
@section('breadcrumb', 'Content / Blog')
@section('content')
@if(session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div><h2 class="admin-panel__title">Articles</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Create, preview and publish clinic and academy editorial content.</p></div>
        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">Create article</a>
    </div>
    <div class="admin-panel__body">
        <form method="GET" class="grid gap-3 md:grid-cols-[1fr_12rem_auto]">
            <input name="q" value="{{ request('q') }}" class="admin-input" placeholder="Search title or excerpt">
            <select name="status" class="admin-input"><option value="">All statuses</option><option value="draft" @selected(request('status') === 'draft')>Draft</option><option value="published" @selected(request('status') === 'published')>Published</option></select>
            <button class="btn btn-secondary" type="submit">Filter</button>
        </form>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead><tr><th>Article</th><th>Author</th><th>Status</th><th>Published</th><th></th></tr></thead>
            <tbody>
            @forelse($posts as $post)
                <tr>
                    <td><strong>{{ $post->title }}</strong><br><span class="text-sm text-[var(--admin-text-muted)]">{{ $post->category ?: 'Uncategorised' }}@if($post->is_featured) · Featured @endif</span></td>
                    <td>{{ $post->author?->name ?: 'Former staff' }}</td>
                    <td><span class="admin-status {{ $post->isPublic() ? 'admin-status--success' : 'admin-status--warning' }}">{{ $post->isPublic() ? 'Live' : ($post->status === 'published' ? 'Scheduled' : 'Draft') }}</span></td>
                    <td>{{ $post->published_at?->format('d M Y, H:i') ?: '—' }}</td>
                    <td class="text-right whitespace-nowrap">
                        <a href="{{ route('web.blog.show', ['post' => $post, 'preview' => 1]) }}" class="btn btn-secondary" target="_blank">Preview</a>
                        <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-secondary">Edit</a>
                        <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete this article?')">@csrf @method('DELETE')<button class="btn btn-secondary">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="admin-empty"><p class="admin-empty__title">No articles yet</p><p class="admin-empty__copy">Create the first article and publish it when it is ready.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $posts->links() }}
@endsection
