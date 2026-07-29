@extends('admin.layouts.app')

@section('heading', 'Audit log')
@section('title', 'Audit log')
@section('breadcrumb', 'System / Audit')

@section('content')
<div class="admin-panel">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Audit log</h2>
    </div>
    <div class="admin-panel__body admin-table-wrap">
        @if ($logs->isEmpty())
            <div class="admin-empty">
                <p class="admin-empty__title">No audit entries yet</p>
                <p class="admin-empty__copy">Critical admin actions will appear here as modules write to the audit log.</p>
            </div>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->timezone(config('clinic.timezone'))->format('d M Y H:i') }}</td>
                            <td>{{ $log->user?->name ?? '—' }}</td>
                            <td><code>{{ $log->action }}</code></td>
                            <td>{{ $log->description ?? data_get($log->new_values, 'message') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="admin-pagination">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
