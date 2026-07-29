<?php

namespace App\Services\Inventory;

use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    public function decrementForOrder(Order $order, ?int $actorId = null): void
    {
        DB::transaction(function () use ($order, $actorId) {
            foreach ($order->items as $item) {
                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if (! $product) {
                    throw new InvalidArgumentException('Product missing for order item.');
                }

                if ($product->stock_quantity < $item->quantity) {
                    throw new InvalidArgumentException($product->name.' does not have enough stock.');
                }

                $previous = $product->stock_quantity;
                $product->update(['stock_quantity' => $previous - $item->quantity]);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity_change' => -1 * $item->quantity,
                    'reason' => 'sale',
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'created_by' => $actorId,
                ]);
            }
        });
    }
}
