<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_name',
        'logo_url',
        'contact_email',
        'contact_phone',
        'address',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'whatsapp_number',
        'facebook_pixel_id',
        'google_tag_id',
        'currency',
        'free_shipping_threshold',
        'shipping_charge',
    ];

    protected $casts = [
        'free_shipping_threshold' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
    ];
}
