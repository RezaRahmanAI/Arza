<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSubCategoryController extends Controller
{
    public function index() { return response()->json(SubCategory::orderBy('display_order')->get()); }
    public function show($id) { return response()->json(SubCategory::findOrFail($id)); }
    public function store(Request $r) { $d=$r->validate(['name'=>'required|string|max:255','category_id'=>'required|exists:categories,id']); $d['slug']=$r->slug ?? Str::slug($d['name']); return response()->json(SubCategory::create($d),201);}    
    public function update(Request $r,$id) { $s=SubCategory::findOrFail($id); $s->update($r->all()); return response()->json($s); }
    public function destroy($id) { SubCategory::findOrFail($id)->delete(); return response()->json(null,204);}    
    public function uploadImage(Request $r) { $r->validate(['file'=>'required|image|max:4096']); $path=$r->file('file')->store('uploads/subcategories','public'); return response()->json(['url'=>Storage::url($path)]); }
}
