<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
Route::get('/health', fn() => response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]));

// Admin Routes (Unified SSO)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'auth.admin'])->group(function () {
    Route::get('/', function() {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Orders (Migrated from earlier)
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{id}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancelOrder'])->name('orders.cancel');

    // Products
    Route::resource('/products', \App\Http\Controllers\Admin\ProductController::class)->except(['show', 'destroy']);
    Route::post('/products/{id}/toggle', [\App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle');

    // Categories
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{id}/toggle', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle');

    // Inventory
    Route::get('/inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{id}/adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Returns
    Route::get('/returns', [\App\Http\Controllers\Admin\ReturnController::class, 'index'])->name('returns.index');
    Route::post('/returns/{id}/status', [\App\Http\Controllers\Admin\ReturnController::class, 'updateStatus'])->name('returns.status');

    // Exchanges
    Route::get('/exchanges', [\App\Http\Controllers\Admin\ExchangeController::class, 'index'])->name('exchanges.index');
    Route::post('/exchanges/{id}/status', [\App\Http\Controllers\Admin\ExchangeController::class, 'updateStatus'])->name('exchanges.status');

    // Refunds
    Route::get('/refunds', [\App\Http\Controllers\Admin\RefundController::class, 'index'])->name('refunds.index');
    Route::post('/refunds/{id}/status', [\App\Http\Controllers\Admin\RefundController::class, 'updateStatus'])->name('refunds.status');

    // Customers
    Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/{id}/status', [\App\Http\Controllers\Admin\CustomerController::class, 'toggleStatus'])->name('customers.status');

    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/status', [\App\Http\Controllers\Admin\ReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('/reviews/{id}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Banners (Homepage Management)
    Route::get('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');
    Route::post('/banners/{id}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('banners.toggle');

    // Audit Logs (Super Admin Only)
    Route::middleware('role.admin:Super Admin')->group(function () {
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs');
    });
});

require __DIR__.'/auth.php';

// Serve React SPA bundle assets
Route::get('/assets/{file}', function ($file) {
    $path = public_path("spa/assets/{$file}");
    if (!file_exists($path)) {
        abort(404);
    }
    
    $mimeType = 'text/plain';
    if (str_ends_with($file, '.css')) {
        $mimeType = 'text/css; charset=utf-8';
    } elseif (str_ends_with($file, '.js')) {
        $mimeType = 'application/javascript; charset=utf-8';
    } elseif (str_ends_with($file, '.svg')) {
        $mimeType = 'image/svg+xml';
    } elseif (str_ends_with($file, '.png')) {
        $mimeType = 'image/png';
    } elseif (str_ends_with($file, '.jpg') || str_ends_with($file, '.jpeg')) {
        $mimeType = 'image/jpeg';
    } elseif (str_ends_with($file, '.woff2')) {
        $mimeType = 'font/woff2';
    }

    return response()->file($path, [
        'Content-Type' => $mimeType,
    ]);
})->where('file', '.*');

// Catch-all route for React SPA
Route::get('/{any?}', function () {
    $path = public_path('spa/index.html');
    if (!file_exists($path)) {
        abort(404, 'React SPA build not found at public/spa/index.html');
    }
    return file_get_contents($path);
})->where('any', '^(?!admin|api|sanctum|assets).*$')->name('home');
