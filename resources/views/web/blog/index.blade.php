@extends('web.layouts.app')
@section('title', __('web.pages.blog_title').' — '.config('clinic.name'))
@section('content')
@include('web.components.page-intro', [
    'eyebrow' => __('web.pages.blog_eyebrow'),
    'title' => __('web.pages.blog_title'),
    'lead' => __('web.pages.blog_lead'),
])
<section class="catalogue-shell">
    <div class="container-site blog-teaser__grid">
        @foreach (__('web.home.blog_posts') as $post)
            <article class="blog-card">
                <p class="text-label">{{ $post['category'] }}</p>
                <h2 class="font-display text-2xl mb-2">{{ $post['title'] }}</h2>
                <p>{{ $post['excerpt'] }}</p>
            </article>
        @endforeach
    </div>
</section>
@endsection
