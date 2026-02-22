<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// Public Routes
Route::get('categories', [CategoriesController::class, 'index']);
Route::get('categories/{slug}', [CategoriesController::class, 'show']);

Route::get('products', [ProductsController::class, 'index']);
Route::get('products/{slug}', [ProductsController::class, 'show']);
Route::get('products/featured', [ProductsController::class, 'getFeaturedProducts']);
Route::get('products/new-arrivals', [ProductsController::class, 'getNewArrivals']);

// Admin Routes (To be protected with middleware)
Route::prefix('admin')->group(function () {
    // Categories
    Route::get('categories', [AdminCategoryController::class, 'index']);
    Route::post('categories', [AdminCategoryController::class, 'store']);
    Route::get('categories/{category}', [AdminCategoryController::class, 'show']);
    Route::put('categories/{category}', [AdminCategoryController::class, 'update']);
    Route::post('categories/{id}', [AdminCategoryController::class, 'update']); // ASP.NET used POST for update
    Route::post('categories/{id}/delete', [AdminCategoryController::class, 'destroy']);
    Route::post('categories/upload-image', [AdminCategoryController::class, 'uploadImage']);
    Route::post('categories/reorder', [AdminCategoryController::class, 'reorder']);

    // Products
    Route::get('products', [AdminProductsController::class, 'index']);
    Route::post('products', [AdminProductsController::class, 'store']);
    Route::get('products/{product}', [AdminProductsController::class, 'show']);
    Route::put('products/{product}', [AdminProductsController::class, 'update']);
    Route::post('products/{id}', [AdminProductsController::class, 'update']);
    Route::post('products/{id}/delete', [AdminProductsController::class, 'destroy']);
    Route::post('products/upload-image', [AdminProductsController::class, 'uploadImage']);

    // Orders
    Route::get('orders', [AdminOrdersController::class, 'index']);
    Route::get('orders/{order}', [AdminOrdersController::class, 'show']);
    Route::post('orders/{id}/status', [AdminOrdersController::class, 'updateStatus']);
    Route::post('orders/{id}/send-to-steadfast', [AdminOrdersController::class, 'sendToSteadfast']);
    Route::post('orders/{id}/delete', [AdminOrdersController::class, 'destroy']);
});
