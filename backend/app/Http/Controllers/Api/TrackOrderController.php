<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $order = null;
        $error = null;

        if ($request->has('order_number')) {
            $request->validate([
                'order_number' => 'required|string'
            ]);

            $order = Order::with(['items.product.images', 'items.variant', 'address'])
                ->where('order_number', $request->order_number)
                ->first();

            if (!$order) {
                $error = "Order not found. Please check your order number and try again.";
            } elseif (Auth::check() && $order->user_id !== Auth::id()) {
                $order = null;
                $error = "You do not have permission to view this order.";
            } elseif (!Auth::check()) {
                $order = null;
                $error = "Please log in to track your orders.";
            }
        }

        $timeline = null;
        if ($order) {
            $timeline = $order->timeline_status;
        }

        return response()->json([
            'order' => $order,
            'timeline' => $timeline,
            'error' => $error,
        ]);
    }
}
