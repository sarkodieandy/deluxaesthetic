@extends('admin.layouts.app')
@section('title', 'Gallery')
@section('heading', 'Gallery and before / after')
@section('breadcrumb', 'Content / Gallery')
@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Gallery items</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.gallery.create', ['type' => 'before_after']) }}" class="btn btn-secondary">Add before / after</a>
            <a href="{{ route('admin.gallery.create', ['type' => 'gallery']) }}" class="btn btn-primary">Add gallery photo</a>
        </div>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            @if($item->type === 'before_after')
                                <div class="flex gap-1">
                                    @if($item->beforeImageUrl())
                                        <img class="admin-avatar" src="{{ $item->beforeImageUrl() }}" alt="{{ $item->title }} before">
                                    @else
                                        <span class="admin-status admin-status--warning">Before missing</span>
                                    @endif
                                    @if($item->afterImageUrl())
                                        <img class="admin-avatar" src="{{ $item->afterImageUrl() }}" alt="{{ $item->title }} after">
                                    @else
                                        <span class="admin-status admin-status--warning">After missing</span>
                                    @endif
                                </div>
                            @elseif($item->imageUrl())
                                <img class="admin-avatar" src="{{ $item->imageUrl() }}" alt="{{ $item->title }}">
                            @else
                                <span class="admin-status admin-status--warning">File missing</span>
                            @endif
                        </td>
                        <td><strong>{{ $item->title }}</strong></td>
                        <td>{{ $item->type === 'before_after' ? 'Before / after' : 'Gallery image' }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="admin-status admin-status--success">Active</span>
                            @else
                                <span class="admin-status admin-status--warning">Hidden</span>
                            @endif
                            @if($item->is_featured)
                                <span class="admin-status admin-status--success ml-1">Featured</span>
                            @endif
                        </td>
                        <td>{{ $item->sort_order }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.gallery.edit', $item) }}" class="btn btn-secondary">Edit</a>
                            <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Remove this gallery item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No gallery items yet</p>
                                <p class="admin-empty__copy">Use <strong>Add before / after</strong> for the homepage slider, or <strong>Add gallery photo</strong> for the three-tile row.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $items->links() }}
@endsection
