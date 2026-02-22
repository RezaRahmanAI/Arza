<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'name',
        'address',
        'delivery_details',
        'is_suspicious',
    ];

    protected $casts = [
        'is_suspicious' => 'boolean',
    ];
}
