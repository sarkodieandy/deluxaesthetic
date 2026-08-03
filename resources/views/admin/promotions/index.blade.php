@extends('admin.layouts.app')
@section('title','Promotions')
@section('heading','Promotions')
@section('breadcrumb','Marketing / Promotions')
@section('content')
<div class="admin-callout"><div><p class="admin-callout__title">Campaign banners</p><p class="admin-callout__copy">Schedule premium promotional banners by website placement, attach campaign artwork and connect offers to a clear call to action.</p></div><a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">Create promotion</a></div>
@if(session('status'))<div class="admin-callout"><p class="admin-callout__title">Promotion updated</p></div>@endif
<div class="admin-panel"><div class="admin-panel__body" style="padding:0;overflow-x:auto"><table class="admin-table"><thead><tr><th>Campaign</th><th>Placement</th><th>Schedule</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($promotions as $promotion)<tr><td><div class="flex items-center gap-3">@if($promotion->imageUrl())<img src="{{ $promotion->imageUrl() }}" alt="" class="h-14 w-24 object-cover">@endif<div><strong>{{ $promotion->title }}</strong>@if($promotion->coupon_code)<p class="text-xs text-[var(--admin-text-muted)]">Code: {{ $promotion->coupon_code }}</p>@endif</div></div></td><td>{{ ucfirst($promotion->placement) }}</td><td class="text-sm">{{ $promotion->starts_at?->format('d M Y H:i') ?? 'Immediately' }}<br><span class="text-[var(--admin-text-muted)]">to {{ $promotion->ends_at?->format('d M Y H:i') ?? 'No end date' }}</span></td><td><span class="admin-status {{ $promotion->is_active ? 'admin-status--success':'admin-status--warning' }}">{{ $promotion->is_active ? ($promotion->newQuery()->whereKey($promotion)->live()->exists() ? 'Live':'Scheduled') : 'Inactive' }}</span></td><td class="text-right"><a href="{{ route('admin.promotions.edit',$promotion) }}" class="btn btn-secondary btn-sm">Manage</a></td></tr>
@empty<tr><td colspan="5"><div class="admin-empty"><p class="admin-empty__title">No promotions yet</p><p class="admin-empty__copy">Create the first campaign banner for the public site.</p></div></td></tr>@endforelse
</tbody></table></div></div><div class="mt-6">{{ $promotions->links() }}</div>
@endsection
