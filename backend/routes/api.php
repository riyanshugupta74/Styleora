<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\TrackOrderController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for React SPA Frontend
|--------------------------------------------------------------------------
*/

// Public routes (no auth required)
Route::get('/home', [HomeController::class, 'index']);
Route::get('/category/{type}', [ShopController::class, 'category']);
Route::get('/products/{slug}', [ShopController::class, 'product']);
Route::get('/shop/product/{slug}', [ShopController::class, 'product']);
Route::get('/search', [ShopController::class, 'search']);
Route::get('/search/suggestions', [ShopController::class, 'suggestions']);

// Auth
Route::get('/user', [AuthController::class, 'user']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

// Cart (session-based, no auth required for viewing)
Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/update', [CartController::class, 'updateQuantity']);
Route::post('/cart/remove', [CartController::class, 'remove']);
Route::get('/cart/count', [CartController::class, 'count']);

// Track Order (public but with server-side auth check)
Route::get('/track-order', [TrackOrderController::class, 'index']);

// Wishlist (toggle has its own auth check)
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);

    // Checkout
    Route::get('/checkout/address', [CheckoutController::class, 'address']);
    Route::post('/checkout/address', [CheckoutController::class, 'processAddress']);
    Route::get('/checkout/payment', [CheckoutController::class, 'payment']);
    Route::post('/checkout/process', [CheckoutController::class, 'processOrder']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{item}/cancel', [OrderController::class, 'cancelItem']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});
