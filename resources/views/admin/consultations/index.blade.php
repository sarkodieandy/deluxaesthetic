@extends('admin.layouts.app')
@section('title', 'Consultation requests')
@section('heading', 'Consultation requests')
@section('breadcrumb', 'Clinic / Consultation Requests')
@section('content')
@if (session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Website and academy enquiries</h2>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Preferred date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultations as $consultation)
                    <tr>
                        <td>{{ $consultation->created_at?->timezone(config('clinic.timezone'))->format('d M Y') }}</td>
                        <td>
                            <strong>{{ $consultation->name }}</strong><br>
                            <span class="text-[var(--admin-text-muted)] text-sm">{{ $consultation->email }}</span>
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $consultation->preferred_channel)) }}</td>
                        <td><span class="admin-status admin-status--info">{{ ucfirst(str_replace('_', ' ', $consultation->status)) }}</span></td>
                        <td>{{ $consultation->preferred_date?->format('d M Y') ?? '—' }}</td>
                        <td class="text-right"><a href="{{ route('admin.consultations.edit', $consultation) }}" class="btn btn-secondary">Open</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No consultation requests yet</p>
                                <p class="admin-empty__copy">Academy enquiries and website consultation requests will appear here.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $consultations->links() }}
@endsection
