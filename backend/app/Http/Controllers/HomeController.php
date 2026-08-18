<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $highlightCats = Category::where('status', 1)->take(8)->get();
        
        $saleProducts = Product::with(['images', 'brand'])
            ->where('status', 1)
            ->whereNotNull('discount_price')
            ->whereColumn('discount_price', '<', 'price')
            ->inRandomOrder()
            ->take(5)
            ->get();
            
        $newProducts = Product::with(['images', 'brand'])
            ->where('status', 1)
            ->where('is_new_arrival', 1)
            ->latest()
            ->take(5)
            ->get();
            
        $trendingProducts = Product::with(['images', 'brand'])
            ->where('status', 1)
            ->where('is_trending', 1)
            ->inRandomOrder()
            ->take(5)
            ->get();
            
        return view('home', compact('highlightCats', 'saleProducts', 'newProducts', 'trendingProducts'));
    }
}
