@php
    $p = $practitioner ?? null;
    $user = $p?->user;
    $social = $p?->social_links ?? [];
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
    <div class="admin-panel__head"><h2 class="font-display text-xl m-0">Profile</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="admin-label" for="name">Full name</label>
            <input id="name" name="name" type="text" class="admin-input" value="{{ old('name', $user?->name) }}" required>
        </div>
        <div>
            <label class="admin-label" for="email">Email</label>
            <input id="email" name="email" type="email" class="admin-input" value="{{ old('email', $user?->email) }}" required>
        </div>
        <div>
            <label class="admin-label" for="phone">Phone</label>
            <input id="phone" name="phone" type="text" class="admin-input" value="{{ old('phone', $user?->phone) }}">
        </div>
        <div>
            <label class="admin-label" for="professional_title">Role / title shown on site</label>
            <input id="professional_title" name="professional_title" type="text" class="admin-input" value="{{ old('professional_title', $p?->professional_title) }}" required placeholder="e.g. Senior Aesthetician">
        </div>
        <div>
            <label class="admin-label" for="title">Internal title</label>
            <input id="title" name="title" type="text" class="admin-input" value="{{ old('title', $p?->title) }}">
        </div>
        <div class="md:col-span-2">
            <label class="admin-label" for="biography">Biography</label>
            <textarea id="biography" name="biography" rows="4" class="admin-input">{{ old('biography', $p?->biography) }}</textarea>
        </div>
        <div>
            <label class="admin-label" for="years_experience">Years experience</label>
            <input id="years_experience" name="years_experience" type="number" min="0" max="60" class="admin-input" value="{{ old('years_experience', $p?->years_experience ?? 0) }}">
        </div>
        <div>
            <label class="admin-label" for="sort_order">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="admin-input" value="{{ old('sort_order', $p?->sort_order ?? 10) }}">
        </div>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="font-display text-xl m-0">Portrait photo</h2></div>
    <div class="admin-panel__body">
        @if($p?->photo_path)
            <img src="{{ $p->photoUrl() }}" alt="" class="admin-photo-preview mb-4">
        @endif
        <label class="admin-label" for="photo">Upload image (JPG, PNG, WebP · max 4MB)</label>
        <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="admin-input">
        <p class="admin-help">Recommended: portrait crop, at least 800×1000px. This appears on the Our Team page.</p>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="font-display text-xl m-0">Social links</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div>
            <label class="admin-label" for="social_facebook">Facebook URL</label>
            <input id="social_facebook" name="social_facebook" type="url" class="admin-input" value="{{ old('social_facebook', $social['facebook'] ?? '') }}">
        </div>
        <div>
            <label class="admin-label" for="social_instagram">Instagram URL</label>
            <input id="social_instagram" name="social_instagram" type="url" class="admin-input" value="{{ old('social_instagram', $social['instagram'] ?? '') }}">
        </div>
        <div>
            <label class="admin-label" for="social_twitter">X / Twitter URL</label>
            <input id="social_twitter" name="social_twitter" type="url" class="admin-input" value="{{ old('social_twitter', $social['twitter'] ?? '') }}">
        </div>
        <div>
            <label class="admin-label" for="social_linkedin">LinkedIn URL</label>
            <input id="social_linkedin" name="social_linkedin" type="url" class="admin-input" value="{{ old('social_linkedin', $social['linkedin'] ?? '') }}">
        </div>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel__head"><h2 class="font-display text-xl m-0">Visibility</h2></div>
    <div class="admin-panel__body flex flex-wrap gap-6">
        <label class="admin-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $p?->is_active ?? true))> Active on website</label>
        <label class="admin-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $p?->is_featured ?? true))> Featured</label>
        <label class="admin-check"><input type="checkbox" name="is_ceo" value="1" @checked(old('is_ceo', $p?->is_ceo ?? false))> CEO / founder</label>
    </div>
</div>
