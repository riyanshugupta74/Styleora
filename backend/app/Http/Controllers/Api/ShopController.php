<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    private function getProductsQuery(Request $request, $categoryName = null, $isSale = false, $isNewArrival = false, $isTrending = false)
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

        if ($isNewArrival) {
            $query->where('is_new_arrival', 1);
        }

        if ($isTrending) {
            $query->where('is_trending', 1);
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

    private function getFilterData($categoryName = null): array
    {
        $brandsQuery = Brand::whereHas('products', function ($q) use ($categoryName) {
            $q->where('status', 1);
            if ($categoryName) {
                $q->whereHas('category', function ($q2) use ($categoryName) {
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

    private function getWishlistProductIds(): array
    {
        if (!auth()->check()) {
            return [];
        }
        $wishlist = \App\Models\Wishlist::where('user_id', auth()->id())->first();
        if (!$wishlist) {
            return [];
        }
        return \App\Models\WishlistItem::where('wishlist_id', $wishlist->id)
            ->pluck('product_id')
            ->toArray();
    }

    public function category(Request $request, string $type): JsonResponse
    {
        $config = [
            'men' => ['category' => 'Men', 'title' => "Men's Fashion", 'perPage' => 16],
            'women' => ['category' => 'Women', 'title' => "Women's Fashion", 'perPage' => 16],
            'sale' => ['category' => null, 'title' => 'Big Fashion Sale', 'isSale' => true, 'perPage' => 16],
            'new-arrivals' => ['category' => null, 'title' => 'New Arrivals', 'isNewArrival' => true, 'perPage' => 16],
            'trending' => ['category' => null, 'title' => 'Trending Now', 'isTrending' => true, 'perPage' => 24],
        ];

        if (!isset($config[$type])) {
            return response()->json(['error' => 'Invalid category type'], 404);
        }

        $c = $config[$type];
        $categoryName = $c['category'] ?? null;

        $query = $this->getProductsQuery(
            $request,
            $categoryName,
            $c['isSale'] ?? false,
            $c['isNewArrival'] ?? false,
            $c['isTrending'] ?? false
        );

        if (($c['isNewArrival'] ?? false) && !$request->has('sort')) {
            $query->orderBy('created_at', 'desc');
        }
        if (($c['isTrending'] ?? false) && !$request->has('sort')) {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate($c['perPage'])->withQueryString();
        $filters = $this->getFilterData($categoryName);
        $wishlistProductIds = $this->getWishlistProductIds();

        return response()->json([
            'products' => $products,
            'filters' => $filters,
            'title' => $c['title'],
            'gender' => $type,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    public function product(string $slug): JsonResponse
    {
        $product = Product::with(['images', 'brand', 'category', 'subcategory', 'variants.color', 'variants.size'])
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

        $wishlistProductIds = $this->getWishlistProductIds();

        return response()->json([
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json(['products' => [], 'query' => '', 'mappedQuery' => '']);
        }

        $mappedQuery = $this->applyFuzzySearch($query);

        $products = Product::with(['images', 'brand', 'variants.color', 'variants.size'])
            ->where('status', 1)
            ->where(function ($q) use ($mappedQuery) {
                $q->where('name', 'like', "%{$mappedQuery}%")
                    ->orWhere('description', 'like', "%{$mappedQuery}%")
                    ->orWhereHas('category', function ($q) use ($mappedQuery) {
                        $q->where('name', 'like', "%{$mappedQuery}%");
                    })
                    ->orWhereHas('subcategory', function ($q) use ($mappedQuery) {
                        $q->where('name', 'like', "%{$mappedQuery}%");
                    })
                    ->orWhereHas('brand', function ($q) use ($mappedQuery) {
                        $q->where('name', 'like', "%{$mappedQuery}%");
                    });
            })
            ->paginate(12)
            ->withQueryString();

        $wishlistProductIds = $this->getWishlistProductIds();

        return response()->json([
            'products' => $products,
            'query' => $query,
            'mappedQuery' => $mappedQuery,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([]);
        }

        $mappedQuery = $this->applyFuzzySearch($query);

        $products = Product::with(['images', 'brand'])
            ->where('status', 1)
            ->where(function ($q) use ($mappedQuery) {
                $q->where('name', 'like', "%{$mappedQuery}%")
                    ->orWhereHas('category', function ($q) use ($mappedQuery) {
                        $q->where('name', 'like', "%{$mappedQuery}%");
                    })
                    ->orWhereHas('brand', function ($q) use ($mappedQuery) {
                        $q->where('name', 'like', "%{$mappedQuery}%");
                    });
            })
            ->take(5)
            ->get();

        $formatted = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'brand' => $product->brand ? $product->brand->name : '',
                'price' => number_format($product->price, 2),
                'discount_price' => $product->discount_price ? number_format($product->discount_price, 2) : null,
                'image' => $product->images->where('is_primary', 1)->first()
                    ? $product->images->where('is_primary', 1)->first()->image_path
                    : ($product->images->first() ? $product->images->first()->image_path : '/images/product-placeholder.jpg'),
            ];
        });

        return response()->json($formatted);
    }

    private function applyFuzzySearch($query): string
    {
        $query = strtolower(trim($query));

        $typoMap = [
            'shrit' => 'shirt', 'shrits' => 'shirt', 'shirts' => 'shirt',
            'shoe' => 'shoes', 'sho' => 'shoes',
            'saari' => 'saree', 'sari' => 'saree', 'sare' => 'saree',
            'tshirt' => 't-shirt', 'jean' => 'jeans', 'jenas' => 'jeans', 'jens' => 'jeans',
            'dresss' => 'dresses', 'pant' => 'trousers', 'pants' => 'trousers',
            'trouser' => 'trousers', 'watch' => 'watches', 'jaket' => 'jackets',
            'jacket' => 'jackets', 'hudi' => 'hoodies', 'hoody' => 'hoodies'
        ];

        return $typoMap[$query] ?? $query;
    }
}
