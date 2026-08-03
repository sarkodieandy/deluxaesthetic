@php($post = $post ?? null)
@if($errors->any())<div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if(session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
    <div class="space-y-6">
        <div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Article content</h2></div><div class="admin-panel__body space-y-4">
            <div><label class="admin-label" for="title">Title</label><input id="title" name="title" class="admin-input" maxlength="190" required value="{{ old('title', $post?->title) }}"></div>
            <div><label class="admin-label" for="category">Category</label><input id="category" name="category" class="admin-input" maxlength="100" placeholder="Skin health, Academy, Aftercare…" value="{{ old('category', $post?->category) }}"></div>
            <div><label class="admin-label" for="excerpt">Short excerpt</label><textarea id="excerpt" name="excerpt" class="admin-input" rows="3" maxlength="500" required>{{ old('excerpt', $post?->excerpt) }}</textarea><p class="mt-1 text-xs text-[var(--admin-text-muted)]">Displayed on the blog listing and in search results.</p></div>
            <div><label class="admin-label" for="body">Article body</label><textarea id="body" name="body" class="admin-input" rows="18" required>{{ old('body', $post?->body) }}</textarea><p class="mt-1 text-xs text-[var(--admin-text-muted)]">Use blank lines to separate paragraphs.</p></div>
        </div>
        <div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Cover image</h2></div><div class="admin-panel__body space-y-4">
            @if($post?->coverImageUrl())<img src="{{ $post->coverImageUrl() }}" alt="" class="admin-photo-preview max-h-64 w-full object-cover">@endif
            <div><label class="admin-label" for="cover_image">Upload image</label><input id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/webp" class="admin-input"></div>
            <div><label class="admin-label" for="cover_image_url">Or image URL</label><input id="cover_image_url" name="cover_image_url" type="url" class="admin-input" placeholder="https://example.com/image.jpg" value="{{ old('cover_image_url', \App\Support\GalleryMedia::urlFieldValue($post?->cover_image_path)) }}"></div>
            <div><label class="admin-label" for="cover_image_alt">Image description</label><input id="cover_image_alt" name="cover_image_alt" class="admin-input" maxlength="255" value="{{ old('cover_image_alt', $post?->cover_image_alt) }}"></div>
        </div>
        <div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Search appearance</h2></div><div class="admin-panel__body space-y-4">
            <div><label class="admin-label" for="seo_title">SEO title</label><input id="seo_title" name="seo_title" class="admin-input" maxlength="70" value="{{ old('seo_title', $post?->seo_title) }}" placeholder="Defaults to article title"></div>
            <div><label class="admin-label" for="seo_description">SEO description</label><textarea id="seo_description" name="seo_description" class="admin-input" rows="3" maxlength="170" placeholder="Defaults to excerpt">{{ old('seo_description', $post?->seo_description) }}</textarea></div>
        </div>
    </div>
    <aside class="space-y-6">
        <div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Publishing</h2></div><div class="admin-panel__body space-y-4">
            <div><label class="admin-label" for="status">Status</label><select id="status" name="status" class="admin-input"><option value="draft" @selected(old('status', $post?->status ?? 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $post?->status) === 'published')>Published</option></select></div>
            <div><label class="admin-label" for="published_at">Publish date and time</label><input id="published_at" name="published_at" type="datetime-local" class="admin-input" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\TH:i')) }}"><p class="mt-1 text-xs text-[var(--admin-text-muted)]">Leave empty to publish immediately. Choose a future time to schedule.</p></div>
            <label class="admin-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post?->is_featured ?? false))> Feature this article</label>
            <button type="submit" class="btn btn-primary w-full">{{ $post ? 'Save changes' : 'Create article' }}</button>
            <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary w-full">Back to blog</a>
        </div>
    </aside>
</div>
