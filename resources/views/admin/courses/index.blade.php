@extends('admin.layouts.app')
@section('title', 'Courses')
@section('heading', 'Courses')
@section('breadcrumb', 'Academy / Courses')
@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div>
            <h2 class="admin-panel__title">Courses</h2>
            <p class="mt-1 text-sm text-[var(--admin-text-muted)]">Featured courses become premium pathways on the public Academy page. Prices, order, images and curriculum update immediately after publishing.</p>
        </div>
        @can('courses.create')
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Add course</a>
        @endcan
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead><tr><th>Order</th><th>Name</th><th>Fee</th><th>Placement</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($courses as $course)
                <tr>
                    <td>{{ $course->sort_order }}</td>
                    <td><strong>{{ $course->name }}</strong><br><span class="text-sm text-[var(--admin-text-muted)]">{{ $course->venue ?: 'Venue confirmed by admissions' }}</span></td>
                    <td>{{ ($course->currency ?? 'GHS') === 'USD' ? '$' : 'GHS ' }}{{ number_format((float) $course->fee, 0) }}</td>
                    <td>{{ $course->is_featured ? 'Premium pathway' : 'Secondary catalogue' }}</td>
                    <td><span class="admin-status {{ $course->is_active ? 'admin-status--success' : 'admin-status--warning' }}">{{ $course->is_active ? 'Active' : 'Hidden' }}</span></td>
                    <td class="text-right whitespace-nowrap">
                        @can('courses.update')
                            <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-secondary">Edit</a>
                        @endcan
                        @can('courses.delete')
                            <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove this course?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-secondary">Delete</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="admin-empty"><p class="admin-empty__title">No courses yet</p><p class="admin-empty__copy">Create and publish academy courses here.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $courses->links() }}
@endsection
