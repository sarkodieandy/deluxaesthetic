@extends('web.layouts.app')
@section('title', $title ?? config('clinic.name'))
@section('content')
<section class="section">
    <div class="container-site">
        <p class="text-label mb-3">{{ config('clinic.wordmark') }}</p>
        <h1 class="text-page-title mb-4">{{ $heading }}</h1>
        <div class="h-px w-20 bg-[var(--color-bronze)] mb-8"></div>
        <p class="max-w-2xl text-[var(--color-soft-grey)] mb-10">{{ $copy }}</p>
        @isset($cta)
            <a href="{{ $cta['url'] }}" class="btn btn-primary">{{ $cta['label'] }}</a>
        @endisset
    </div>
</section>
@endsection
