<x-app-layout>
    @section('title', 'STYLEORA | Order Details')

    <div class="bg-gray-50 min-h-screen pt-8 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6 flex items-center">
                <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-900 mr-4">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="font-outfit text-3xl font-bold text-gray-900">Order Details</h1>
            </div>

            <!-- Order Summary Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold mb-1">Order ID</p>
                    <p class="text-lg font-mono text-gray-900 font-bold">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold mb-1">Order Date</p>
                    <p class="text-lg text-gray-900 font-medium">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold mb-1">Order Total</p>
                    <p class="text-lg text-gray-900 font-bold">₹{{ number_format($order->total, 2) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Left Column: Products & Timeline -->
                <div class="md:col-span-2 space-y-8">
                    
                    @foreach($order->items as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <!-- Product Info -->
                            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row gap-6">
                                <div class="shrink-0">
                                    @php
                                        $displayImage = null;
                                        if ($item->variant && $item->variant->image) {
                                            $displayImage = $item->variant->image;
                                        } elseif ($item->product && $item->product->images->where('is_primary', true)->first()) {
                                            $displayImage = $item->product->images->where('is_primary', true)->first()->image_path;
                                        } elseif ($item->image_snapshot) {
                                            $displayImage = $item->image_snapshot;
                                        }
                                    @endphp
                                    @if($displayImage)
                                        <img src="{{ $displayImage }}" class="w-32 h-40 object-cover rounded bg-gray-50 border border-gray-200">
                                    @else
                                        <div class="w-32 h-40 bg-gray-200 rounded flex items-center justify-center border border-gray-200">
                                            <i class="fa-regular fa-image text-gray-400 text-3xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 text-xl mb-1">{{ $item->product_name_snapshot ?? ($item->product ? $item->product->name : 'Unknown Product') }}</h3>
                                    <p class="text-gray-500 mb-3 text-sm">{{ $item->product ? $item->product->brand->name ?? 'Brand' : 'Brand' }}</p>
                                    
                                    <div class="grid grid-cols-2 gap-y-2 text-sm mb-4">
                                        @if($item->color_snapshot)
                                            <div><span class="text-gray-500">Color:</span> <span class="font-medium text-gray-900">{{ $item->color_snapshot }}</span></div>
                                        @endif
                                        @if($item->size_snapshot)
                                            <div><span class="text-gray-500">Size:</span> <span class="font-medium text-gray-900">{{ $item->size_snapshot }}</span></div>
                                        @endif
                                        <div><span class="text-gray-500">Qty:</span> <span class="font-medium text-gray-900">{{ $item->quantity }}</span></div>
                                        <div><span class="text-gray-500">Price:</span> <span class="font-bold text-gray-900">₹{{ number_format($item->price, 2) }}</span></div>
                                    </div>
                                    
                                </div>
                            </div>

                            <!-- Tracking Timeline -->
                            <div class="p-6 bg-gray-50">
                                <h4 class="font-bold text-gray-900 mb-6 uppercase text-sm tracking-wider">Delivery Tracking</h4>
                                
                                @php
                                    // Calculate timeline logic. 
                                    // Order model has getTimelineStatusAttribute but we can also use $order->status directly.
                                    $timeline = $order->timeline_status; 
                                @endphp

                                @if(empty($timeline))
                                    <!-- Cancelled / Returned states -->
                                    <div class="flex items-center text-red-600 font-bold bg-red-50 p-4 rounded border border-red-200">
                                        <i class="fa-solid fa-circle-exclamation text-xl mr-3"></i>
                                        <div>
                                            <p class="text-sm uppercase tracking-wider mb-1">Order Status</p>
                                            <p class="text-lg">{{ str_replace('_', ' ', $order->status) }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="relative pl-8">
                                        <!-- Vertical line -->
                                        <div class="absolute left-3 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                                        
                                        @foreach($timeline as $key => $stage)
                                            <div class="relative mb-6 last:mb-0">
                                                <!-- Marker -->
                                                <div class="absolute -left-8 top-1 w-6 h-6 rounded-full flex items-center justify-center 
                                                    {{ $stage['completed'] || $stage['current'] ? 'bg-green-500' : 'bg-gray-200' }} border-4 border-white shadow-sm">
                                                    @if($stage['completed'])
                                                        <i class="fa-solid fa-check text-[10px] text-white"></i>
                                                    @elseif($stage['current'])
                                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Text -->
                                                <div>
                                                    <p class="font-bold {{ $stage['completed'] || $stage['current'] ? 'text-gray-900' : 'text-gray-400' }}">
                                                        {{ $stage['label'] }}
                                                    </p>
                                                    @if($stage['current'])
                                                        <p class="text-xs text-gray-500 mt-1">Current status</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Right Column: Shipping & Payment -->
                <div class="space-y-8">
                    <!-- Delivery Address -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 uppercase text-sm tracking-wider flex items-center">
                            <i class="fa-solid fa-location-dot mr-2 text-gray-400"></i> Delivery Address
                        </h3>
                        @if($order->address)
                            <p class="font-bold text-gray-900 mb-1">{{ $order->address->name ?? Auth::user()->name }}</p>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ $order->address->address_line1 }}<br>
                                @if($order->address->address_line2) {{ $order->address->address_line2 }}<br> @endif
                                {{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->pincode }}<br>
                                {{ $order->address->country ?? 'India' }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 mt-3">
                                <i class="fa-solid fa-phone mr-1 text-gray-400"></i> {{ $order->address->phone }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500">Address details not available.</p>
                        @endif
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 uppercase text-sm tracking-wider flex items-center">
                            <i class="fa-solid fa-credit-card mr-2 text-gray-400"></i> Payment Information
                        </h3>
                        
                        <div class="space-y-3 text-sm mb-6 pb-6 border-b border-gray-100">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Method</span>
                                <span class="font-medium text-gray-900 uppercase">{{ $order->payment_method }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                <span class="font-bold uppercase {{ $order->payment_status == 'completed' ? 'text-green-600' : 'text-orange-500' }}">{{ $order->payment_status }}</span>
                            </div>
                            @if($order->payments && $order->payments->count() > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Transaction ID</span>
                                    <span class="font-mono text-xs text-gray-900">{{ $order->payments->first()->transaction_id ?? 'N/A' }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Item Total</span>
                                <span class="font-medium text-gray-900">₹{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->discount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Discount</span>
                                    <span class="font-medium text-green-600">-₹{{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif
                            @if($order->coupon_discount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Coupon Discount</span>
                                    <span class="font-medium text-green-600">-₹{{ number_format($order->coupon_discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-500">Shipping</span>
                                <span class="font-medium text-gray-900">{{ $order->shipping > 0 ? '₹'.number_format($order->shipping, 2) : 'Free' }}</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-gray-200 text-base font-bold">
                                <span>Grand Total</span>
                                <span>₹{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    @if(in_array($order->status, ['cancelled', 'return_requested', 'returned']))
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 text-sm text-gray-600">
                        <p class="font-bold text-gray-900 mb-2">Refund Status</p>
                        <p>Refund amounts typically reflect in your original payment method within 5-7 business days.</p>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
