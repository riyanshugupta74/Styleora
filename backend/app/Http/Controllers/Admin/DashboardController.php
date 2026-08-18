<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ReturnRequest;
use App\Models\ExchangeRequest;
use App\Models\Refund;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalSales = Order::whereNotIn('status', ['cancelled', 'return_requested', 'returned', 'refunded'])->sum('total');
        $todaySales = Order::whereDate('created_at', $today)
            ->whereNotIn('status', ['cancelled', 'return_requested', 'returned', 'refunded'])
            ->sum('total');
            
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('status', ['pending', 'confirmed'])->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        
        $returnRequests = ReturnRequest::where('status', 'pending')->count();
        $exchangeRequests = ExchangeRequest::where('status', 'pending')->count();
        $pendingRefunds = Refund::where('status', 'pending')->count();
        
        $totalCustomers = User::count();
        $totalProducts = Product::count();
        
        $lowStockProducts = \App\Models\ProductVariant::where('stock', '<', 10)->where('stock', '>', 0)->count();
        $outOfStockProducts = \App\Models\ProductVariant::where('stock', 0)->count();

        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSales', 'todaySales', 'totalOrders', 'pendingOrders',
            'deliveredOrders', 'cancelledOrders', 'returnRequests',
            'exchangeRequests', 'pendingRefunds', 'totalCustomers',
            'totalProducts', 'lowStockProducts', 'outOfStockProducts',
            'recentOrders'
        ));
    }
}
