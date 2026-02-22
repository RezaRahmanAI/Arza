<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductsController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->query('searchTerm');
        $categoryName = $request->query('category');
        $statusTab = $request->query('statusTab');
        $page = $request->query('page', 1);
        $pageSize = $request->query('pageSize', 10);

        $query = Product::with(['category', 'images']);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        if ($categoryName && $categoryName !== 'all') {
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        if ($statusTab && $statusTab !== 'all') {
            $isActive = strtolower($statusTab) === 'active';
            $query->where('is_active', $isActive);
        }

        $total = $query->count();
        $products = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'sku' => $p->sku,
                    'price' => $p->price,
                    'salePrice' => $p->compare_at_price,
                    'purchaseRate' => $p->purchase_rate,
                    'stockQuantity' => $p->stock_quantity,
                    'isNew' => (bool)$p->is_new,
                    'isFeatured' => (bool)$p->is_featured,
                    'status' => $p->is_active ? 'Active' : 'Draft',
                    'imageUrl' => $p->image_url,
                    'category' => $p->category->name ?? null,
                    'categoryId' => $p->category_id,
                    'mediaUrls' => $p->images->pluck('url'),
                    'createdAt' => $p->created_at,
                    'slug' => $p->slug
                ];
            });

        return response()->json(['items' => $products, 'total' => $total]);
    }

    public function show($id)
    {
        $product = Product::with(['images', 'variants', 'category', 'subCategory'])->find($id);
        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        // Map to match DTO structure if needed
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'short_description' => $data['shortDescription'] ?? null,
                'sku' => $data['sku'],
                'image_url' => $data['imageUrl'] ?? null,
                'price' => $data['price'],
                'compare_at_price' => $data['salePrice'] ?? null,
                'purchase_rate' => $data['purchaseRate'] ?? null,
                'stock_quantity' => $data['stockQuantity'] ?? 0,
                'is_active' => $data['isActive'] ?? true,
                'is_new' => $data['isNew'] ?? false,
                'is_featured' => $data['isFeatured'] ?? false,
                'category_id' => $data['categoryId'],
                'sub_category_id' => $data['subCategoryId'] ?? null,
                'tier' => $data['tier'] ?? null,
                'tags' => $data['tags'] ?? null,
            ]);

            // Handle Images
            if (isset($data['mediaUrls']) && is_array($data['mediaUrls'])) {
                foreach ($data['mediaUrls'] as $index => $url) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $url,
                        'is_main' => ($url === $product->image_url),
                        'display_order' => $index
                    ]);
                }
            }

            // Handle Variants
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $v) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $v['sku'] ?? null,
                        'size' => $v['size'] ?? null,
                        'price' => $v['price'] ?? null,
                        'stock_quantity' => $v['stockQuantity'] ?? 0,
                        'is_active' => true
                    ]);
                }
            }

            return response()->json($product, 210);
        });
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        $data = $request->all();

        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'short_description' => $data['shortDescription'] ?? null,
                'sku' => $data['sku'],
                'image_url' => $data['imageUrl'] ?? null,
                'price' => $data['price'],
                'compare_at_price' => $data['salePrice'] ?? null,
                'purchase_rate' => $data['purchaseRate'] ?? null,
                'stock_quantity' => $data['stockQuantity'] ?? 0,
                'is_active' => $data['isActive'] ?? true,
                'is_new' => $data['isNew'] ?? false,
                'is_featured' => $data['isFeatured'] ?? false,
                'category_id' => $data['categoryId'],
                'sub_category_id' => $data['subCategoryId'] ?? null,
                'tier' => $data['tier'] ?? null,
                'tags' => $data['tags'] ?? null,
            ]);

            // Sync Images
            if (isset($data['mediaUrls']) && is_array($data['mediaUrls'])) {
                $product->images()->delete();
                foreach ($data['mediaUrls'] as $index => $url) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $url,
                        'is_main' => ($url === $product->image_url),
                        'display_order' => $index
                    ]);
                }
            }

            // Sync Variants
            if (isset($data['variants']) && is_array($data['variants'])) {
                $product->variants()->delete();
                foreach ($data['variants'] as $v) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $v['sku'] ?? null,
                        'size' => $v['size'] ?? null,
                        'price' => $v['price'] ?? null,
                        'stock_quantity' => $v['stockQuantity'] ?? 0,
                        'is_active' => true
                    ]);
                }
            }

            return response()->json($product);
        });
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        $product->delete(); // Cascading delete should handle images and variants
        return response()->json(null, 204);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('files')) {
            $urls = [];
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads/products', 'public');
                $urls[] = Storage::url($path);
            }
            return response()->json($urls);
        }

        return response()->json(['message' => 'No files uploaded'], 400);
    }


    public function inventory(Request $request)
    {
        $variants = ProductVariant::with('product')->orderByDesc('updated_at')->paginate($request->query('pageSize', 50));
        return response()->json($variants);
    }

    public function updateInventory(Request $request, $variantId)
    {
        $variant = ProductVariant::find($variantId);
        if (!$variant) return response()->json(['message' => 'Variant not found'], 404);

        $variant->stock_quantity = $request->input('stockQuantity', $variant->stock_quantity);
        $variant->price = $request->input('price', $variant->price);
        $variant->save();

        return response()->json($variant);
    }

}
