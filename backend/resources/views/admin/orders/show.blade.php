@extends('layouts.admin')
@section('title', 'Order Details')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors bg-white w-10 h-10 rounded-full flex items-center justify-center shadow-sm border border-gray-100">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="font-outfit text-2xl font-bold text-gray-900 tracking-tight">Order #{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500 mt-1">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
        </div>

        @if(!in_array($order->status, ['delivered', 'cancelled', 'returned']))
            <div x-data="{ showCancelModal: false }">
                <button @click="showCancelModal = true" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-6 py-2.5 rounded-md text-sm font-bold uppercase tracking-widest transition-colors border border-red-200 hover:border-red-600 shadow-sm">
                    <i class="fa-solid fa-ban mr-2"></i> Cancel Order
                </button>

                <!-- Cancel Modal -->
                <div x-show="showCancelModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;">
                    <div @click.away="showCancelModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-lg overflow-hidden text-left">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-red-50">
                            <h3 class="font-bold text-red-700 uppercase tracking-wider text-sm flex items-center"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Cancel Order</h3>
                            <button @click="showCancelModal = false" class="text-red-400 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST">
                            @csrf
                            <div class="p-6 bg-white space-y-4">
                                <p class="text-sm text-gray-600 mb-4">Are you sure you want to cancel this order? This action will restock inventory and cannot be undone.</p>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Reason for Cancellation <span class="text-red-500">*</span></label>
                                    <select name="cancellation_reason" required class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500">
                                        <option value="">Select a reason...</option>
                                        <option value="Customer requested cancellation">Customer requested cancellation</option>
                                        <option value="Product unavailable">Product unavailable</option>
                                        <option value="Payment issue">Payment issue</option>
                                        <option value="Address issue">Address issue</option>
                                        <option value="Fraud/suspicious order">Fraud/suspicious order</option>
                                        <option value="Operational issue">Operational issue</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Additional Note (Optional)</label>
                                    <textarea name="cancellation_note" rows="3" class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500" placeholder="Provide any additional details..."></textarea>
                                </div>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                                <button type="button" @click="showCancelModal = false" class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-gray-900 transition-colors uppercase tracking-wider">Nevermind</button>
                                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-md text-sm font-bold uppercase tracking-widest hover:bg-red-700 transition-colors shadow-sm">Confirm Cancellation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <!-- Update Global Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Update Order Status</h3>
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="flex gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <select name="status" class="w-full appearance-none bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm font-medium focus:outline-none focus:ring-1 focus:ring-[#ff3f6c] focus:border-[#ff3f6c] focus:bg-white transition-colors">
                            <option value="placed" {{ $order->status == 'placed' ? 'selected' : '' }}>Placed</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="packed" {{ $order->status == 'packed' ? 'selected' : '' }}>Packed</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="out_for_delivery" {{ $order->status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                    <button type="submit" class="bg-gray-900 text-white px-6 py-3 rounded-md text-sm font-bold uppercase tracking-widest hover:bg-black transition-colors shrink-0 shadow-md hover:shadow-lg">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Order Items</h3>
                </div>
                <div class="p-6 space-y-6">
                    @foreach($order->items as $item)
                        <div class="flex flex-col md:flex-row items-start gap-4 pb-6 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                            @if($item->image_snapshot)
                                <img src="{{ $item->image_snapshot }}" class="w-20 h-28 object-cover rounded-md border border-gray-100 shrink-0 shadow-sm">
                            @endif
                            
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 leading-tight">{{ $item->product_name_snapshot ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500 mt-1 font-mono">SKU: {{ $item->variant_sku_snapshot ?? 'N/A' }}</p>
                                <div class="flex items-center gap-4 text-sm text-gray-600 mt-3 bg-gray-50 inline-flex px-3 py-1.5 rounded-md">
                                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full border border-gray-200" style="background-color: {{ strtolower($item->color_snapshot) }};"></span> {{ $item->color_snapshot }}</span>
                                    <span class="w-px h-4 bg-gray-200"></span>
                                    <span>Size <span class="font-bold text-gray-900">{{ $item->size_snapshot }}</span></span>
                                    <span class="w-px h-4 bg-gray-200"></span>
                                    <span>Qty <span class="font-bold text-gray-900">{{ $item->quantity }}</span></span>
                                </div>
                                <p class="font-bold text-gray-900 mt-4 text-lg">₹{{ number_format($item->price, 0) }}</p>
                            </div>

                            <div class="shrink-0 flex flex-col gap-2 w-full md:w-48 bg-gray-50 p-3 rounded-md border border-gray-100">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Item Status</span>
                                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                    <div class="relative">
                                        <select name="status" onchange="this.form.submit()" class="w-full appearance-none border border-gray-200 rounded px-3 py-2 text-xs font-bold uppercase tracking-wider bg-white focus:outline-none focus:border-[#ff3f6c]
                                            {{ $item->status == 'cancelled' ? 'text-red-600 border-red-200' : 'text-gray-700' }}
                                            {{ $item->status == 'delivered' ? 'text-green-600 border-green-200' : '' }}">
                                            <option value="placed" {{ $item->status == 'placed' ? 'selected' : '' }}>Placed</option>
                                            <option value="confirmed" {{ $item->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="packed" {{ $item->status == 'packed' ? 'selected' : '' }}>Packed</option>
                                            <option value="shipped" {{ $item->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="out_for_delivery" {{ $item->status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                            <option value="delivered" {{ $item->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $item->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            <option value="return_requested" {{ $item->status == 'return_requested' ? 'selected' : '' }}>Return Req</option>
                                            <option value="returned" {{ $item->status == 'returned' ? 'selected' : '' }}>Returned</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[10px]"></i>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Customer Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-50 pb-3">Customer & Shipping</h3>
                <div class="flex items-start gap-3 mt-4">
                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $order->address->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-phone mr-1 text-[10px]"></i> {{ $order->address->phone ?? '' }}</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3 mt-4 pt-4 border-t border-gray-50">
                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $order->address->address_line_1 ?? '' }}<br>
                            {{ $order->address->city ?? '' }}, {{ $order->address->state ?? '' }} <span class="font-bold text-gray-900">{{ $order->address->pincode ?? '' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-50 pb-3">Payment Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Method</span>
                        <span class="text-xs font-bold uppercase tracking-wider bg-gray-100 px-2.5 py-1 rounded-sm text-gray-700">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Status</span>
                        <span class="text-xs font-bold uppercase tracking-wider {{ $order->payment_status == 'completed' ? 'text-green-600 bg-green-50' : 'text-orange-600 bg-orange-50' }} px-2.5 py-1 rounded-sm">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                    
                    <div class="pt-4 mt-4 border-t border-gray-100 border-dashed space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Item Total</span>
                            <span class="font-medium text-gray-900">₹{{ number_format($order->total, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Shipping</span>
                            <span class="font-medium text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 mt-2 border-t border-gray-100">
                            <span class="font-bold text-gray-900">Grand Total</span>
                            <span class="font-black text-[#ff3f6c] text-xl">₹{{ number_format($order->total, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
