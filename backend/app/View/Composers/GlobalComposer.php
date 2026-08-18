<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\WishlistItem;

class GlobalComposer
{
    public function compose(View $view)
    {
        $wishlistCount = 0;

        if (Auth::check()) {
            $wishlist = \App\Models\Wishlist::where('user_id', Auth::id())->first();
            if ($wishlist) {
                $wishlistCount = \App\Models\WishlistItem::where('wishlist_id', $wishlist->id)->count();
            }
        }

        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        $view->with([
            'wishlistCount' => $wishlistCount,
            'cartCount' => $cartCount
        ]);
    }
}