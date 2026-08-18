<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        $highlightCats = \App\Models\Subcategory::where('status', 1)
            ->get()
            ->unique('name')
            ->values();

        $saleProducts = Product::with(['images', 'brand'])
            ->where('status', 1)
            ->whereNotNull('discount_price')
            ->whereColumn('discount_price', '<', 'price')
            ->inRandomOrder()
            ->take(5)
            ->get();

        $newProducts = Product::with(['images', 'brand'])
            ->where('status', 1)
            ->where('is_new_arrival', 1)
            ->latest()
            ->take(5)
            ->get();

        $trendingProducts = Product::with(['images', 'brand'])
            ->where('status', 1)
            ->where('is_trending', 1)
            ->inRandomOrder()
            ->take(5)
            ->get();

        $wishlistProductIds = $this->getWishlistProductIds();

        return response()->json([
            'highlightCats' => $highlightCats,
            'saleProducts' => $saleProducts,
            'newProducts' => $newProducts,
            'trendingProducts' => $trendingProducts,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    private function getWishlistProductIds(): array
    {
        if (!auth()->check()) {
            return [];
        }
        $wishlist = \App\Models\Wishlist::where('user_id', auth()->id())->first();
        if (!$wishlist) {
            return [];
        }
        return \App\Models\WishlistItem::where('wishlist_id', $wishlist->id)
            ->pluck('product_id')
            ->toArray();
    }
}
