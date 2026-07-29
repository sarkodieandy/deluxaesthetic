@extends('admin.layouts.app')
@section('title', 'Certificates')
@section('heading', 'Certificates')
@section('breadcrumb', 'Academy / Certificates')
@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif
<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Issued certificates</h2>
        @can('certificates.issue')
            <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">Create certificate</a>
        @endcan
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Completed</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $certificate)
                    <tr>
                        <td>{{ $certificate->number }}</td>
                        <td>{{ $certificate->student_name }}</td>
                        <td>{{ $certificate->course_name }}</td>
                        <td>{{ $certificate->completion_date?->format('d M Y') }}</td>
                        <td><span class="admin-status admin-status--info">{{ ucfirst($certificate->status) }}</span></td>
                        <td class="text-right">
                            @if($certificate->isDownloadable())
                                <a href="{{ route('admin.certificates.download', $certificate) }}" class="btn btn-secondary">Download PDF</a>
                            @endif
                            <a href="{{ route('admin.certificates.edit', $certificate) }}" class="btn btn-secondary">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No certificates yet</p>
                                <p class="admin-empty__copy">Create a certificate after a student completes their course.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $certificates->links() }}
@endsection
