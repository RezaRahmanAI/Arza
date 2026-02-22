<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMethod;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function show(){return response()->json(SiteSetting::first());}
    public function update(Request $r){$s=SiteSetting::first() ?? new SiteSetting();$s->fill($r->all());$s->save();return response()->json($s);}    
    public function media(Request $r){$r->validate(['file'=>'required|file|max:5120']);$p=$r->file('file')->store('uploads/settings','public');return response()->json(['url'=>Storage::url($p)]);}    
    public function deliveryMethods(){return response()->json(DeliveryMethod::all());}
    public function storeDeliveryMethod(Request $r){return response()->json(DeliveryMethod::create($r->all()),201);}    
    public function updateDeliveryMethod(Request $r,$id){$d=DeliveryMethod::findOrFail($id);$d->update($r->all());return response()->json($d);}    
    public function destroyDeliveryMethod($id){DeliveryMethod::findOrFail($id)->delete();return response()->json(null,204);}    
}
