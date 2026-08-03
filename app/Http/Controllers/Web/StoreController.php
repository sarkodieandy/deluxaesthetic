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
}
