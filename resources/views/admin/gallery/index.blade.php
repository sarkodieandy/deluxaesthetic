@extends('admin.layouts.app')
@section('title', 'Gallery')
@section('heading', 'Gallery and before / after')
@section('breadcrumb', 'Content / Gallery')
@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif

<section class="admin-gallery-collections" aria-labelledby="gallery-collections-heading">
    <div class="admin-gallery-collections__heading">
        <div>
            <p class="admin-gallery-collections__eyebrow">Destination library</p>
            <h2 id="gallery-collections-heading">Country collections</h2>
        </div>
        <p>Organise every photograph by where the training, event or clinical experience happened. The website uses these same collections automatically.</p>
    </div>
    <div class="admin-gallery-collections__grid">
        @foreach($collectionStats as $collection)
            <article class="admin-gallery-collection {{ $collectionFilter === $collection['key'] ? 'is-active' : '' }}">
                <div class="admin-gallery-collection__top">
                    <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <strong>{{ $collection['total'] }}</strong>
                </div>
                <h3>{{ $collection['label'] }}</h3>
                <p>{{ $collection['description'] }}</p>
                <dl>
                    <div><dt>Photos</dt><dd>{{ $collection['photos'] }}</dd></div>
                    <div><dt>Results</dt><dd>{{ $collection['results'] }}</dd></div>
                    <div><dt>Live</dt><dd>{{ $collection['active'] }}</dd></div>
                </dl>
                <div class="admin-gallery-collection__actions">
                    <a href="{{ route('admin.gallery.index', ['collection' => $collection['key']]) }}">View collection</a>
                    <a href="{{ route('admin.gallery.create', ['type' => 'gallery', 'collection' => $collection['key']]) }}">Add photo <span aria-hidden="true">＋</span></a>
                </div>
            </article>
        @endforeach
    </div>
</section>

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Gallery items</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.gallery.create', ['type' => 'before_after']) }}" class="btn btn-secondary">Add before / after</a>
            <a href="{{ route('admin.gallery.create', ['type' => 'gallery']) }}" class="btn btn-primary">Add gallery photo</a>
        </div>
    </div>
    <div class="admin-panel__body border-b border-[var(--admin-border)]">
        <form method="GET" action="{{ route('admin.gallery.index') }}" class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end">
            <div>
                <label class="admin-label" for="collection-filter">Collection</label>
                <select id="collection-filter" name="collection" class="admin-input">
                    <option value="">All collections</option>
                    @foreach($locationGroups as $key => $label)
                        <option value="{{ $key }}" @selected($collectionFilter === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="type-filter">Content type</label>
                <select id="type-filter" name="type" class="admin-input">
                    <option value="">All content types</option>
                    <option value="gallery" @selected($typeFilter === 'gallery')>Gallery photos</option>
                    <option value="before_after" @selected($typeFilter === 'before_after')>Before / after</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                @if($collectionFilter || $typeFilter)
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Collection</th>
                    <th>Procedure</th>
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
                        <td><span class="admin-status">{{ $item->locationGroupLabel() }}</span></td>
                        <td>{{ $item->treatment?->name ?? 'General clinic work' }}</td>
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
                        <td colspan="8">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No gallery items yet</p>
                                <p class="admin-empty__copy">Add a photo to one of the destination collections above, or create a before-and-after treatment result.</p>
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
