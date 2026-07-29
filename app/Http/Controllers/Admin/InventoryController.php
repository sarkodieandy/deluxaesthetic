<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Orders\AdjustInventoryRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->with('category')->orderBy('name')->paginate(20);
        $movements = DB::table('inventory_movements')->latest('created_at')->limit(20)->get();

        return view('admin.inventory.index', compact('products', 'movements'));
    }

    public function adjust(AdjustInventoryRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $product, $request) {
            $newStock = max(0, $product->stock_quantity + (int) $data['quantity_change']);
            $product->update(['stock_quantity' => $newStock]);

            DB::table('inventory_movements')->insert([
                'product_id' => $product->id,
                'product_variant_id' => null,
                'quantity_change' => (int) $data['quantity_change'],
                'reason' => $data['reason'],
                'reference_type' => Product::class,
                'reference_id' => $product->id,
                'created_by' => $request->user()?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.inventory.index')->with('status', 'Inventory adjusted successfully.');
    }
}
