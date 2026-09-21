<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Cart\CartService;
use App\Support\WhatsAppOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    private const MAX_PRICE_FILTER = 1_000_000;

    public function __construct(private readonly CartService $carts) {}

    public function index(Request $request): View
    {
        $query = Product::query()
            ->with(['category', 'images'])
            ->where('is_active', true);

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%');
            });
        }

        if ($categoryId = $request->integer('category')) {
            $query->where('product_category_id', $categoryId);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        $availablePriceRange = (clone $query)
            ->selectRaw('MIN(COALESCE(sale_price, price)) as minimum, MAX(COALESCE(sale_price, price)) as maximum')
            ->first();

        $minimumPrice = $this->priceFilterValue($request->query('min_price'));
        $maximumPrice = $this->priceFilterValue($request->query('max_price'));

        if ($minimumPrice !== null && $maximumPrice !== null && $minimumPrice > $maximumPrice) {
            [$minimumPrice, $maximumPrice] = [$maximumPrice, $minimumPrice];
        }

        $query
            ->when($minimumPrice !== null, fn ($query) => $query->whereRaw(
                'CAST(COALESCE(sale_price, price) AS DECIMAL(12, 2)) >= CAST(? AS DECIMAL(12, 2))',
                [$minimumPrice]
            ))
            ->when($maximumPrice !== null, fn ($query) => $query->whereRaw(
                'CAST(COALESCE(sale_price, price) AS DECIMAL(12, 2)) <= CAST(? AS DECIMAL(12, 2))',
                [$maximumPrice]
            ));

        $sort = $request->string('sort')->toString() ?: 'featured';
        match ($sort) {
            'newest' => $query->latest('id'),
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('is_featured')->orderBy('name'),
        };

        $cart = $this->carts->resolve();
        $cartProductIds = $cart->items->pluck('product_id')->all();

        $products = $query->paginate(12)->withQueryString();

        return view('web.store.index', [
            'products' => $products,
            'whatsAppOrderUrls' => $products->getCollection()
                ->mapWithKeys(fn (Product $product) => [$product->id => WhatsAppOrder::productUrl($product)]),
            'categories' => ProductCategory::query()
                ->where('is_active', true)
                ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('name')
                ->get(),
            'cartProductIds' => $cartProductIds,
            'filters' => [
                'q' => $search ?? '',
                'category' => $categoryId ?: null,
                'sort' => $sort,
                'in_stock' => $request->boolean('in_stock'),
                'min_price' => $minimumPrice,
                'max_price' => $maximumPrice,
            ],
            'availablePriceRange' => [
                'minimum' => $availablePriceRange?->minimum !== null ? (float) $availablePriceRange->minimum : null,
                'maximum' => $availablePriceRange?->maximum !== null ? (float) $availablePriceRange->maximum : null,
            ],
            'pricePresets' => [
                ['label' => 'Under GHS 200', 'minimum' => null, 'maximum' => 200],
                ['label' => 'GHS 200–500', 'minimum' => 200, 'maximum' => 500],
                ['label' => 'GHS 500–1,000', 'minimum' => 500, 'maximum' => 1000],
                ['label' => 'GHS 1,000+', 'minimum' => 1000, 'maximum' => null],
            ],
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->with(['category', 'images', 'variants'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::query()
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $product->id)
            ->orderByDesc('is_featured')
            ->take(4)
            ->get();

        $cart = $this->carts->resolve();
        $inCart = $cart->items->contains('product_id', $product->id);

        return view('web.store.show', [
            'product' => $product,
            'related' => $related,
            'inCart' => $inCart,
            'whatsAppOrderUrl' => WhatsAppOrder::productUrl($product),
        ]);
    }

    private function priceFilterValue(mixed $value): ?float
    {
        if (! is_scalar($value) || $value === '' || ! is_numeric($value)) {
            return null;
        }

        $price = (float) $value;

        if (! is_finite($price) || $price < 0 || $price > self::MAX_PRICE_FILTER) {
            return null;
        }

        return round($price, 2);
    }
}
