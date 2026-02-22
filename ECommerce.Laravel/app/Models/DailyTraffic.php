<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTraffic extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'page_views',
        'unique_visitors',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
