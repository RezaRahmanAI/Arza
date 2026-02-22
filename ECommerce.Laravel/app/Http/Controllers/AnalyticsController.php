<?php

namespace App\Http\Controllers;

use App\Models\DailyTraffic;

class AnalyticsController extends Controller
{
    public function daily()
    {
        return response()->json(DailyTraffic::orderByDesc('date')->take(30)->get());
    }
}
