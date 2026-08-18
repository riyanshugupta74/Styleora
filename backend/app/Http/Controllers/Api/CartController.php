<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $cart = session()->get('cart', []);

        return response()->json([
            'cart' => $cart,
            'cartCount' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            'quantity' => 'nullable|integer|min:1|max:10',
        ]);

        $product = Product::with(['images', 'variants.color', 'variants.size'])
            ->findOrFail($request->product_id);

        $variant = null;

        // If color_id and size_id provided, find by those
        if ($request->filled('color_id') && $request->filled('size_id')) {
            $variant = ProductVariant::with(['color', 'size'])
                ->where('product_id', $product->id)
                ->where('color_id', $request->color_id)
                ->where('size_id', $request->size_id)
                ->first();
        }
        // If variant_id provided, use directly
        elseif ($request->filled('variant_id')) {
            $variant = ProductVariant::with(['color', 'size'])
                ->where('id', $request->variant_id)
                ->where('product_id', $product->id)
                ->first();
        }
        // Fallback: first available variant
        else {
            $variant = ProductVariant::with(['color', 'size'])
                ->where('product_id', $product->id)
                ->where('stock', '>', 0)
                ->first();
        }

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'No available variant found for this product.',
            ], 400);
        }

        if ($variant->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This variant is currently out of stock.',
            ], 400);
        }

        $cart = session()->get('cart', []);
        $cartKey = $product->id . '_' . $variant->id;
        $qtyToAdd = $request->quantity ?? 1;

        if (isset($cart[$cartKey])) {
            $newQty = $cart[$cartKey]['quantity'] + $qtyToAdd;

            if ($newQty > $variant->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$variant->stock} item(s) available in stock.",
                ], 400);
            }

            $cart[$cartKey]['quantity'] = $newQty;
        } else {
            if ($qtyToAdd > $variant->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$variant->stock} item(s) available in stock.",
                ], 400);
            }

            $effectivePrice = $product->discount_price && $product->discount_price < $product->price
                ? ($variant->price ? round($variant->price * ($product->discount_price / $product->price)) : $product->discount_price)
                : ($variant->price ?? $product->price);

            if ($variant->discount_price && $variant->discount_price < $variant->price) {
                $effectivePrice = $variant->discount_price;
            }

            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'name' => $product->name,
                'quantity' => $qtyToAdd,
                'price' => (float)$effectivePrice,
                'original_price' => (float)($variant->price ?? $product->price),
                'image' => $variant->image ?? ($product->images->where('is_primary', true)->first()->image_path ?? ($product->images->first()->image_path ?? '')),
                'color' => $variant->color->name ?? '',
                'size' => $variant->size->name ?? '',
                'sku' => $variant->sku,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Added to bag!',
            'cartCount' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    public function updateQuantity(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = session()->get('cart', []);
        $id = $request->id;

        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.',
            ], 404);
        }

        // Verify stock
        $variant = ProductVariant::find($cart[$id]['variant_id']);
        if (!$variant || $variant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Only " . ($variant ? $variant->stock : 0) . " item(s) available in stock.",
            ], 400);
        }

        $cart[$id]['quantity'] = $request->quantity;
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cartCount' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $id = $request->id;
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cartCount' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    public function count(): JsonResponse
    {
        $cart = session()->get('cart', []);
        return response()->json([
            'count' => array_sum(array_column($cart, 'quantity')),
        ]);
    }
}
