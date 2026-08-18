<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    private function getProductsQuery(Request $request, $categoryName = null, $isSale = false)
    {
        $query = Product::with(['images', 'brand', 'variants.color', 'variants.size'])
            ->where('status', 1);

        if ($categoryName) {
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        if ($isSale) {
            $query->whereNotNull('discount_price');
        }

        // Filters
        if ($request->has('brand') && !empty($request->brand)) {
            $brands = is_array($request->brand) ? $request->brand : explode(',', $request->brand);
            $query->whereIn('brand_id', $brands);
        }

        if ($request->has('category') && !empty($request->category)) {
            $categories = is_array($request->category) ? $request->category : explode(',', $request->category);
            $query->whereHas('subcategory', function ($q) use ($categories) {
                $q->whereIn('slug', $categories);
            });
        }

        if ($request->has('color') && !empty($request->color)) {
            $colors = is_array($request->color) ? $request->color : explode(',', $request->color);
            $query->whereHas('variants', function ($q) use ($colors) {
                $q->whereIn('color_id', $colors);
            });
        }

        if ($request->has('size') && !empty($request->size)) {
            $sizes = is_array($request->size) ? $request->size : explode(',', $request->size);
            $query->whereHas('variants', function ($q) use ($sizes) {
                $q->whereIn('size_id', $sizes);
            });
        }

        if ($request->has('price_min')) {
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [$request->price_min]);
        }
        if ($request->has('price_max')) {
            $query->whereRaw('COALESCE(discount_price, price) <= ?', [$request->price_max]);
        }

        // Sorting
        $sort = $request->get('sort', 'recommended');
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_low':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'discount':
                $query->orderByRaw('((price - COALESCE(discount_price, price)) / price) DESC');
                break;
            case 'recommended':
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    private function getFilterData($categoryName = null)
    {
        $brandsQuery = Brand::whereHas('products', function($q) use ($categoryName) {
            $q->where('status', 1);
            if ($categoryName) {
                $q->whereHas('category', function($q2) use ($categoryName) {
                    $q2->where('name', $categoryName);
                });
            }
        });

        $categoriesQuery = Category::with('subcategories')->where('status', 1);
        if ($categoryName) {
            $categoriesQuery->where('name', $categoryName);
        }

        return [
            'brands' => $brandsQuery->get(),
            'categories' => $categoriesQuery->get(),
            'colors' => Color::all(),
            'sizes' => Size::orderBy('display_order')->get()
        ];
    }

    public function men(Request $request)
    {
        $products = $this->getProductsQuery($request, 'Men')->paginate(16)->withQueryString();
        $filters = $this->getFilterData('Men');
        $title = "Men's Fashion";
        $gender = 'men';
        
        return view('shop.category', compact('products', 'filters', 'title', 'gender'));
    }

    public function women(Request $request)
    {
        $products = $this->getProductsQuery($request, 'Women')->paginate(16)->withQueryString();
        $filters = $this->getFilterData('Women');
        $title = "Women's Fashion";
        $gender = 'women';

        return view('shop.category', compact('products', 'filters', 'title', 'gender'));
    }

    public function sale(Request $request)
    {
        $products = $this->getProductsQuery($request, null, true)->paginate(16)->withQueryString();
        $filters = $this->getFilterData(null);
        $title = "Big Fashion Sale";
        $gender = 'sale';

        return view('shop.category', compact('products', 'filters', 'title', 'gender'));
    }

    public function product($slug)
    {
        $product = Product::with(['images', 'brand', 'category', 'variants.color', 'variants.size'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $relatedProducts = Product::with(['images', 'brand'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'relatedProducts'));
    }

    public function newArrivals(Request $request)
    {
        $query = $this->getProductsQuery($request);
        
        // Enforce new arrivals logic
        $query->where('is_new_arrival', 1);

        if (!$request->has('sort')) {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(16)->withQueryString();
        $filters = $this->getFilterData(null);
        $title = "New Arrivals";
        $gender = 'new-arrivals';
        
        return view('shop.category', compact('products', 'filters', 'title', 'gender'));
    }

    public function trending(Request $request)
    {
        $query = $this->getProductsQuery($request);
        
        // Enforce trending logic
        $query->where('is_trending', 1);

        // Sorting
        if (!$request->has('sort')) {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(24)->withQueryString();
        $filters = $this->getFilterData(null);
        $title = "Trending Now";
        $gender = 'trending';

        // Reusing the same category view for consistency
        return view('shop.category', compact('products', 'filters', 'title', 'gender'));
    }
}
