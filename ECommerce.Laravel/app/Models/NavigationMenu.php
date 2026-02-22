<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'display_order',
        'is_active',
        'is_mega_menu',
        'icon',
        'category_id',
        'parent_menu_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_mega_menu' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function parentMenu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'parent_menu_id');
    }

    public function childMenus(): HasMany
    {
        return $this->hasMany(NavigationMenu::class, 'parent_menu_id')->orderBy('display_order');
    }
}
