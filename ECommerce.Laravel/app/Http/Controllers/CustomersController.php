<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function lookup(Request $request)
    {
        $phone = $request->query('phone');
        return response()->json(Customer::where('phone', $phone)->first());
    }

    public function profile(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|max:30',
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'delivery_details' => 'nullable|string',
        ]);

        $customer = Customer::updateOrCreate(['phone' => $data['phone']], $data);
        return response()->json($customer);
    }

    public function orders(Request $request)
    {
        $phone = $request->query('phone');
        return response()->json(Order::where('customer_phone', $phone)->orderByDesc('created_at')->get());
    }
}
