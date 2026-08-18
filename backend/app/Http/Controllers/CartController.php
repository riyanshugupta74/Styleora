<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('shop.cart', compact('cart'));
    }

    /**
     * Normal add to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id'   => 'required|exists:colors,id',
            'size_id'    => 'required|exists:sizes,id',
            'quantity'   => 'nullable|integer|min:1|max:10',
        ]);

        $product = Product::with('images')->findOrFail($request->product_id);

        // Find exact variant using IDs
        $variant = ProductVariant::with(['color', 'size'])
            ->where('product_id', $product->id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->first();

        if (!$variant) {
            return redirect()->back()->with(
                'error',
                'Selected variant is not available.'
            );
        }

        if ($variant->stock <= 0) {
            return redirect()->back()->with(
                'error',
                'Sorry, this item is currently out of stock.'
            );
        }

        $cart = session()->get('cart', []);

        // Unique key for product + variant
        $cartKey = $product->id . '_' . $variant->id;

        $qtyToAdd = $request->quantity ?? 1;

        /*
        |--------------------------------------------------------------------------
        | Check existing quantity
        |--------------------------------------------------------------------------
        */
        if (isset($cart[$cartKey])) {

            $newQty = $cart[$cartKey]['quantity'] + $qtyToAdd;

            if ($newQty > $variant->stock) {
                return redirect()->back()->with(
                    'error',
                    "Only {$variant->stock} item(s) available in stock."
                );
            }

            $cart[$cartKey]['quantity'] = $newQty;

        } else {

            if ($qtyToAdd > $variant->stock) {
                return redirect()->back()->with(
                    'error',
                    "Only {$variant->stock} item(s) available in stock."
                );
            }

            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'name'       => $product->name,
                'color'      => $variant->color->name ?? '',
                'size'       => $variant->size->name ?? '',
                'sku'        => $variant->sku,
                'quantity'   => $qtyToAdd,
                'price'      => $variant->price
                    ?? $product->discount_price
                    ?? $product->price,
                'image'      => $variant->image
                    ?? ($product->images->first()->image_path ?? ''),
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with(
            'success',
            'Product added to bag successfully!'
        );
    }

    /**
     * AJAX Add to Cart
     */
    public function addToCartAjax(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::with(['images', 'variants.color', 'variants.size'])
            ->findOrFail($request->product_id);

        /*
        |--------------------------------------------------------------------------
        | Find exact variant
        |--------------------------------------------------------------------------
        */

        $variant = null;

        // If variant_id is provided, use it directly
        if ($request->filled('variant_id')) {

            $variant = ProductVariant::with(['color', 'size'])
                ->where('id', $request->variant_id)
                ->where('product_id', $product->id)
                ->first();

        } else {

            // Fallback: find first available variant
            $variant = ProductVariant::with(['color', 'size'])
                ->where('product_id', $product->id)
                ->where('stock', '>', 0)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Variant does not exist
        |--------------------------------------------------------------------------
        */

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'No available variant found for this product.',
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Check stock
        |--------------------------------------------------------------------------
        */

        if ($variant->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This variant is currently out of stock.',
            ], 400);
        }

        $cart = session()->get('cart', []);

        // Use ONE consistent cart key format
        $cartKey = $product->id . '_' . $variant->id;

        /*
        |--------------------------------------------------------------------------
        | Existing item
        |--------------------------------------------------------------------------
        */

        if (isset($cart[$cartKey])) {

            $newQuantity = $cart[$cartKey]['quantity'] + 1;

            if ($newQuantity > $variant->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$variant->stock} item(s) available in stock.",
                ], 400);
            }

            $cart[$cartKey]['quantity'] = $newQuantity;

        } else {

            /*
            |--------------------------------------------------------------------------
            | New item
            |--------------------------------------------------------------------------
            */

            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'name'       => $product->name,
                'quantity'   => 1,
                'price'      => $variant->price
                    ?? $product->discount_price
                    ?? $product->price,
                'image'      => $variant->image
                    ?? ($product->images
                        ->where('is_primary', true)
                        ->first()
                        ->image_path ?? ''),
                'color'      => $variant->color->name ?? '',
                'size'       => $variant->size->name ?? '',
                'sku'        => $variant->sku,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success'   => true,
            'message'   => 'Added to bag!',
            'cartCount' => array_sum(
                array_column($cart, 'quantity')
            ),
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        $id = $request->id;

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);

            session()->put('cart', $cart);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'cartCount' => array_sum(
                    array_column($cart, 'quantity')
                ),
            ]);
        }

        return redirect()->back()->with(
            'success',
            'Item removed from cart!'
        );
    }

    /**
     * Get cart count
     */
    public function getCartCount()
    {
        $cart = session()->get('cart', []);

        return response()->json([
            'count' => array_sum(
                array_column($cart, 'quantity')
            ),
        ]);
    }
}