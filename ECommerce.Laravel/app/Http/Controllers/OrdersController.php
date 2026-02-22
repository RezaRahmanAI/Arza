<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            'shipping_address' => 'required|string',
            'delivery_method_id' => 'nullable|integer|exists:delivery_methods,id',
            'sub_total' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
            'total' => 'required|numeric',
            'items' => 'array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric',
            'items.*.total_price' => 'required|numeric',
        ]);

        $order = DB::transaction(function () use ($data) {
            $order = Order::create([
                ...$data,
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . rand(100, 999),
                'status' => 'pending',
            ]);
            foreach (($data['items'] ?? []) as $item) {
                OrderItem::create([...$item, 'order_id' => $order->id]);
            }
            return $order->load('items');
        });

        return response()->json($order, 201);
    }

    public function index(Request $request)
    {
        return response()->json(Order::where('customer_phone', $request->query('phone'))->orderByDesc('created_at')->get());
    }
}
