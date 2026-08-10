@php
    use App\Support\GalleryMedia;
    $treatment = $treatment ?? null;
    $selectedPractitioners = collect(old(
        'practitioner_profile_ids',
        $treatment?->practitioners?->pluck('id')->all() ?? []
    ))->map(fn ($id) => (string) $id);
@endphp

@if ($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

@if ($categories->isEmpty())
    <div class="mb-6 border border-[var(--color-warning)] bg-white px-4 py-3">
        Create and publish a <a class="underline" href="{{ route('admin.treatment-categories.create') }}">procedure category</a> before adding a procedure.
    </div>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><div><h2 class="admin-panel__title">Procedure details</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">The name, category and summary appear in the public clinical catalogue.</p></div></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div>
            <label class="admin-label" for="treatment_category_id">Category</label>
            <select id="treatment_category_id" name="treatment_category_id" class="admin-input" required>
                <option value="">Select category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('treatment_category_id', $treatment?->treatment_category_id) === (string) $category->id)>{{ $category->name }}{{ $category->is_active ? '' : ' (hidden)' }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="admin-label" for="name">Procedure name</label><input id="name" name="name" class="admin-input" value="{{ old('name', $treatment?->name) }}" required></div>
        <div class="md:col-span-2"><label class="admin-label" for="short_description">Card summary</label><input id="short_description" name="short_description" maxlength="255" class="admin-input" value="{{ old('short_description', $treatment?->short_description) }}" required></div>
        <div class="md:col-span-2"><label class="admin-label" for="description">Full public description</label><textarea id="description" name="description" rows="5" class="admin-input">{{ old('description', $treatment?->description) }}</textarea></div>
        <div><label class="admin-label" for="duration_minutes">Duration (minutes)</label><input id="duration_minutes" name="duration_minutes" type="number" min="1" max="600" class="admin-input" value="{{ old('duration_minutes', $treatment?->duration_minutes ?? 60) }}" required></div>
        <div><label class="admin-label" for="recovery_days">Typical recovery (days)</label><input id="recovery_days" name="recovery_days" type="number" min="0" class="admin-input" value="{{ old('recovery_days', $treatment?->recovery_days ?? 0) }}"></div>
        <div><label class="admin-label" for="recommended_sessions">Recommended sessions</label><input id="recommended_sessions" name="recommended_sessions" type="number" min="1" max="50" class="admin-input" value="{{ old('recommended_sessions', $treatment?->recommended_sessions ?? 1) }}"></div>
        <div><label class="admin-label" for="sort_order">Display order</label><input id="sort_order" name="sort_order" type="number" min="0" max="9999" class="admin-input" value="{{ old('sort_order', $treatment?->sort_order ?? 10) }}" required><p class="mt-2 text-xs text-[var(--admin-text-muted)]">Lower numbers appear first within the category.</p></div>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><div><h2 class="admin-panel__title">Pricing and booking</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">All public clinic fees are displayed in Ghana cedis.</p></div></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div><label class="admin-label" for="price">Standard price (GHS)</label><input id="price" name="price" type="number" step="0.01" min="0" class="admin-input" value="{{ old('price', $treatment?->price) }}" required></div>
        <div><label class="admin-label" for="promotional_price">Promotional price (GHS)</label><input id="promotional_price" name="promotional_price" type="number" step="0.01" min="0" class="admin-input" value="{{ old('promotional_price', $treatment?->promotional_price) }}"><p class="mt-2 text-xs text-[var(--admin-text-muted)]">Leave blank when there is no offer.</p></div>
        <div><label class="admin-label" for="deposit_amount">Booking deposit (GHS)</label><input id="deposit_amount" name="deposit_amount" type="number" step="0.01" min="0" class="admin-input" value="{{ old('deposit_amount', $treatment?->deposit_amount) }}"></div>
        <div><label class="admin-label" for="buffer_before_minutes">Buffer before (minutes)</label><input id="buffer_before_minutes" name="buffer_before_minutes" type="number" min="0" max="240" class="admin-input" value="{{ old('buffer_before_minutes', $treatment?->buffer_before_minutes ?? 0) }}"></div>
        <div><label class="admin-label" for="buffer_after_minutes">Buffer after (minutes)</label><input id="buffer_after_minutes" name="buffer_after_minutes" type="number" min="0" max="240" class="admin-input" value="{{ old('buffer_after_minutes', $treatment?->buffer_after_minutes ?? 15) }}"></div>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Clinical guidance</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2"><label class="admin-label" for="benefits">Benefits (one per line)</label><textarea id="benefits" name="benefits" rows="4" class="admin-input">{{ old('benefits', $treatment?->benefits ? implode("\n", $treatment->benefits) : '') }}</textarea></div>
        <div><label class="admin-label" for="suitable_candidates">Who it may suit</label><textarea id="suitable_candidates" name="suitable_candidates" rows="4" class="admin-input">{{ old('suitable_candidates', $treatment?->suitable_candidates) }}</textarea></div>
        <div><label class="admin-label" for="contraindications">Important contraindications</label><textarea id="contraindications" name="contraindications" rows="4" class="admin-input">{{ old('contraindications', $treatment?->contraindications) }}</textarea></div>
        <div><label class="admin-label" for="preparation_instructions">Preparation</label><textarea id="preparation_instructions" name="preparation_instructions" rows="4" class="admin-input">{{ old('preparation_instructions', $treatment?->preparation_instructions) }}</textarea></div>
        <div><label class="admin-label" for="aftercare_instructions">Aftercare</label><textarea id="aftercare_instructions" name="aftercare_instructions" rows="4" class="admin-input">{{ old('aftercare_instructions', $treatment?->aftercare_instructions) }}</textarea></div>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Procedure image</h2></div>
    <div class="admin-panel__body">
        @if ($treatment?->imageUrl())<img src="{{ $treatment->imageUrl() }}" alt="" class="admin-photo-preview mb-4 max-h-64">@endif
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="admin-label" for="image">Upload image</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="admin-input"><p class="mt-2 text-xs text-[var(--admin-text-muted)]">JPG, PNG or WebP · max 4 MB.</p></div>
            <div><label class="admin-label" for="image_url">Or image URL</label><input id="image_url" name="image_url" type="url" class="admin-input" value="{{ old('image_url', GalleryMedia::urlFieldValue($treatment?->image_path)) }}" placeholder="https://example.com/procedure.jpg"></div>
        </div>
        @if ($treatment?->image_path)<label class="admin-check mt-4"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>@endif
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Available practitioners</h2></div>
    <div class="admin-panel__body">
        @if ($practitioners->isEmpty())
            <p class="text-sm text-[var(--admin-text-muted)]">No active practitioners are available yet. The procedure can still be saved and assigned later.</p>
        @else
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($practitioners as $practitioner)
                    <label class="admin-check"><input type="checkbox" name="practitioner_profile_ids[]" value="{{ $practitioner->id }}" @checked($selectedPractitioners->contains((string) $practitioner->id))> <span>{{ $practitioner->user?->name ?? 'Practitioner' }}<small class="block text-[var(--admin-text-muted)]">{{ $practitioner->displayTitle() }}</small></span></label>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Search appearance</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div><label class="admin-label" for="seo_title">SEO title</label><input id="seo_title" name="seo_title" maxlength="190" class="admin-input" value="{{ old('seo_title', $treatment?->seo_title) }}"></div>
        <div><label class="admin-label" for="seo_description">SEO description</label><textarea id="seo_description" name="seo_description" rows="3" maxlength="400" class="admin-input">{{ old('seo_description', $treatment?->seo_description) }}</textarea></div>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Visibility</h2></div>
    <div class="admin-panel__body flex flex-wrap gap-6">
        <label class="admin-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $treatment?->is_active ?? true))> Published on website</label>
        <label class="admin-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $treatment?->is_featured ?? false))> Featured procedure</label>
    </div>
</div>
