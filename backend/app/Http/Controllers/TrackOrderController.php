<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackOrderController extends Controller
{
    /**
     * Show the track order form/page.
     */
    public function index(Request $request)
    {
    
        $order = null;
        $error = null;

        if ($request->has('order_number')) {
            $request->validate([
                'order_number' => 'required|string'
            ]);

            $order = Order::with(['items.product.images', 'items.variant'])
                ->where('order_number', $request->order_number)
                ->first();

            if (!$order) {
                $error = "Order not found. Please check your order number and try again.";
            } elseif (Auth::check() && $order->user_id !== Auth::id()) {
                // Tracking Security: If logged in, you can only track your own orders
                $order = null;
                $error = "You do not have permission to view this order.";
            } elseif (!Auth::check()) {
                 // If not logged in, maybe require email verification or just allow it if order number is known?
                 // The prompt states: "A customer must only be able to view orders belonging to their own account. Backend must verify: authenticated_user_id === order.user_id"
                 $order = null;
                 $error = "Please log in to track your orders.";
            }
        }

        return view('shop.track-order', compact('order', 'error'));
    }
}
