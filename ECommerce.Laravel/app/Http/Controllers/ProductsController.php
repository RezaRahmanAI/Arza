<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $pageIndex = $request->query('pageIndex', 1);
        $pageSize = $request->query('pageSize', 12);
        $sort = $request->query('sort');
        $categoryId = $request->query('categoryId');
        $subCategoryId = $request->query('subCategoryId');
        $categorySlug = $request->query('categorySlug');
        $searchTerm = $request->query('searchTerm');
        $isFeatured = $request->query('isFeatured');
        $isNew = $request->query('isNew');

        $query = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true);

        if ($categoryId) $query->where('category_id', $categoryId);
        if ($subCategoryId) $query->where('sub_category_id', $subCategoryId);
        if ($categorySlug) {
            $query->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }
        if ($isFeatured) $query->where('is_featured', true);
        if ($isNew) $query->where('is_new', true);

        // Sorting
        switch ($sort) {
            case 'priceAsc': 
                $query->orderBy('price', 'asc');
                break;
            case 'priceDesc': 
                $query->orderBy('price', 'desc');
                break;
            default: 
                $query->orderByDesc('created_at');
                break;
        }

        $totalItems = $query->count();
        $products = $query->skip(($pageIndex - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return response()->json([
            'pageIndex' => (int)$pageIndex,
            'pageSize' => (int)$pageSize,
            'count' => $totalItems,
            'data' => $products
        ]);
    }

    public function show($slug)
    {
        $product = Product::with(['images', 'variants', 'category', 'subCategory'])
            ->where('slug', $slug)
            ->first();

        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        return response()->json($product);
    }

    public function getFeaturedProducts()
    {
        $products = Product::with('images')
            ->where('is_featured', true)
            ->where('is_active', true)
            ->take(8)
            ->get();
        return response()->json($products);
    }

    public function getNewArrivals()
    {
        $products = Product::with('images')
            ->where('is_new', true)
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(8)
            ->get();
        return response()->json($products);
    }
}
