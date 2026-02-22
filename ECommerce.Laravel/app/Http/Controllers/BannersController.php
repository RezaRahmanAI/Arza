<?php

namespace App\Http\Controllers;

use App\Models\HeroBanner;

class BannersController extends Controller
{
    public function index()
    {
        return response()->json(HeroBanner::where('is_active', true)->orderBy('display_order')->get());
    }
}
