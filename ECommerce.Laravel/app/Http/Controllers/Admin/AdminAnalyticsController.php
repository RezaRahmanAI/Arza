<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class AdminAnalyticsController extends Controller
{
    public function sales(){return response()->json(Order::selectRaw('DATE(created_at) as date, SUM(total) as total')->groupBy('date')->orderBy('date')->get());}
    public function orderDistribution(){return response()->json(Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->get());}
    public function customerGrowth(){return response()->json(Customer::selectRaw('DATE(created_at) as date, COUNT(*) as total')->groupBy('date')->orderBy('date')->get());}
    public function topProducts(){return response()->json(Product::orderByDesc('stock_quantity')->take(10)->get());}
}
