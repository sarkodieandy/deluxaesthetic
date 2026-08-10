@php
    $item = $item ?? null;
    $currentType = old('type', $item?->type ?? 'student_story');
    $remoteImage = $item?->image_path && filter_var($item->image_path, FILTER_VALIDATE_URL) ? $item->image_path : '';
@endphp

@if($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white p-4 text-[var(--color-error)]">
        <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div><h2 class="admin-panel__title">Showcase content</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Published items appear in their designed Academy section. Use Website order to control their sequence.</p></div>
    </div>
    <div class="admin-panel__body grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label" for="showcase_type">Content type</label>
            <select id="showcase_type" name="type" class="admin-input" required>
                @foreach(\App\Models\AcademyShowcaseItem::TYPES as $value => $label)
                    <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label" for="showcase_title">Title</label>
            <input id="showcase_title" name="title" class="admin-input" required maxlength="160" value="{{ old('title', $item?->title) }}">
        </div>
        <div>
            <label class="admin-label" for="showcase_subtitle">Subtitle / location / credential</label>
            <input id="showcase_subtitle" name="subtitle" class="admin-input" maxlength="200" value="{{ old('subtitle', $item?->subtitle) }}">
        </div>
        <div>
            <label class="admin-label" for="showcase_sort_order">Website order</label>
            <input id="showcase_sort_order" name="sort_order" type="number" min="0" class="admin-input" value="{{ old('sort_order', $item?->sort_order ?? 10) }}">
        </div>
        <div class="md:col-span-2">
            <label class="admin-label" for="showcase_body">Story, review or description</label>
            <textarea id="showcase_body" name="body" rows="5" maxlength="3000" class="admin-input">{{ old('body', $item?->body) }}</textarea>
            <p class="mt-2 text-xs text-[var(--admin-text-muted)]">Required for training steps, countries, student stories, skill reviews, career benefits and training experience.</p>
        </div>
        <div>
            <label class="admin-label" for="showcase_image">Upload image</label>
            <input id="showcase_image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="admin-input">
            @if($item?->imageUrl())
                <img src="{{ $item->imageUrl() }}" alt="" class="admin-photo-preview mt-3 max-h-40">
                <label class="admin-check mt-3"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
            @endif
        </div>
        <div>
            <label class="admin-label" for="showcase_image_url">Or image URL</label>
            <input id="showcase_image_url" name="image_url" type="url" class="admin-input" value="{{ old('image_url', $remoteImage) }}" placeholder="https://...">
            <label class="admin-label mt-4" for="showcase_video_url">Video URL</label>
            <input id="showcase_video_url" name="video_url" type="url" class="admin-input" value="{{ old('video_url', $item?->video_url) }}" placeholder="YouTube, Vimeo or an HTTPS video page">
            <p class="mt-2 text-xs text-[var(--admin-text-muted)]">A video URL is required for Student video. YouTube and Vimeo play securely inside the Academy page.</p>
        </div>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Visibility</h2></div>
    <div class="admin-panel__body flex flex-wrap gap-6">
        <label class="admin-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item?->is_active ?? true))> Published on Academy page</label>
        <label class="admin-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item?->is_featured ?? false))> Featured first</label>
    </div>
</div>
