@php
    use App\Support\GalleryMedia;
    $category = $category ?? null;
@endphp

@if ($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Category details</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div><label class="admin-label" for="name">Name</label><input id="name" name="name" class="admin-input" value="{{ old('name', $category?->name) }}" required></div>
        <div><label class="admin-label" for="sort_order">Display order</label><input id="sort_order" name="sort_order" type="number" min="0" max="9999" class="admin-input" value="{{ old('sort_order', $category?->sort_order ?? 10) }}" required><p class="mt-2 text-xs text-[var(--admin-text-muted)]">Lower numbers appear first.</p></div>
        <div class="md:col-span-2"><label class="admin-label" for="description">Public description</label><textarea id="description" name="description" rows="4" class="admin-input">{{ old('description', $category?->description) }}</textarea></div>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Category cover</h2></div>
    <div class="admin-panel__body">
        @if ($category?->imageUrl())<img src="{{ $category->imageUrl() }}" alt="" class="admin-photo-preview mb-4 max-h-56">@endif
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="admin-label" for="image">Upload image</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="admin-input"><p class="mt-2 text-xs text-[var(--admin-text-muted)]">JPG, PNG or WebP · max 8 MB.</p></div>
            <div><label class="admin-label" for="image_url">Or image URL</label><input id="image_url" name="image_url" type="url" class="admin-input" value="{{ old('image_url', GalleryMedia::urlFieldValue($category?->image_path)) }}" placeholder="https://example.com/procedure.jpg"></div>
        </div>
        @if ($category?->image_path)<label class="admin-check mt-4"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>@endif
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Visibility</h2></div>
    <div class="admin-panel__body"><label class="admin-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true))> Published on the clinical procedures page</label></div>
</div>
