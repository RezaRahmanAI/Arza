<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrdersController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->query('searchTerm');
        $status = $request->query('status');
        $dateRange = $request->query('dateRange');
        $page = $request->query('page', 1);
        $pageSize = $request->query('pageSize', 10);

        $query = Order::with(['items.product', 'deliveryMethod']);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                  ->orWhere('customer_name', 'like', "%{$searchTerm}%")
                  ->orWhere('customer_phone', 'like', "%{$searchTerm}%");
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Date range logic (simplified for broad matching)
        if ($dateRange && $dateRange !== 'all') {
             // Implementation depends on how frontend sends dateRange (e.g., 'today', 'last7days')
        }

        $total = $query->count();
        $orders = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return response()->json(['items' => $orders, 'total' => $total]);
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'deliveryMethod'])->find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status updated successfully']);
    }

    public function sendToSteadfast($id)
    {
        // Placeholder for Steadfast Courier integration logic
        // This usually involves calling an external API and updating consignment details
        return response()->json(['message' => 'Order submission to Steadfast is not yet implemented in Laravel'], 501);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $order->delete();
        return response()->json(null, 204);
    }
}
