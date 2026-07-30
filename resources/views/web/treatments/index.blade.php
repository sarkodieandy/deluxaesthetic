@extends('web.layouts.app')

@section('title', __('web.nav.treatments').' — '.config('clinic.name'))
@section('meta_description', 'Explore professional facial, skin, body, wellness and injectable aesthetic treatments at our clinic in East Legon, Accra.')

@section('content')
<section class="section">
    <div class="container-site">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="text-label mb-3">{{ $cmsPage?->hero_eyebrow ?: 'Clinic' }}</p>
                <h1 class="text-page-title">{{ $cmsPage?->hero_title ?: __('web.nav.treatments') }}</h1>
                @if($cmsPage?->hero_body)<p class="mt-4 max-w-2xl text-[var(--color-soft-grey)]">{{ $cmsPage->hero_body }}</p>@endif
            </div>
            <a href="{{ route('web.booking.create') }}" class="btn btn-primary">{{ __('web.book') }}</a>
        </div>

        <form method="GET" class="mb-10 grid gap-3 border border-[var(--color-border)] bg-white p-4 md:grid-cols-4">
            <input class="field md:col-span-2" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search treatments">
            <select class="field" name="category">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected($filters['category'] === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="field" name="sort">
                <option value="">Featured</option>
                <option value="name" @selected($filters['sort'] === 'name')>Name</option>
                <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Price ↑</option>
                <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Price ↓</option>
            </select>
            <button class="btn btn-secondary md:col-span-4 md:w-fit" type="submit">Apply filters</button>
        </form>

        <div class="grid gap-0 border border-[var(--color-border)] md:grid-cols-3">
            @forelse ($treatments as $treatment)
                <article class="border-[var(--color-border)] bg-white p-8 md:border-r md:border-b">
                    @if ($treatment->image_path)
                        <div class="mb-5 overflow-hidden border border-[var(--color-border)] bg-[var(--color-stone)]">
                            <img src="{{ $treatment->imageUrl() ?? asset('storage/'.$treatment->image_path) }}" alt="{{ $treatment->name }}" class="h-64 w-full object-cover" loading="lazy">
                        </div>
                    @endif
                    <p class="text-label mb-3">{{ $treatment->category?->name }}</p>
                    <h2 class="font-display text-2xl mb-3">
                        <a href="{{ route('web.treatments.show', $treatment->slug) }}">{{ $treatment->name }}</a>
                    </h2>
                    <p class="mb-4 text-[var(--color-soft-grey)]">{{ $treatment->short_description }}</p>
                    <p class="mb-2">GHS {{ number_format((float) $treatment->effectivePrice(), 2) }}</p>
                    <p class="mb-6 text-sm text-[var(--color-soft-grey)]">{{ $treatment->duration_minutes }} minutes</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('web.treatments.show', $treatment->slug) }}" class="btn btn-secondary">Details</a>
                        <a href="{{ route('web.booking.create', ['treatment_id' => $treatment->id]) }}" class="btn btn-primary">{{ __('web.book') }}</a>
                    </div>
                </article>
            @empty
                <div class="p-10 md:col-span-3">
                    <p class="text-[var(--color-soft-grey)]">No treatments match these filters.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $treatments->links() }}
        </div>
    </div>
</section>
@endsection
