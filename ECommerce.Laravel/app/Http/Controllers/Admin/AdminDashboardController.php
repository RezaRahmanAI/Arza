<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class AdminDashboardController extends Controller
{
    public function stats(){return response()->json(['orders'=>Order::count(),'products'=>Product::count(),'customers'=>Customer::count(),'revenue'=>Order::sum('total')]);}
    public function recentOrders(){return response()->json(Order::latest()->take(10)->get());}
    public function popularProducts(){return response()->json(Product::orderByDesc('stock_quantity')->take(10)->get());}
    public function salesAnalytics(){return response()->json(['revenue'=>Order::sum('total')]);}
    public function orderDistribution(){return response()->json(Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->get());}
    public function customerGrowth(){return response()->json(Customer::selectRaw('DATE(created_at) as date, COUNT(*) as total')->groupBy('date')->orderBy('date')->get());}
}
