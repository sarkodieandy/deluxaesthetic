@extends('admin.layouts.app')
@section('title', 'Procedure categories')
@section('heading', 'Procedure categories')
@section('breadcrumb', 'Clinic / Clinical procedures / Categories')

@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif
@if ($errors->any())
    <div class="mb-4 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">
        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div>
            <h2 class="admin-panel__title">Clinical catalogue structure</h2>
            <p class="mt-1 text-sm text-[var(--admin-text-muted)]">Arrange the public journey from spa therapy through injectable procedures.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.treatments.index') }}" class="btn btn-secondary">View procedures</a>
            @can('treatments.create')
                <a href="{{ route('admin.treatment-categories.create') }}" class="btn btn-primary">Add category</a>
            @endcan
        </div>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead><tr><th>Image</th><th>Category</th><th>Procedures</th><th>Order</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>
                        @if ($category->imageUrl())
                            <img class="admin-avatar" src="{{ $category->imageUrl() }}" alt="">
                        @else
                            <span class="admin-status admin-status--warning">No image</span>
                        @endif
                    </td>
                    <td><strong>{{ $category->name }}</strong><p class="mt-1 max-w-md text-sm text-[var(--admin-text-muted)]">{{ $category->description }}</p></td>
                    <td>{{ $category->treatments_count }}</td>
                    <td>{{ $category->sort_order }}</td>
                    <td>
                        <span class="admin-status {{ $category->is_active ? 'admin-status--success' : 'admin-status--warning' }}">
                            {{ $category->is_active ? 'Published' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="text-right whitespace-nowrap">
                        @can('treatments.update')
                            <a href="{{ route('admin.treatment-categories.edit', $category) }}" class="btn btn-secondary">Edit</a>
                        @endcan
                        @can('treatments.delete')
                            <form action="{{ route('admin.treatment-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Remove this procedure category?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-secondary">Delete</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="admin-empty"><p class="admin-empty__title">No procedure categories yet</p><p class="admin-empty__copy">Create the public clinic pathways before adding procedures.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $categories->links() }}
@endsection
