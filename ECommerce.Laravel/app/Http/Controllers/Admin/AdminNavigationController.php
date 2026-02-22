<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationMenu;
use Illuminate\Http\Request;

class AdminNavigationController extends Controller
{
    public function index(){return response()->json(NavigationMenu::with('childMenus')->orderBy('display_order')->get());}
    public function show($id){return response()->json(NavigationMenu::findOrFail($id));}
    public function store(Request $r){return response()->json(NavigationMenu::create($r->all()),201);}
    public function update(Request $r,$id){$m=NavigationMenu::findOrFail($id);$m->update($r->all());return response()->json($m);}    
    public function destroy($id){NavigationMenu::findOrFail($id)->delete();return response()->json(null,204);}    
}
