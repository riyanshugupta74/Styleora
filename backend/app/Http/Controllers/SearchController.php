<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->route('home');
        }

        $productsQuery = Product::with(['images', 'brand', 'variants.color', 'variants.size'])
            ->where('status', 1);

        $mappedQuery = $this->applyFuzzySearch($query);

        $products = $productsQuery->where(function($q) use ($mappedQuery) {
                $q->where('name', 'like', "%{$mappedQuery}%")
                  ->orWhere('description', 'like', "%{$mappedQuery}%")
                  ->orWhereHas('category', function($q) use ($mappedQuery) {
                      $q->where('name', 'like', "%{$mappedQuery}%");
                  })
                  ->orWhereHas('subcategory', function($q) use ($mappedQuery) {
                      $q->where('name', 'like', "%{$mappedQuery}%");
                  })
                  ->orWhereHas('brand', function($q) use ($mappedQuery) {
                      $q->where('name', 'like', "%{$mappedQuery}%");
                  });
            })
            ->paginate(12)
            ->withQueryString();

        return view('shop.search', compact('products', 'query', 'mappedQuery'));
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([]);
        }

        $mappedQuery = $this->applyFuzzySearch($query);

        $products = Product::with(['images', 'brand'])
            ->where('status', 1)
            ->where(function($q) use ($mappedQuery) {
                $q->where('name', 'like', "%{$mappedQuery}%")
                  ->orWhereHas('category', function($q) use ($mappedQuery) {
                      $q->where('name', 'like', "%{$mappedQuery}%");
                  })
                  ->orWhereHas('brand', function($q) use ($mappedQuery) {
                      $q->where('name', 'like', "%{$mappedQuery}%");
                  });
            })
            ->take(5)
            ->get();

        $formattedProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'brand' => $product->brand ? $product->brand->name : '',
                'price' => number_format($product->price, 2),
                'discount_price' => $product->discount_price ? number_format($product->discount_price, 2) : null,
                'image' => $product->images->where('is_primary', 1)->first() ? $product->images->where('is_primary', 1)->first()->image_path : ($product->images->first() ? $product->images->first()->image_path : '/images/placeholder.jpg'),
                'url' => route('product.show', $product->slug)
            ];
        });

        return response()->json($formattedProducts);
    }

    private function applyFuzzySearch($query)
    {
        $query = strtolower(trim($query));
        
        $typoMap = [
            'shrit' => 'shirt',
            'shrits' => 'shirt',
            'shirts' => 'shirt',
            'shoe' => 'shoes',
            'sho' => 'shoes',
            'saari' => 'saree',
            'sari' => 'saree',
            'sare' => 'saree',
            'tshirt' => 't-shirt',
            'jean' => 'jeans',
            'jenas' => 'jeans',
            'jens' => 'jeans',
            'dresss' => 'dresses',
            'pant' => 'trousers',
            'pants' => 'trousers',
            'trouser' => 'trousers',
            'watch' => 'watches',
            'jaket' => 'jackets',
            'jacket' => 'jackets',
            'hudi' => 'hoodies',
            'hoody' => 'hoodies'
        ];

        return $typoMap[$query] ?? $query;
    }
}
