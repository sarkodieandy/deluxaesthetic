@extends('web.layouts.app')
@section('title', __('web.pages.blog_title').' — '.config('clinic.name'))
@section('meta_description', 'Read practical guidance on skin health, aesthetic treatments, beauty aftercare and professional training from our Accra clinic and academy.')
@section('content')
@include('web.components.page-intro', [
    'eyebrow' => $cmsPage?->hero_eyebrow ?: __('web.pages.blog_eyebrow'),
    'title' => $cmsPage?->hero_title ?: __('web.pages.blog_title'),
    'lead' => $cmsPage?->hero_body ?: __('web.pages.blog_lead'),
])
<section class="catalogue-shell">
    <div class="container-site">
        @if($categories->isNotEmpty())
            <nav class="flex flex-wrap gap-2 mb-10" aria-label="Blog categories">
                <a href="{{ route('web.blog.index') }}" class="btn {{ request('category') ? 'btn-secondary' : 'btn-primary' }}">All</a>
                @foreach($categories as $category)<a href="{{ route('web.blog.index', ['category' => $category]) }}" class="btn {{ request('category') === $category ? 'btn-primary' : 'btn-secondary' }}">{{ $category }}</a>@endforeach
            </nav>
        @endif

        <div class="blog-teaser__grid">
            @forelse($posts as $post)
                <article class="blog-card">
                    @if($post->coverImageUrl())<a href="{{ route('web.blog.show', $post) }}"><img src="{{ $post->coverImageUrl() }}" alt="{{ $post->cover_image_alt }}" class="mb-5 aspect-[4/3] w-full object-cover" loading="lazy"></a>@endif
                    <p class="text-label">{{ $post->category ?: 'Journal' }} · {{ $post->published_at->format('d M Y') }}</p>
                    <h2 class="font-display text-2xl mb-2"><a href="{{ route('web.blog.show', $post) }}">{{ $post->title }}</a></h2>
                    <p>{{ $post->excerpt }}</p>
                    <a href="{{ route('web.blog.show', $post) }}" class="inline-block mt-5 text-label">Read article →</a>
                </article>
            @empty
                <div class="blog-card"><p class="text-label">Journal</p><h2 class="font-display text-2xl mb-2">New articles are coming soon.</h2><p>Our clinical and academy team is preparing thoughtful guidance for you.</p></div>
            @endforelse
        </div>
        <div class="mt-10">{{ $posts->links() }}</div>
    </div>
</section>
@endsection
