@php
    use App\Support\GalleryMedia;
    $item = $item ?? null;
    $type = old('type', $item?->type ?? ($defaultType ?? 'gallery'));
    $isEdit = (bool) $item;
@endphp

@if ($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Gallery item</h2>
    </div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div>
            <label class="admin-label" for="title">Title</label>
            <input id="title" name="title" class="admin-input" value="{{ old('title', $item?->title) }}" required>
            @error('title')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="admin-label" for="gallery-type">Type</label>
            <select id="gallery-type" name="type" class="admin-input" required data-gallery-type-select>
                <option value="gallery" @selected($type === 'gallery')>Gallery image (homepage grid)</option>
                <option value="before_after" @selected($type === 'before_after')>Before / after (comparison slider)</option>
            </select>
            @error('type')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="admin-label" for="description">Description</label>
            <textarea id="description" name="description" rows="3" class="admin-input">{{ old('description', $item?->description) }}</textarea>
        </div>
        <div>
            <label class="admin-label" for="alt_text">Alt text (accessibility)</label>
            <input id="alt_text" name="alt_text" class="admin-input" value="{{ old('alt_text', $item?->alt_text) }}">
        </div>
        <div>
            <label class="admin-label" for="sort_order">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="admin-input" value="{{ old('sort_order', $item?->sort_order ?? 10) }}">
        </div>
    </div>
</div>

<div class="admin-panel mb-6" data-gallery-panel="gallery" @if($type !== 'gallery') hidden @endif>
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Gallery image</h2>
    </div>
    <div class="admin-panel__body">
        <p class="text-sm text-[var(--admin-text-muted)] mb-4 max-w-2xl">Upload from your device <strong>or</strong> paste a direct image URL (https). Used for the homepage <strong>Our Work</strong> grid when featured.</p>
        @if($item?->imageUrl())
            <img src="{{ $item->imageUrl() }}" alt="" class="admin-photo-preview mb-4 max-h-48">
        @endif
        <label class="admin-label" for="image">Upload from device</label>
        <input
            id="image"
            name="image"
            type="file"
            accept="image/jpeg,image/png,image/webp,image/jpg"
            class="admin-input mb-4"
        >
        @error('image')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
        <label class="admin-label" for="image_url">Or image URL</label>
        <input
            id="image_url"
            name="image_url"
            type="url"
            class="admin-input"
            placeholder="https://example.com/photo.jpg"
            value="{{ old('image_url', GalleryMedia::urlFieldValue($item?->image_path)) }}"
        >
        @error('image_url')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
        <p class="mt-2 text-xs text-[var(--admin-text-muted)]">Device upload takes priority if both are provided. JPG, PNG, or WebP · max 8 MB.</p>
    </div>
</div>

<div class="admin-panel mb-6" data-gallery-panel="before_after" @if($type !== 'before_after') hidden @endif>
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Before &amp; after images</h2>
    </div>
    <div class="admin-panel__body grid gap-6 md:grid-cols-2">
        <p class="md:col-span-2 text-sm text-[var(--admin-text-muted)] max-w-3xl">For each side, upload from your device <strong>or</strong> paste an image URL. Both before and after are required. Mark <strong>Featured on homepage</strong> for the main slider.</p>

        <div class="before-after-upload">
            <label class="admin-label before-after-upload__heading" for="before_image">Before</label>
            @if($item?->beforeImageUrl())
                <img src="{{ $item->beforeImageUrl() }}" alt="" class="admin-photo-preview mb-3 max-h-40 w-full object-cover">
            @endif
            <label class="admin-label" for="before_image">Upload from device</label>
            <input
                id="before_image"
                name="before_image"
                type="file"
                accept="image/jpeg,image/png,image/webp,image/jpg"
                class="admin-input mb-3"
            >
            <label class="admin-label" for="before_image_url">Or image URL</label>
            <input
                id="before_image_url"
                name="before_image_url"
                type="url"
                class="admin-input"
                placeholder="https://example.com/before.jpg"
                value="{{ old('before_image_url', GalleryMedia::urlFieldValue($item?->before_image_path)) }}"
            >
            @error('before_image')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
            @error('before_image_url')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
        </div>

        <div class="before-after-upload before-after-upload--after">
            <label class="admin-label before-after-upload__heading" for="after_image">After</label>
            @if($item?->afterImageUrl())
                <img src="{{ $item->afterImageUrl() }}" alt="" class="admin-photo-preview mb-3 max-h-40 w-full object-cover">
            @endif
            <label class="admin-label" for="after_image">Upload from device</label>
            <input
                id="after_image"
                name="after_image"
                type="file"
                accept="image/jpeg,image/png,image/webp,image/jpg"
                class="admin-input mb-3"
            >
            <label class="admin-label" for="after_image_url">Or image URL</label>
            <input
                id="after_image_url"
                name="after_image_url"
                type="url"
                class="admin-input"
                placeholder="https://example.com/after.jpg"
                value="{{ old('after_image_url', GalleryMedia::urlFieldValue($item?->after_image_path)) }}"
            >
            @error('after_image')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
            @error('after_image_url')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Visibility</h2>
    </div>
    <div class="admin-panel__body space-y-4">
        <div class="flex flex-wrap gap-6">
            <label class="admin-check">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item?->is_active ?? true))>
                Active on website
            </label>
            <label class="admin-check">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item?->is_featured ?? false))>
                Featured on homepage
            </label>
        </div>
    </div>
</div>

<script>
    (function () {
        const select = document.querySelector('[data-gallery-type-select]');
        const panels = document.querySelectorAll('[data-gallery-panel]');
        if (!select) return;
        select.addEventListener('change', () => {
            const type = select.value;
            panels.forEach((panel) => {
                panel.hidden = panel.getAttribute('data-gallery-panel') !== type;
            });
        });
    })();
</script>
