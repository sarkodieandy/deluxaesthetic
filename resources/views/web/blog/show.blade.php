@extends('web.layouts.app')
@section('title', ($post->seo_title ?: $post->title).' — '.config('clinic.name'))
@section('meta_description', $post->seo_description ?: $post->excerpt)
@section('content')
@if($canPreview)<div class="cms-preview-bar">Draft preview — this article is not publicly visible</div>@endif
<article>
    <header class="page-intro">
        <div class="container-site max-w-4xl">
            <p class="text-label">{{ $post->category ?: 'Journal' }} · {{ $post->published_at?->format('d F Y') ?: 'Draft' }}</p>
            <h1 class="font-display mt-4">{{ $post->title }}</h1>
            <p class="mt-6 max-w-3xl text-lg">{{ $post->excerpt }}</p>
        </div>
    </header>
    <section class="catalogue-shell">
        <div class="container-site max-w-4xl">
            @if($post->coverImageUrl())<img src="{{ $post->coverImageUrl() }}" alt="{{ $post->cover_image_alt }}" class="mb-12 aspect-[16/9] w-full object-cover">@endif
            <div class="font-body text-lg leading-8 text-[var(--color-ink)] space-y-6">
                @foreach(preg_split('/\R{2,}/', trim($post->body)) as $paragraph)<p>{!! nl2br(e($paragraph)) !!}</p>@endforeach
            </div>
            <div class="mt-12 border-t border-[var(--color-border)] pt-8"><a href="{{ route('web.blog.index') }}" class="btn btn-secondary">← Back to journal</a></div>
        </div>
    </section>
</article>
@if($related->isNotEmpty())
<section class="catalogue-shell border-t border-[var(--color-border)]"><div class="container-site"><p class="text-label mb-6">Continue reading</p><div class="blog-teaser__grid">@foreach($related as $item)<article class="blog-card"><p class="text-label">{{ $item->category ?: 'Journal' }}</p><h2 class="font-display text-2xl mb-2"><a href="{{ route('web.blog.show', $item) }}">{{ $item->title }}</a></h2><p>{{ $item->excerpt }}</p></article>@endforeach</div></div></section>
@endif
@endsection
