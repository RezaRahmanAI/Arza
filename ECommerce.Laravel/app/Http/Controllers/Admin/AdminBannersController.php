<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBannersController extends Controller
{
    public function index() { return response()->json(HeroBanner::orderBy('display_order')->get()); }
    public function show($id) { return response()->json(HeroBanner::findOrFail($id)); }
    public function store(Request $r) { return response()->json(HeroBanner::create($r->all()),201); }
    public function update(Request $r,$id) { $m=HeroBanner::findOrFail($id); $m->update($r->all()); return response()->json($m); }
    public function destroy($id) { HeroBanner::findOrFail($id)->delete(); return response()->json(null,204); }
    public function image(Request $r) { $r->validate(['file'=>'required|image|max:4096']); $p=$r->file('file')->store('uploads/banners','public'); return response()->json(['url'=>Storage::url($p)]); }
}
