<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['subCategories.collections'])
            ->withCount('products')
            ->orderBy('display_order')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'imageUrl' => $category->image_url,
                    'isActive' => (bool)$category->is_active,
                    'displayOrder' => $category->display_order,
                    'productCount' => $category->products_count,
                    'parentId' => $category->parent_id,
                    'createdAt' => $category->created_at,
                    'subCategories' => $category->subCategories->map(function ($sc) {
                        return [
                            'id' => $sc->id,
                            'name' => $sc->name,
                            'slug' => $sc->slug,
                            'categoryId' => $sc->category_id,
                            'isActive' => (bool)$sc->is_active,
                            'collections' => $sc->collections->map(function ($col) {
                                return [
                                    'id' => $col->id,
                                    'name' => $col->name,
                                    'slug' => $col->slug,
                                    'subCategoryId' => $col->sub_category_id,
                                    'isActive' => (bool)$col->is_active
                                ];
                            })
                        ];
                    })
                ];
            });

        return response()->json($categories);
    }

    public function show(Category $category)
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'imageUrl' => $category->image_url,
            'productCount' => $category->products()->count(),
            'parentId' => $category->parent_id,
            'createdAt' => $category->created_at
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $data = $request->all();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $data['isActive'] ?? true;
        $data['display_order'] = $data['displayOrder'] ?? 0;
        $data['image_url'] = $data['imageUrl'] ?? null;
        $data['parent_id'] = $data['parentId'] ?? null;

        $category = Category::create($data);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'imageUrl' => $category->image_url,
            'isActive' => (bool)$category->is_active,
            'displayOrder' => $category->display_order,
            'productCount' => 0,
            'createdAt' => $category->created_at
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id|different:id',
        ]);

        $data = $request->all();
        $category->name = $data['name'];
        $category->slug = $data['slug'] ?? Str::slug($data['name']);
        $category->is_active = $data['isActive'] ?? $category->is_active;
        $category->display_order = $data['displayOrder'] ?? $category->display_order;
        $category->parent_id = $data['parentId'] ?? null;

        if (isset($data['imageUrl']) && $data['imageUrl'] !== $category->image_url) {
            // In a real app, delete the old image from storage if necessary
            $category->image_url = $data['imageUrl'];
        }

        $category->save();

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'imageUrl' => $category->image_url,
            'isActive' => (bool)$category->is_active,
            'displayOrder' => $category->display_order,
            'productCount' => $category->products()->count(),
            'createdAt' => $category->created_at
        ]);
    }

    public function destroy(Category $category)
    {
        // In a real app, delete the associated image from storage
        $category->delete();
        return response()->json(null, 204);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048', // 2MB limit
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads/categories', 'public');
            return response()->json(['url' => Storage::url($path)]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orderedIds' => 'required|array',
        ]);

        foreach ($request->orderedIds as $index => $id) {
            Category::where('id', $id)->update(['display_order' => $index + 1]);
        }

        return response()->json(true);
    }
}
