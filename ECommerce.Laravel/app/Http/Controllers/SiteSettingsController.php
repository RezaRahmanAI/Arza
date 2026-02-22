<?php

namespace App\Http\Controllers;

use App\Models\DeliveryMethod;
use App\Models\SiteSetting;

class SiteSettingsController extends Controller
{
    public function index()
    {
        return response()->json(SiteSetting::query()->first());
    }

    public function deliveryMethods()
    {
        return response()->json(DeliveryMethod::where('is_active', true)->get());
    }
}
