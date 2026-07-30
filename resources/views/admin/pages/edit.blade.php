@extends('admin.layouts.app')
@section('title', 'Edit '.$page->name)
@section('heading', 'Edit '.$page->name)
@section('breadcrumb', 'Content / Website pages / '.$page->name)
@section('content')
@if(session('status') === 'page-updated')<div class="admin-callout"><div><p class="admin-callout__title">Page updated</p><p class="admin-callout__copy">The latest content and publishing settings have been saved.</p></div><a href="{{ route('admin.pages.preview', $page) }}" class="btn btn-secondary" target="_blank">Preview page</a></div>@endif
@if($errors->any())<div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ route('admin.pages.update', $page) }}" data-page-editor>
    @csrf @method('PUT')
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_21rem]">
        <div class="space-y-6">
            <section class="admin-panel">
                <div class="admin-panel__head"><div><h2 class="admin-panel__title">Page identity & SEO</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Controls how this page appears in search and browser tabs.</p></div></div>
                <div class="admin-panel__body grid gap-5 md:grid-cols-2">
                    <div><label class="admin-label" for="name">Admin page name</label><input id="name" name="name" class="admin-input" value="{{ old('name', $page->name) }}" required></div>
                    <div><label class="admin-label">Public route</label><input class="admin-input" value="{{ route($page->route_name) }}" disabled></div>
                    <div class="md:col-span-2"><label class="admin-label" for="seo_title">SEO title</label><input id="seo_title" name="seo_title" class="admin-input" maxlength="70" value="{{ old('seo_title', $page->seo_title) }}" placeholder="Leave blank to use the website default"><p class="mt-1 text-xs text-[var(--admin-text-muted)]">Recommended: 50–60 characters.</p></div>
                    <div class="md:col-span-2"><label class="admin-label" for="meta_description">Meta description</label><textarea id="meta_description" name="meta_description" class="admin-input" rows="3" maxlength="170" placeholder="A concise description for Google results.">{{ old('meta_description', $page->meta_description) }}</textarea></div>
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel__head"><div><h2 class="admin-panel__title">Hero content</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Overrides the leading content on the public page. Empty fields retain the designed defaults.</p></div></div>
                <div class="admin-panel__body grid gap-5">
                    <div><label class="admin-label" for="hero_eyebrow">Eyebrow</label><input id="hero_eyebrow" name="hero_eyebrow" class="admin-input" value="{{ old('hero_eyebrow', $page->hero_eyebrow) }}" placeholder="The De Luxe edit"></div>
                    <div><label class="admin-label" for="hero_title">Hero heading</label><textarea id="hero_title" name="hero_title" class="admin-input" rows="2" placeholder="Professional care, beyond the clinic.">{{ old('hero_title', $page->hero_title) }}</textarea></div>
                    <div><label class="admin-label" for="hero_body">Hero introduction</label><textarea id="hero_body" name="hero_body" class="admin-input" rows="4">{{ old('hero_body', $page->hero_body) }}</textarea></div>
                    <div><label class="admin-label" for="hero_image_url">Hero image URL</label><input id="hero_image_url" name="hero_image_url" class="admin-input" value="{{ old('hero_image_url', $page->hero_image_url) }}" placeholder="https://…"><p class="mt-1 text-xs text-[var(--admin-text-muted)]">Use an HTTPS image URL. Leave blank to keep the designed page image.</p></div>
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel__head"><div><h2 class="admin-panel__title">Flexible sections</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Add editorial sections below the existing designed content. Reorder them with the arrow controls.</p></div><button type="button" class="btn btn-secondary" data-add-page-section>Add section</button></div>
                <div class="admin-panel__body space-y-4" data-page-sections>
                    @foreach(old('sections', $page->sections ?? []) as $index => $section)
                        @include('admin.pages.section-fields', ['index' => $index, 'section' => $section])
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="admin-panel">
                <div class="admin-panel__head"><h2 class="admin-panel__title">Publishing</h2></div>
                <div class="admin-panel__body space-y-4">
                    <input type="hidden" name="is_published" value="0">
                    <label class="admin-check"><input type="checkbox" name="is_published" value="1" @checked((bool) old('is_published', $page->is_published))> Published and visible</label>
                    <p class="text-sm text-[var(--admin-text-muted)]">Unpublishing hides this page from public visitors. Admin preview remains available.</p>
                    @if($page->published_at)<p class="text-xs text-[var(--admin-text-muted)]">Published {{ $page->published_at->format('d M Y, H:i') }}</p>@endif
                </div>
            </section>
            <div class="grid gap-3">
                <button class="btn btn-primary">Save page</button>
                <a href="{{ route('admin.pages.preview', $page) }}" target="_blank" class="btn btn-secondary">Open preview</a>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Back to pages</a>
            </div>
        </aside>
    </div>
</form>

<template data-page-section-template>
    @include('admin.pages.section-fields', ['index' => '__INDEX__', 'section' => []])
</template>
@endsection
