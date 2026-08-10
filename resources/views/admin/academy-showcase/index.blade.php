@extends('admin.layouts.app')
@section('title', 'Academy showcase')
@section('heading', 'Academy showcase')
@section('breadcrumb', 'Academy / Showcase')
@section('content')
@if(session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div>
            <h2 class="admin-panel__title">Academy proof, format &amp; stories</h2>
            <p class="mt-1 text-sm text-[var(--admin-text-muted)]">Manage training steps, past students, skill reviews, countries, videos, certifications, experience and career support shown publicly.</p>
        </div>
        @can('courses.create')<a href="{{ route('admin.academy-showcase.create') }}" class="btn btn-primary">Add showcase item</a>@endcan
    </div>
    <div class="admin-panel__body">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64"><label class="admin-label" for="showcase_filter">Content type</label><select id="showcase_filter" name="type" class="admin-input"><option value="">All content</option>@foreach(\App\Models\AcademyShowcaseItem::TYPES as $value => $label)<option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>@endforeach</select></div>
            <button class="btn btn-secondary">Filter</button>
            @if($selectedType)<a href="{{ route('admin.academy-showcase.index') }}" class="btn btn-secondary">Clear</a>@endif
        </form>
    </div>
    <div class="admin-panel__body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th>Order</th><th>Type</th><th>Content</th><th>Media</th><th>Visibility</th><th></th></tr></thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->sort_order }}</td>
                    <td>{{ \App\Models\AcademyShowcaseItem::TYPES[$item->type] ?? $item->type }}</td>
                    <td><strong>{{ $item->title }}</strong><div class="text-sm text-[var(--admin-text-muted)]">{{ $item->subtitle }}</div></td>
                    <td>{{ $item->video_url ? 'Video' : ($item->image_path ? 'Image' : 'Text') }}</td>
                    <td><span class="admin-status {{ $item->is_active ? 'admin-status--success' : 'admin-status--warning' }}">{{ $item->is_active ? 'Published' : 'Hidden' }}</span></td>
                    <td class="text-right whitespace-nowrap">
                        @can('courses.update')
                            <a href="{{ route('admin.academy-showcase.edit', $item) }}" class="btn btn-secondary">Edit</a>
                        @endcan
                        @can('courses.delete')
                            <form action="{{ route('admin.academy-showcase.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Remove this showcase item?')">@csrf @method('DELETE')<button class="btn btn-secondary">Delete</button></form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="admin-empty"><p class="admin-empty__title">No Academy content in this view</p><p class="admin-empty__copy">Add verified training locations, stories, videos, certifications and Academy experience here.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $items->links() }}
@endsection
