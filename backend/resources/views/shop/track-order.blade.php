<x-app-layout>
    @section('title', 'STYLEORA | Track Order')

    <div class="bg-gray-50 min-h-screen pt-12 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-12">
                <h1 class="font-outfit text-3xl md:text-4xl font-bold text-gray-900 mb-4">Track Your Order</h1>
                <p class="text-gray-500 max-w-lg mx-auto">Enter your Order ID to track the current status of your delivery.</p>
            </div>

            <!-- Search Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-12 max-w-2xl mx-auto">
                <form action="{{ route('track.order') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="order_number" class="sr-only">Order ID</label>
                        <input type="text" name="order_number" id="order_number" value="{{ request('order_number') }}" required
                            class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black font-mono uppercase" 
                            placeholder="Enter Order ID (e.g., ORD-12345)">
                    </div>
                    <button type="submit" class="bg-black text-white px-8 py-3 rounded-md font-bold uppercase tracking-wider hover:bg-gray-800 transition-colors shrink-0">
                        Track
                    </button>
                </form>
                
                @if(isset($error) && $error)
                    <div class="mt-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded text-sm flex items-start">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 mr-2"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endif
            </div>

            <!-- Tracking Results -->
            @if(isset($order) && $order)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-900 text-white p-6 sm:px-8 flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <p class="text-gray-400 text-sm uppercase tracking-wider font-bold mb-1">Order Number</p>
                            <p class="text-xl font-mono font-bold">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-400 text-sm uppercase tracking-wider font-bold mb-1">Order Date</p>
                            <p class="text-lg font-medium">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                            <!-- Left: Timeline -->
                            <div>
                                <h3 class="font-bold text-gray-900 mb-8 uppercase tracking-wider">Delivery Status</h3>
                                
                                @php
                                    $timeline = $order->timeline_status;
                                @endphp

                                @if(empty($timeline))
                                    <!-- Special States -->
                                    <div class="bg-gray-50 border border-gray-200 rounded p-6 text-center">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 
                                            {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }}">
                                            <i class="fa-solid {{ $order->status == 'cancelled' ? 'fa-xmark' : 'fa-rotate-left' }} text-2xl"></i>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-900 mb-2 uppercase">{{ str_replace('_', ' ', $order->status) }}</h4>
                                        <p class="text-gray-500 text-sm">This order is no longer active for delivery.</p>
                                    </div>
                                @else
                                    <div class="relative pl-8 max-w-sm mx-auto lg:mx-0">
                                        <div class="absolute left-3 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                                        
                                        @foreach($timeline as $stage)
                                            <div class="relative mb-8 last:mb-0">
                                                <div class="absolute -left-8 top-0 w-6 h-6 rounded-full flex items-center justify-center 
                                                    {{ $stage['completed'] || $stage['current'] ? 'bg-green-500' : 'bg-gray-200' }} border-4 border-white">
                                                    @if($stage['completed'])
                                                        <i class="fa-solid fa-check text-[10px] text-white"></i>
                                                    @elseif($stage['current'])
                                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                                    @endif
                                                </div>
                                                
                                                <div>
                                                    <p class="font-bold text-lg {{ $stage['completed'] || $stage['current'] ? 'text-gray-900' : 'text-gray-400' }}">
                                                        {{ $stage['label'] }}
                                                    </p>
                                                    @if($stage['current'])
                                                        <p class="text-sm font-medium text-[#ff3f6c] mt-1">Current Status</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if(in_array($order->status, ['shipped', 'out_for_delivery']))
                                        <div class="mt-8 bg-blue-50 border border-blue-200 rounded p-4 flex items-start text-blue-800">
                                            <i class="fa-solid fa-calendar-check mt-1 mr-3 text-blue-600"></i>
                                            <div>
                                                <p class="font-bold">Expected Delivery</p>
                                                <p class="text-sm mt-1">{{ $order->created_at->addDays(5)->format('l, d F Y') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Right: Order Summary -->
                            <div>
                                <h3 class="font-bold text-gray-900 mb-6 uppercase tracking-wider">Order Summary</h3>
                                
                                <div class="space-y-6">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="flex gap-4">
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
                                            <div class="w-16 h-20 shrink-0 bg-gray-100 rounded border border-gray-200 overflow-hidden">
                                                @if($displayImage)
                                                    <img src="{{ $displayImage }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center"><i class="fa-regular fa-image text-gray-400"></i></div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm line-clamp-2">{{ $item->product_name_snapshot ?? ($item->product ? $item->product->name : 'Unknown Product') }}</p>
                                                <p class="text-gray-500 text-xs mt-1">Qty: {{ $item->quantity }}</p>
                                                <p class="font-bold text-gray-900 text-sm mt-1">₹{{ number_format($item->price, 2) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($order->items->count() > 3)
                                        <p class="text-sm text-gray-500 text-center font-medium">+ {{ $order->items->count() - 3 }} more items</p>
                                    @endif
                                </div>
                                
                                <div class="mt-8 pt-6 border-t border-gray-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-500">Order Amount</span>
                                        <span class="font-bold text-gray-900 text-lg">₹{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Payment</span>
                                        <span class="font-medium text-gray-900 uppercase">{{ $order->payment_method }} ({{ $order->payment_status }})</span>
                                    </div>
                                </div>
                                
                                <div class="mt-8">
                                    <a href="{{ route('orders.show', $order->id) }}" class="block w-full text-center bg-gray-100 text-gray-900 font-bold py-3 rounded-md hover:bg-gray-200 transition-colors">
                                        View Full Order Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
