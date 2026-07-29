@php($branch = $branch ?? null)

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
    <div class="admin-panel__head"><h2 class="admin-panel__title">Branch details</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div>
            <label class="admin-label" for="name">Branch name</label>
            <input id="name" name="name" class="admin-input" value="{{ old('name', $branch?->name) }}" required placeholder="East Legon flagship">
        </div>
        <div>
            <label class="admin-label" for="slug">URL slug</label>
            <input id="slug" name="slug" class="admin-input" value="{{ old('slug', $branch?->slug) }}" placeholder="auto-generated if empty">
            <p class="mt-1 text-xs text-[var(--admin-text-muted)]">Lowercase letters, numbers, and hyphens only.</p>
        </div>
        <div>
            <label class="admin-label" for="sort_order">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="admin-input" value="{{ old('sort_order', $branch?->sort_order ?? 10) }}">
        </div>
        <div>
            <label class="admin-label" for="hours_summary">Opening hours (display)</label>
            <input id="hours_summary" name="hours_summary" class="admin-input" value="{{ old('hours_summary', $branch?->hours_summary ?? config('clinic.hours')) }}" placeholder="Daily · 7:00am–7:00pm">
        </div>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Contact</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div>
            <label class="admin-label" for="email">Email</label>
            <input id="email" name="email" type="email" class="admin-input" value="{{ old('email', $branch?->email) }}">
        </div>
        <div>
            <label class="admin-label" for="phone">Phone</label>
            <input id="phone" name="phone" class="admin-input" value="{{ old('phone', $branch?->phone) }}">
        </div>
        <div class="md:col-span-2">
            <label class="admin-label" for="whatsapp">WhatsApp</label>
            <input id="whatsapp" name="whatsapp" class="admin-input" value="{{ old('whatsapp', $branch?->whatsapp) }}" placeholder="+233…">
        </div>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Address</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="admin-label" for="address_line_1">Address line 1</label>
            <input id="address_line_1" name="address_line_1" class="admin-input" value="{{ old('address_line_1', $branch?->address_line_1) }}">
        </div>
        <div class="md:col-span-2">
            <label class="admin-label" for="address_line_2">Address line 2</label>
            <input id="address_line_2" name="address_line_2" class="admin-input" value="{{ old('address_line_2', $branch?->address_line_2) }}">
        </div>
        <div>
            <label class="admin-label" for="city">City</label>
            <input id="city" name="city" class="admin-input" value="{{ old('city', $branch?->city ?? 'Accra') }}">
        </div>
        <div>
            <label class="admin-label" for="region">Region</label>
            <input id="region" name="region" class="admin-input" value="{{ old('region', $branch?->region) }}">
        </div>
        <div>
            <label class="admin-label" for="country">Country</label>
            <input id="country" name="country" class="admin-input" value="{{ old('country', $branch?->country ?? 'Ghana') }}">
        </div>
        <div>
            <label class="admin-label" for="postal_code">Postal / GPS code</label>
            <input id="postal_code" name="postal_code" class="admin-input" value="{{ old('postal_code', $branch?->postal_code) }}">
        </div>
    </div>
</div>

<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Map</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div>
            <label class="admin-label" for="latitude">Latitude</label>
            <input id="latitude" name="latitude" type="number" step="any" class="admin-input" value="{{ old('latitude', $branch?->latitude) }}">
        </div>
        <div>
            <label class="admin-label" for="longitude">Longitude</label>
            <input id="longitude" name="longitude" type="number" step="any" class="admin-input" value="{{ old('longitude', $branch?->longitude) }}">
        </div>
        <div class="md:col-span-2">
            <label class="admin-label" for="map_embed_url">Google Maps embed URL</label>
            <textarea id="map_embed_url" name="map_embed_url" rows="2" class="admin-input">{{ old('map_embed_url', $branch?->map_embed_url ?? config('clinic.map_embed_url')) }}</textarea>
            <p class="mt-1 text-xs text-[var(--admin-text-muted)]">Used on contact pages and location widgets when this branch is featured.</p>
        </div>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Status</h2></div>
    <div class="admin-panel__body space-y-3">
        <p class="text-sm text-[var(--admin-text-muted)] max-w-2xl">Active branches appear in online booking and staff workflows. One branch should be marked as <strong>primary</strong> (default for new clients).</p>
        <div class="flex flex-wrap gap-6">
            <label class="admin-check">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $branch?->is_active ?? true))>
                Active
            </label>
            <label class="admin-check">
                <input type="checkbox" name="is_primary" value="1" @checked(old('is_primary', $branch?->is_primary ?? false))>
                Primary branch
            </label>
        </div>
    </div>
</div>
