<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Orders\UpdateOrderRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.name as user_name')
            ->whereNull('orders.deleted_at')
            ->latest('orders.created_at')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function edit(int $order): View
    {
        $order = DB::table('orders')->join('users', 'users.id', '=', 'orders.user_id')->select('orders.*', 'users.name as user_name', 'users.email')->where('orders.id', $order)->whereNull('orders.deleted_at')->firstOrFail();
        $delivery = DB::table('deliveries')->where('order_id', $order->id)->first();
        $items = DB::table('order_items')->where('order_id', $order->id)->get();

        return view('admin.orders.edit', compact('order', 'delivery', 'items'));
    }

    public function update(UpdateOrderRequest $request, int $order): RedirectResponse
    {
        $data = $request->validated();
        $record = DB::table('orders')->where('id', $order)->whereNull('deleted_at')->firstOrFail();

        DB::transaction(function () use ($data, $order, $record, $request) {
            DB::table('orders')->where('id', $order)->update([
                'status' => $data['status'],
                'notes' => $data['notes'] ?: null,
                'updated_at' => now(),
            ]);

            if (DB::table('deliveries')->where('order_id', $order)->exists()) {
                DB::table('deliveries')->where('order_id', $order)->update([
                    'tracking_number' => $data['tracking_number'] ?: null,
                    'status' => $data['delivery_status'],
                    'updated_at' => now(),
                ]);
            }

            if ($record->status !== $data['status']) {
                DB::table('order_status_histories')->insert([
                    'order_id' => $record->id,
                    'from_status' => $record->status,
                    'to_status' => $data['status'],
                    'changed_by' => $request->user()?->id,
                    'notes' => $data['notes'] ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('admin.orders.edit', $order)->with('status', 'Order updated successfully.');
    }
}
