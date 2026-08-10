@extends('admin.layouts.app')
@section('title', 'Clinical procedures')
@section('heading', 'Clinical procedures')
@section('breadcrumb', 'Clinic / Clinical procedures')

@section('content')
@if (session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div><h2 class="admin-panel__title">Procedure catalogue</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Manage public prices, clinical details, images, order and booking availability.</p></div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.treatment-categories.index') }}" class="btn btn-secondary">Categories ({{ $categoryCount }})</a>
            @can('gallery.manage')<a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Before &amp; after</a>@endcan
            @can('treatments.create')<a href="{{ route('admin.treatments.create') }}" class="btn btn-primary">Add procedure</a>@endcan
        </div>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead><tr><th>Image</th><th>Procedure</th><th>Category</th><th>Price</th><th>Order</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($treatments as $treatment)
                <tr>
                    <td>@if($treatment->imageUrl())<img class="admin-avatar" src="{{ $treatment->imageUrl() }}" alt="">@else<span class="admin-status admin-status--warning">No image</span>@endif</td>
                    <td><strong>{{ $treatment->name }}</strong><p class="mt-1 max-w-sm text-sm text-[var(--admin-text-muted)]">{{ $treatment->short_description }}</p></td>
                    <td>{{ $treatment->category?->name ?? 'Uncategorised' }}</td>
                    <td>
                        @if ($treatment->promotional_price !== null)
                            <strong>GHS {{ number_format((float) $treatment->promotional_price, 2) }}</strong>
                            <small class="block text-[var(--admin-text-muted)] line-through">GHS {{ number_format((float) $treatment->price, 2) }}</small>
                        @else
                            GHS {{ number_format((float) $treatment->price, 2) }}
                        @endif
                    </td>
                    <td>{{ $treatment->sort_order }}</td>
                    <td>
                        <span class="admin-status {{ $treatment->is_active ? 'admin-status--success' : 'admin-status--warning' }}">{{ $treatment->is_active ? 'Published' : 'Hidden' }}</span>
                        @if($treatment->is_featured)<span class="admin-status admin-status--success ml-1">Featured</span>@endif
                    </td>
                    <td class="text-right whitespace-nowrap">
                        @can('treatments.update')<a href="{{ route('admin.treatments.edit', $treatment) }}" class="btn btn-secondary">Edit</a>@endcan
                        @can('treatments.delete')<form action="{{ route('admin.treatments.destroy', $treatment) }}" method="POST" class="inline" onsubmit="return confirm('Remove this clinical procedure?')">@csrf @method('DELETE')<button type="submit" class="btn btn-secondary">Delete</button></form>@endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="admin-empty"><p class="admin-empty__title">No clinical procedures yet</p><p class="admin-empty__copy">Add verified procedure names and prices here. Published procedures appear on the public clinical page automatically.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $treatments->links() }}
@endsection
