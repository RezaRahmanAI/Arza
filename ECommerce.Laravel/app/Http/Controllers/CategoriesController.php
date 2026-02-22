<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::with(['subCategories.collections'])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'imageUrl' => $category->image_url,
                    'displayOrder' => $category->display_order,
                    'parentId' => $category->parent_id,
                    'subCategories' => $category->subCategories->filter(fn($sc) => $sc->is_active)->map(function ($sc) {
                        return [
                            'id' => $sc->id,
                            'name' => $sc->name,
                            'slug' => $sc->slug,
                            'categoryId' => $sc->category_id,
                            'collections' => $sc->collections->filter(fn($col) => $col->is_active)->map(function ($col) {
                                return [
                                    'id' => $col->id,
                                    'name' => $col->name,
                                    'slug' => $col->slug,
                                    'subCategoryId' => $col->sub_category_id,
                                ];
                            })
                        ];
                    })
                ];
            });

        return response()->json($categories);
    }

    public function show($slug)
    {
        $category = Category::with(['subCategories', 'products' => function($q) {
            $q->where('is_active', true)->take(20);
        }])->where('slug', $slug)->first();

        if (!$category) return response()->json(['message' => 'Category not found'], 404);

        return response()->json($category);
    }
}
