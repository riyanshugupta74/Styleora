@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Overview</h1>
        <p class="text-gray-500 text-sm mt-1">Here's what's happening with your store today.</p>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-medium">Total Revenue</h3>
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">₹{{ number_format($totalSales) }}</p>
            <p class="text-xs text-gray-500 mt-2">Today: <span class="font-bold text-gray-900">₹{{ number_format($todaySales) }}</span></p>
        </div>

        <div class="bg-white rounded-lg border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-medium">Total Orders</h3>
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
            <p class="text-xs text-gray-500 mt-2">Pending: <span class="font-bold text-orange-600">{{ number_format($pendingOrders) }}</span></p>
        </div>

        <div class="bg-white rounded-lg border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-medium">Total Customers</h3>
                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCustomers) }}</p>
            <p class="text-xs text-green-600 mt-2 font-medium"><i class="fa-solid fa-arrow-trend-up"></i> Active community</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-medium">Active Products</h3>
                <div class="w-10 h-10 rounded-full bg-pink-50 flex items-center justify-center text-[#ff3f6c]">
                    <i class="fa-solid fa-tags"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
            <p class="text-xs text-gray-500 mt-2">Low Stock: <span class="font-bold text-red-600">{{ $lowStockProducts }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 shadow-sm">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900 uppercase tracking-wider">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-[#ff3f6c] hover:text-[#e02e5c]">View All</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4">Order ID</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[#ff3f6c] font-bold hover:underline">#{{ $order->order_number }}</a>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'packed' => 'bg-indigo-100 text-indigo-800',
                                            'shipped' => 'bg-purple-100 text-purple-800',
                                            'out_for_delivery' => 'bg-orange-100 text-orange-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $color = $colors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $color }}">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                                    ₹{{ number_format($order->total) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Attention Needed -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm flex flex-col h-full">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900 uppercase tracking-wider">Attention Needed</h2>
            </div>
            
            <div class="p-6 flex-1 space-y-4">
                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                            <i class="fa-solid fa-rotate-left"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Return Requests</p>
                            <p class="text-xs text-gray-600 mt-0.5">Awaiting approval</p>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-orange-600">{{ $returnRequests }}</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-right-left"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Exchange Requests</p>
                            <p class="text-xs text-gray-600 mt-0.5">Awaiting approval</p>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-blue-600">{{ $exchangeRequests }}</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Pending Refunds</p>
                            <p class="text-xs text-gray-600 mt-0.5">Requires processing</p>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-purple-600">{{ $pendingRefunds }}</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-100">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Out of Stock</p>
                            <p class="text-xs text-gray-600 mt-0.5">Product variants depleted</p>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-red-600">{{ $outOfStockProducts }}</span>
                </div>
            </div>
        </div>

    </div>
@endsection
