<?php

namespace App\Http\Controllers;

use App\Models\NavigationMenu;

class NavigationController extends Controller
{
    public function megaMenu()
    {
        $menus = NavigationMenu::with('childMenus')->where('is_active', true)->whereNull('parent_menu_id')->orderBy('display_order')->get();
        return response()->json($menus);
    }
}
