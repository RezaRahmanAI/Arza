<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewsController extends Controller
{
    public function index(){return response()->json(Review::with('product')->latest()->paginate(50));}
    public function update(Request $r,$id){$m=Review::findOrFail($id);$m->update($r->only(['rating','comment','is_approved']));return response()->json($m);}    
    public function destroy($id){Review::findOrFail($id)->delete();return response()->json(null,204);}    
}
