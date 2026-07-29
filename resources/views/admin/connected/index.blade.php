@extends('admin.layouts.app')
@section('title', $title)
@section('heading', $title)
@section('breadcrumb', $breadcrumb)
@section('content')
<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">{{ $title }}</h2>
    </div>
    @if(! empty($filters))
        <div class="admin-panel__filters mb-4 flex flex-wrap items-center gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                @if(isset($filters['search']))
                    <input type="search" name="search" value="{{ $filters['search'] }}" class="admin-input" placeholder="Search...">
                @endif
                @if(isset($filters['roleOptions']))
                    <select name="role" class="admin-input">
                        <option value="">All roles</option>
                        @foreach($filters['roleOptions'] as $roleOption)
                            <option value="{{ $roleOption }}" @selected($filters['role'] === $roleOption)>{{ $roleOption }}</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
            @if(request()->query())
                <a href="{{ url()->current() }}" class="btn btn-secondary">Clear</a>
            @endif
        </div>
    @endif
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        @foreach($row as $value)
                            <td>{!! $value !!}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No records yet</p>
                                <p class="admin-empty__copy">This page is now connected to the database and will populate as records are created.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $rows->links() }}
@endsection
