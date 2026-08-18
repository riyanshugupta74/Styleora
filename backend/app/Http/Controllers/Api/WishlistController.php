<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $items = WishlistItem::with([
            'product.images',
            'product.brand'
        ])
            ->where('wishlist_id', $wishlist->id)
            ->get();

        $products = $items->map(fn($item) => $item->product)->filter()->values();

        return response()->json([
            'items' => $items,
            'products' => $products,
            'wishlistCount' => $items->count(),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add to wishlist.'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $item = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->delete();
            $action = 'removed';
            $message = 'Removed from wishlist';
        } else {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $request->product_id
            ]);
            $action = 'added';
            $message = 'Added to wishlist';
        }

        $wishlistCount = WishlistItem::where('wishlist_id', $wishlist->id)->count();

        return response()->json([
            'success' => true,
            'action' => $action,
            'message' => $message,
            'wishlistCount' => $wishlistCount
        ]);
    }
}
