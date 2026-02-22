<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminBannersController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCustomersController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminNavigationController;
use App\Http\Controllers\Admin\AdminOrdersController;
use App\Http\Controllers\Admin\AdminPagesController;
use App\Http\Controllers\Admin\AdminProductsController;
use App\Http\Controllers\Admin\AdminReviewsController;
use App\Http\Controllers\Admin\AdminSecurityController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminSubCategoryController;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::get('categories', [CategoriesController::class, 'index']);
Route::get('categories/{slug}', [CategoriesController::class, 'show']);

Route::get('products', [ProductsController::class, 'index']);
Route::get('products/featured', [ProductsController::class, 'getFeaturedProducts']);
Route::get('products/new-arrivals', [ProductsController::class, 'getNewArrivals']);
Route::get('products/{slug}', [ProductsController::class, 'show']);

Route::get('banners', [BannersController::class, 'index']);
Route::get('navigation/mega-menu', [NavigationController::class, 'megaMenu']);
Route::get('analytics/daily', [AnalyticsController::class, 'daily']);
Route::get('siteSettings', [SiteSettingsController::class, 'index']);
Route::get('siteSettings/delivery-methods', [SiteSettingsController::class, 'deliveryMethods']);

Route::get('customers/lookup', [CustomersController::class, 'lookup']);
Route::post('customers/profile', [CustomersController::class, 'profile']);
Route::get('customers/orders', [CustomersController::class, 'orders']);

Route::post('orders', [OrdersController::class, 'store']);
Route::get('orders', [OrdersController::class, 'index']);

Route::get('reviews/products/{productId}', [ReviewsController::class, 'productReviews']);
Route::get('reviews/featured', [ReviewsController::class, 'featured']);
Route::post('reviews/products/{productId}', [ReviewsController::class, 'store']);

Route::prefix('admin')->group(function () {
    Route::get('dashboard/stats', [AdminDashboardController::class, 'stats']);
    Route::get('dashboard/orders/recent', [AdminDashboardController::class, 'recentOrders']);
    Route::get('dashboard/products/popular', [AdminDashboardController::class, 'popularProducts']);
    Route::get('dashboard/analytics/sales', [AdminDashboardController::class, 'salesAnalytics']);
    Route::get('dashboard/analytics/order-distribution', [AdminDashboardController::class, 'orderDistribution']);
    Route::get('dashboard/analytics/customer-growth', [AdminDashboardController::class, 'customerGrowth']);

    Route::get('analytics/sales', [AdminAnalyticsController::class, 'sales']);
    Route::get('analytics/orders/distribution', [AdminAnalyticsController::class, 'orderDistribution']);
    Route::get('analytics/customers/growth', [AdminAnalyticsController::class, 'customerGrowth']);
    Route::get('analytics/products/top', [AdminAnalyticsController::class, 'topProducts']);

    Route::apiResource('categories', AdminCategoryController::class)->except(['destroy']);
    Route::post('categories/{category}', [AdminCategoryController::class, 'update']);
    Route::post('categories/{category}/delete', [AdminCategoryController::class, 'destroy']);
    Route::post('categories/upload-image', [AdminCategoryController::class, 'uploadImage']);
    Route::post('categories/reorder', [AdminCategoryController::class, 'reorder']);

    Route::apiResource('subcategories', AdminSubCategoryController::class)->except(['destroy']);
    Route::post('subcategories/{id}', [AdminSubCategoryController::class, 'update']);
    Route::post('subcategories/{id}/delete', [AdminSubCategoryController::class, 'destroy']);
    Route::post('subcategories/upload-image', [AdminSubCategoryController::class, 'uploadImage']);

    Route::apiResource('products', AdminProductsController::class)->except(['destroy']);
    Route::post('products/{id}', [AdminProductsController::class, 'update']);
    Route::post('products/{id}/delete', [AdminProductsController::class, 'destroy']);
    Route::post('products/upload-media', [AdminProductsController::class, 'uploadImage']);
    Route::get('products/inventory', [AdminProductsController::class, 'inventory']);
    Route::post('products/inventory/{variantId}', [AdminProductsController::class, 'updateInventory']);

    Route::get('orders', [AdminOrdersController::class, 'index']);
    Route::get('orders/filtered', [AdminOrdersController::class, 'index']);
    Route::get('orders/{id}', [AdminOrdersController::class, 'show']);
    Route::post('orders/{id}/status', [AdminOrdersController::class, 'updateStatus']);
    Route::post('orders/{id}/send-to-steadfast', [AdminOrdersController::class, 'sendToSteadfast']);
    Route::post('orders/{id}/delete', [AdminOrdersController::class, 'destroy']);

    Route::get('customers', [AdminCustomersController::class, 'index']);
    Route::post('customers/{id}/flag', [AdminCustomersController::class, 'flag']);
    Route::post('customers/{id}/unflag', [AdminCustomersController::class, 'unflag']);

    Route::get('banners', [AdminBannersController::class, 'index']);
    Route::get('banners/{id}', [AdminBannersController::class, 'show']);
    Route::post('banners', [AdminBannersController::class, 'store']);
    Route::post('banners/{id}', [AdminBannersController::class, 'update']);
    Route::post('banners/{id}/delete', [AdminBannersController::class, 'destroy']);
    Route::post('banners/image', [AdminBannersController::class, 'image']);

    Route::get('navigation', [AdminNavigationController::class, 'index']);
    Route::get('navigation/{id}', [AdminNavigationController::class, 'show']);
    Route::post('navigation', [AdminNavigationController::class, 'store']);
    Route::post('navigation/{id}', [AdminNavigationController::class, 'update']);
    Route::post('navigation/{id}/delete', [AdminNavigationController::class, 'destroy']);

    Route::get('pages', [AdminPagesController::class, 'index']);
    Route::get('pages/{id}', [AdminPagesController::class, 'show']);
    Route::post('pages', [AdminPagesController::class, 'store']);
    Route::post('pages/{id}', [AdminPagesController::class, 'update']);
    Route::post('pages/{id}/delete', [AdminPagesController::class, 'destroy']);

    Route::get('blog/posts', [AdminBlogController::class, 'index']);
    Route::get('blog/posts/{id}', [AdminBlogController::class, 'show']);
    Route::post('blog/posts', [AdminBlogController::class, 'store']);
    Route::post('blog/posts/{id}', [AdminBlogController::class, 'update']);
    Route::post('blog/posts/{id}/delete', [AdminBlogController::class, 'destroy']);

    Route::get('reviews', [AdminReviewsController::class, 'index']);
    Route::post('reviews/{id}', [AdminReviewsController::class, 'update']);
    Route::post('reviews/{id}/delete', [AdminReviewsController::class, 'destroy']);

    Route::get('security/blocked-ips', [AdminSecurityController::class, 'index']);
    Route::post('security/block-ip', [AdminSecurityController::class, 'store']);
    Route::post('security/unblock-ip/{id}/delete', [AdminSecurityController::class, 'destroy']);

    Route::get('settings', [AdminSettingsController::class, 'show']);
    Route::post('settings', [AdminSettingsController::class, 'update']);
    Route::post('settings/media', [AdminSettingsController::class, 'media']);
    Route::get('settings/delivery-methods', [AdminSettingsController::class, 'deliveryMethods']);
    Route::post('settings/delivery-methods', [AdminSettingsController::class, 'storeDeliveryMethod']);
    Route::post('settings/delivery-methods/{id}', [AdminSettingsController::class, 'updateDeliveryMethod']);
    Route::post('settings/delivery-methods/{id}/delete', [AdminSettingsController::class, 'destroyDeliveryMethod']);
});
