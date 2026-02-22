<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class AdminCustomersController extends Controller
{
    public function index() { return response()->json(Customer::latest()->paginate(50)); }
    public function flag($id) { $c=Customer::findOrFail($id); $c->is_suspicious=true; $c->save(); return response()->json($c); }
    public function unflag($id) { $c=Customer::findOrFail($id); $c->is_suspicious=false; $c->save(); return response()->json($c); }
}
