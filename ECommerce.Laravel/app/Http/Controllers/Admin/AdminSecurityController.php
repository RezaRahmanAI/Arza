<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use Illuminate\Http\Request;

class AdminSecurityController extends Controller
{
    public function index(){return response()->json(BlockedIp::latest('blocked_at')->get());}
    public function store(Request $r){$d=$r->validate(['ip_address'=>'required|ip','reason'=>'nullable|string']);$d['blocked_at']=now();return response()->json(BlockedIp::create($d),201);}    
    public function destroy($id){BlockedIp::findOrFail($id)->delete();return response()->json(null,204);}    
}
