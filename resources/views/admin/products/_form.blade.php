@php($product = $product ?? null)
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
    <div class="admin-panel__head"><h2 class="admin-panel__title">Product details</h2></div>
    <div class="admin-panel__body grid gap-4 md:grid-cols-2">
        <div>
            <label class="admin-label" for="product_category_id">Category</label>
            <select id="product_category_id" name="product_category_id" class="admin-input" required>
                <option value="">Select category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('product_category_id', $product?->product_category_id) === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div><label class="admin-label" for="name">Name</label><input id="name" name="name" class="admin-input" value="{{ old('name', $product?->name) }}" required></div>
        <div><label class="admin-label" for="sku">SKU</label><input id="sku" name="sku" class="admin-input" value="{{ old('sku', $product?->sku) }}" required></div>
        <div><label class="admin-label" for="barcode">Barcode</label><input id="barcode" name="barcode" class="admin-input" value="{{ old('barcode', $product?->barcode) }}"></div>
        <div><label class="admin-label" for="price">Price</label><input id="price" name="price" type="number" step="0.01" min="0" class="admin-input" value="{{ old('price', $product?->price) }}" required></div>
        <div><label class="admin-label" for="sale_price">Sale price</label><input id="sale_price" name="sale_price" type="number" step="0.01" min="0" class="admin-input" value="{{ old('sale_price', $product?->sale_price) }}"></div>
        <div><label class="admin-label" for="cost_price">Cost price</label><input id="cost_price" name="cost_price" type="number" step="0.01" min="0" class="admin-input" value="{{ old('cost_price', $product?->cost_price) }}"></div>
        <div><label class="admin-label" for="weight_kg">Weight (kg)</label><input id="weight_kg" name="weight_kg" type="number" step="0.001" min="0" class="admin-input" value="{{ old('weight_kg', $product?->weight_kg) }}"></div>
        <div><label class="admin-label" for="stock_quantity">Stock quantity</label><input id="stock_quantity" name="stock_quantity" type="number" min="0" class="admin-input" value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}"></div>
        <div><label class="admin-label" for="low_stock_threshold">Low-stock threshold</label><input id="low_stock_threshold" name="low_stock_threshold" type="number" min="0" class="admin-input" value="{{ old('low_stock_threshold', $product?->low_stock_threshold ?? 5) }}"></div>
        <div class="md:col-span-2"><label class="admin-label" for="description">Description</label><textarea id="description" name="description" rows="4" class="admin-input">{{ old('description', $product?->description) }}</textarea></div>
        <div class="md:col-span-2"><label class="admin-label" for="usage_instructions">Usage</label><textarea id="usage_instructions" name="usage_instructions" rows="3" class="admin-input">{{ old('usage_instructions', $product?->usage_instructions) }}</textarea></div>
        <div class="md:col-span-2"><label class="admin-label" for="ingredients">Ingredients</label><textarea id="ingredients" name="ingredients" rows="3" class="admin-input">{{ old('ingredients', $product?->ingredients) }}</textarea></div>
    </div>
</div>
<div class="admin-panel mb-6">
    <div class="admin-panel__head"><h2 class="admin-panel__title">Product image</h2></div>
    <div class="admin-panel__body">
        @if($product?->imageUrl())
            <img src="{{ $product->imageUrl() }}" alt="" class="admin-photo-preview mb-4">
        @endif
        <label class="admin-label" for="image">{{ $product ? 'Replace photo' : 'Upload photo' }}</label>
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="admin-input" @required(! $product)>
        <p class="mt-2 text-xs text-[var(--admin-text-muted)]">JPG, PNG or WebP · max 2MB. This photo appears on the public store.</p>
    </div>
</div>
<div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Visibility</h2></div><div class="admin-panel__body flex flex-wrap gap-6"><label class="admin-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))> Active on website</label><label class="admin-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured ?? false))> Featured</label><label class="admin-check"><input type="checkbox" name="delivery_eligible" value="1" @checked(old('delivery_eligible', $product?->delivery_eligible ?? true))> Delivery eligible</label><label class="admin-check"><input type="checkbox" name="pickup_eligible" value="1" @checked(old('pickup_eligible', $product?->pickup_eligible ?? true))> Pickup eligible</label></div></div>
