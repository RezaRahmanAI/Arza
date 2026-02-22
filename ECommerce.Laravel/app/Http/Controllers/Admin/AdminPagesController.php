<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPagesController extends Controller
{
    public function index(){return response()->json(Page::orderByDesc('updated_at')->get());}
    public function show($id){return response()->json(Page::findOrFail($id));}
    public function store(Request $r){$d=$r->all();$d['slug']=$d['slug'] ?? Str::slug($d['title'] ?? 'page');return response()->json(Page::create($d),201);}    
    public function update(Request $r,$id){$m=Page::findOrFail($id);$m->update($r->all());return response()->json($m);}    
    public function destroy($id){Page::findOrFail($id)->delete();return response()->json(null,204);}    
}
