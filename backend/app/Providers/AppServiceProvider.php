<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Global header composer
        View::composer(
            'components.header',
            \App\View\Composers\GlobalComposer::class
        );

        // Preload wishlist product IDs once per request (eliminates N+1 in product cards)
        View::composer('*', function ($view) {
            static $loaded = false;
            if (!$loaded) {
                $loaded = true;
                $wishlistProductIds = [];
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $wishlist = \App\Models\Wishlist::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
                    if ($wishlist) {
                        $wishlistProductIds = \App\Models\WishlistItem::where('wishlist_id', $wishlist->id)
                            ->pluck('product_id')
                            ->toArray();
                    }
                }
                View::share('wishlistProductIds', $wishlistProductIds);
            }
        });
    }
}