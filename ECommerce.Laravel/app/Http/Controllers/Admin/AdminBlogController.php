<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBlogController extends Controller
{
    public function index() { return response()->json(BlogPost::latest()->get()); }
    public function show($id) { return response()->json(BlogPost::findOrFail($id)); }
    public function store(Request $r) { $d=$r->all(); $d['slug']=$d['slug'] ?? Str::slug($d['title'] ?? 'post'); return response()->json(BlogPost::create($d),201); }
    public function update(Request $r,$id) { $m=BlogPost::findOrFail($id); $m->update($r->all()); return response()->json($m); }
    public function destroy($id) { BlogPost::findOrFail($id)->delete(); return response()->json(null,204); }
}
