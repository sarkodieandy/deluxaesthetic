@extends('admin.layouts.app')

@section('heading', $title)
@section('title', $title)
@section('breadcrumb', 'Admin / '.$title)

@section('content')
<div class="admin-panel">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">{{ $title }}</h2>
    </div>
    <div class="admin-panel__body">
        <div class="admin-empty">
            <p class="admin-empty__title">{{ $title }} module</p>
            <p class="admin-empty__copy">This module is wired into admin navigation and routes. Implementation follows the phased plan in <code>docs/admin/implementation-checklist.md</code>.</p>
            <p class="admin-empty__meta">Module key: <code>{{ $moduleKey ?? $title }}</code></p>
        </div>
    </div>
</div>
@endsection
