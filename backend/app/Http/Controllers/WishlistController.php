<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // =====================================================
    // WISHLIST PAGE
    // =====================================================

    public function index()
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

        // Get real wishlist count
        $wishlistCount = $items->count();

        return view(
            'user.wishlist',
            compact('items', 'wishlistCount')
        );
    }


    // =====================================================
    // ADD TO WISHLIST
    // =====================================================

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        WishlistItem::firstOrCreate([
            'wishlist_id' => $wishlist->id,
            'product_id' => $request->product_id
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Product added to wishlist successfully!'
            );
    }


    // =====================================================
    // REMOVE FROM WISHLIST
    // =====================================================

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $wishlist = Wishlist::where(
            'user_id',
            Auth::id()
        )->first();

        if ($wishlist) {

            WishlistItem::where(
                'wishlist_id',
                $wishlist->id
            )
            ->where(
                'product_id',
                $request->product_id
            )
            ->delete();
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Product removed from wishlist successfully!'
            );
    }


    // =====================================================
    // AJAX WISHLIST TOGGLE
    // =====================================================

    public function toggleWishlistAjax(Request $request)
    {
        // -------------------------------------------------
        // CHECK LOGIN
        // -------------------------------------------------

        if (!Auth::check()) {

            return response()->json([
                'success' => false,
                'message' => 'Please login to add to wishlist.'
            ], 401);
        }


        // -------------------------------------------------
        // VALIDATE PRODUCT
        // -------------------------------------------------

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);


        // -------------------------------------------------
        // GET / CREATE USER WISHLIST
        // -------------------------------------------------

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id()
        ]);


        // -------------------------------------------------
        // FIND EXISTING ITEM
        // -------------------------------------------------

        $item = WishlistItem::where(
            'wishlist_id',
            $wishlist->id
        )
        ->where(
            'product_id',
            $request->product_id
        )
        ->first();


        // -------------------------------------------------
        // REMOVE
        // -------------------------------------------------

        if ($item) {

            $item->delete();

            $action = 'removed';

            $message = 'Removed from wishlist';

        }

        // -------------------------------------------------
        // ADD
        // -------------------------------------------------

        else {

            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $request->product_id
            ]);

            $action = 'added';

            $message = 'Added to wishlist';
        }


        // -------------------------------------------------
        // IMPORTANT:
        // GET FRESH COUNT AFTER ADD / DELETE
        // -------------------------------------------------

        $wishlistCount = WishlistItem::where(
            'wishlist_id',
            $wishlist->id
        )->count();


        // -------------------------------------------------
        // RETURN RESPONSE
        // -------------------------------------------------

        return response()->json([

            'success' => true,

            'action' => $action,

            'message' => $message,

            'wishlistCount' => $wishlistCount

        ]);
    }
}