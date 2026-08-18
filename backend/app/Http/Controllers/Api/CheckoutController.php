<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function address(): JsonResponse
    {
        $cart = session()->get('cart', []);
        if (count($cart) == 0) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        $addresses = Address::where('user_id', Auth::id())->get();

        return response()->json([
            'addresses' => $addresses,
        ]);
    }

    public function processAddress(Request $request): JsonResponse
    {
        $request->validate([
            'address_id' => 'nullable|exists:addresses,id',
            'full_name' => 'required_without:address_id|string|max:255',
            'phone' => 'required_without:address_id|string|max:20',
            'address_line_1' => 'required_without:address_id|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required_without:address_id|string|max:100',
            'state' => 'required_without:address_id|string|max:100',
            'pincode' => 'required_without:address_id|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        if ($request->address_id) {
            $address = Address::where('id', $request->address_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        } else {
            $address = Address::create([
                'user_id' => Auth::id(),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'country' => $request->country ?? 'India',
                'is_default' => true
            ]);
        }

        session()->put('checkout_address_id', $address->id);

        return response()->json([
            'success' => true,
            'address_id' => $address->id,
        ]);
    }

    public function payment(): JsonResponse
    {
        if (!session()->has('checkout_address_id')) {
            return response()->json(['error' => 'Please select an address first.'], 400);
        }

        $cart = session()->get('cart', []);
        if (count($cart) == 0) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        // Final stock check before payment render
        foreach ($cart as $key => $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant || $variant->stock < $item['quantity']) {
                return response()->json(['error' => 'Some items in your cart are out of stock. Please review.'], 400);
            }
        }

        $total = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return response()->json([
            'total' => $total,
            'cart' => $cart,
        ]);
    }

    public function processOrder(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|in:cod,card,upi'
        ]);

        $cart = session()->get('cart', []);
        if (count($cart) == 0) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        if (!session()->has('checkout_address_id')) {
            return response()->json(['error' => 'Address is missing.'], 400);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $lockedVariants = [];

            // Verify stock and calculate subtotal
            foreach ($cart as $key => $item) {
                $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                if (!$variant || $variant->stock < $item['quantity']) {
                    throw new \Exception("Product {$item['name']} is out of stock.");
                }
                $subtotal += $item['price'] * $item['quantity'];
                $lockedVariants[$item['variant_id']] = $variant;
            }

            // Create Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'address_id' => session()->get('checkout_address_id'),
                'subtotal' => $subtotal,
                'discount' => 0,
                'shipping' => 0,
                'total' => $subtotal,
                'status' => 'confirmed',
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'completed',
            ]);

            // Create Order Items & Decrement Stock
            foreach ($cart as $key => $item) {
                $variant = $lockedVariants[$item['variant_id']];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                    'product_name_snapshot' => $item['name'],
                    'variant_sku_snapshot' => $item['sku'],
                    'color_snapshot' => $item['color'],
                    'size_snapshot' => $item['size'],
                    'image_snapshot' => $item['image'],
                    'status' => 'confirmed'
                ]);

                $variant->decrement('stock', $item['quantity']);
            }

            DB::commit();

            session()->forget(['cart', 'checkout_address_id']);

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
