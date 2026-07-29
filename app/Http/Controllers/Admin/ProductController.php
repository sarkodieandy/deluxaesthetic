<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\StoreProductRequest;
use App\Http\Requests\Admin\Products\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->with(['category', 'images'])->orderByDesc('is_featured')->orderBy('name')->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => $this->activeCategories(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $product = Product::create([
                'product_category_id' => (int) $data['product_category_id'],
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['name']),
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'description' => $data['description'] ?? null,
                'usage_instructions' => $data['usage_instructions'] ?? null,
                'ingredients' => $data['ingredients'] ?? null,
                'price' => $data['price'],
                'sale_price' => $data['sale_price'] ?? null,
                'cost_price' => $data['cost_price'] ?? null,
                'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
                'low_stock_threshold' => (int) ($data['low_stock_threshold'] ?? 5),
                'weight_kg' => $data['weight_kg'] ?? null,
                'delivery_eligible' => $request->boolean('delivery_eligible', true),
                'pickup_eligible' => $request->boolean('pickup_eligible', true),
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
            ]);

            $this->syncPrimaryImage($product, $request->file('image'), $data['name']);
        });

        return redirect()->route('admin.products.index')->with('status', 'Product added successfully.');
    }

    public function edit(Product $product): View
    {
        $product->load(['category', 'images']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->activeCategories(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $product) {
            $product->update([
                'product_category_id' => (int) $data['product_category_id'],
                'name' => $data['name'],
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'description' => $data['description'] ?? null,
                'usage_instructions' => $data['usage_instructions'] ?? null,
                'ingredients' => $data['ingredients'] ?? null,
                'price' => $data['price'],
                'sale_price' => $data['sale_price'] ?? null,
                'cost_price' => $data['cost_price'] ?? null,
                'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
                'low_stock_threshold' => (int) ($data['low_stock_threshold'] ?? 5),
                'weight_kg' => $data['weight_kg'] ?? null,
                'delivery_eligible' => $request->boolean('delivery_eligible', true),
                'pickup_eligible' => $request->boolean('pickup_eligible', true),
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
            ]);

            if ($request->hasFile('image')) {
                $this->deleteImages($product);
                $this->syncPrimaryImage($product, $request->file('image'), $data['name']);
            }
        });

        return redirect()->route('admin.products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImages($product);
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product removed.');
    }

    /**
     * @return \Illuminate\Support\Collection<int, ProductCategory>
     */
    private function activeCategories()
    {
        return ProductCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function syncPrimaryImage(Product $product, ?UploadedFile $file, string $name): void
    {
        if (! $file) {
            return;
        }

        if (! $file->isValid()) {
            throw new \RuntimeException('Product photo upload failed: '.$file->getErrorMessage());
        }

        $path = $file->store('products', 'public');

        if (! $path || ! Storage::disk('public')->exists($path)) {
            throw new \RuntimeException('Product photo could not be stored.');
        }

        ProductImage::create([
            'product_id' => $product->id,
            'path' => $path,
            'alt_text' => $name,
            'sort_order' => 0,
            'is_primary' => true,
        ]);
    }

    private function deleteImages(Product $product): void
    {
        foreach ($product->images as $image) {
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        }

        $product->images()->delete();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $i = 2;

        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
