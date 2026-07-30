@extends('web.layouts.app')
@section('title', __('web.pages.gallery_title').' — '.config('clinic.name'))
@section('meta_description', 'View clinic treatments, professional aesthetics work and real before-and-after results from De Luxe Aesthetic Clinic in Accra.')
@section('content')
@include('web.components.page-intro', [
    'eyebrow' => $cmsPage?->hero_eyebrow ?: __('web.pages.gallery_eyebrow'),
    'title' => $cmsPage?->hero_title ?: __('web.pages.gallery_title'),
    'lead' => $cmsPage?->hero_body ?: __('web.pages.gallery_lead'),
])
<section class="catalogue-shell">
    <div class="container-site space-y-12">
        <div>
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <p class="text-label mb-3">Studio</p>
                    <h2 class="text-section">Gallery</h2>
                </div>
            </div>
            @if ($galleryItems->isEmpty())
                <div class="catalogue-empty">
                    <p class="text-[var(--color-soft-grey)]">Gallery uploads from admin will appear here.</p>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($galleryItems as $item)
                        <article class="border border-[var(--color-border)] bg-white">
                            @if ($item->imageUrl())
                                <img src="{{ $item->imageUrl() }}" alt="{{ $item->alt_text ?: $item->title }}" class="h-80 w-full object-cover" loading="lazy">
                            @endif
                            <div class="p-5">
                                <h3 class="font-display text-2xl mb-2">{{ $item->title }}</h3>
                                @if($item->description)<p class="text-[var(--color-soft-grey)]">{{ $item->description }}</p>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="mb-6">
                <p class="text-label mb-3">Clinical results</p>
                <h2 class="text-section">{{ __('web.home.ba_title') }}</h2>
            </div>
            @if ($beforeAfterItems->isEmpty())
                <div class="catalogue-empty">
                    <p class="text-[var(--color-soft-grey)]">Before-and-after cases uploaded in admin will appear here.</p>
                </div>
            @else
                <div class="space-y-10">
                    @foreach ($beforeAfterItems as $item)
                        @if ($item->hasBeforeAfterPair())
                            <article>
                                <div class="mb-4">
                                    <h3 class="font-display text-2xl mb-2">{{ $item->title }}</h3>
                                    @if($item->description)<p class="text-[var(--color-soft-grey)]">{{ $item->description }}</p>@endif
                                </div>
                                @include('web.components.before-after-compare', [
                                    'beforeUrl' => $item->beforeImageUrl(),
                                    'afterUrl' => $item->afterImageUrl(),
                                    'beforeAlt' => $item->alt_text ?: $item->title.' — '.__('web.home.ba_before'),
                                    'afterAlt' => $item->alt_text ?: $item->title.' — '.__('web.home.ba_after'),
                                    'title' => $item->title,
                                ])
                            </article>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
