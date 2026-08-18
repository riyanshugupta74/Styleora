<x-app-layout>
    @section('title', 'STYLEORA | My Orders')

    <div class="bg-gray-50 min-h-screen pt-8 pb-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex items-center justify-between mb-8">
                <h1 class="font-outfit text-3xl font-bold text-gray-900">My Orders</h1>
                <a href="{{ route('home') }}" class="text-sm font-bold text-[#ff3f6c] hover:underline">Continue Shopping <i class="fa-solid fa-arrow-right ml-1 text-xs"></i></a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-8 flex items-center shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check text-xl mr-3"></i>
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-8 flex items-center shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-exclamation text-xl mr-3"></i>
                    <span class="block sm:inline font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($orders->count() > 0)
                <div class="space-y-8">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <!-- Order Header -->
                            <div class="bg-gray-100 px-6 py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
                                <div class="flex flex-wrap gap-8">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Order Placed</p>
                                        <p class="text-sm text-gray-900 font-medium">{{ $order->created_at->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Total</p>
                                        <p class="text-sm text-gray-900 font-medium">₹{{ number_format($order->total, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Payment</p>
                                        <p class="text-sm text-gray-900 font-medium uppercase">{{ $order->payment_method }} - 
                                            <span class="{{ $order->payment_status == 'completed' ? 'text-green-600' : 'text-orange-500' }}">{{ $order->payment_status }}</span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Order Status</p>
                                        <p class="text-sm font-medium uppercase">
                                            @if($order->status == 'delivered')
                                                <span class="text-green-600"><i class="fa-solid fa-circle-check mr-1"></i> Delivered</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="text-red-500"><i class="fa-solid fa-circle-xmark mr-1"></i> Cancelled</span>
                                            @else
                                                <span class="text-blue-500"><i class="fa-solid fa-truck-fast mr-1"></i> {{ str_replace('_', ' ', $order->status) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Order #</p>
                                    <p class="text-sm font-mono text-gray-900 font-bold">{{ $order->order_number }}</p>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="p-6">
                                <div class="space-y-8">
                                    @foreach($order->items as $item)
                                        <div class="flex flex-col md:flex-row items-start gap-6 pb-6 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                            <!-- Image -->
                                            <div class="shrink-0 relative group">
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
                                                    <a href="{{ route('orders.show', $order->id) }}">
                                                        <img src="{{ $displayImage }}" class="w-24 h-32 object-cover rounded bg-gray-50 border border-gray-200 group-hover:opacity-90 transition-opacity">
                                                    </a>
                                                @else
                                                    <a href="{{ route('orders.show', $order->id) }}">
                                                        <div class="w-24 h-32 bg-gray-200 rounded flex items-center justify-center border border-gray-200 group-hover:opacity-90 transition-opacity">
                                                            <i class="fa-regular fa-image text-gray-400 text-2xl"></i>
                                                        </div>
                                                    </a>
                                                @endif
                                            </div>
                                            
                                            <!-- Details -->
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900 text-lg mb-1">{{ $item->product_name_snapshot ?? ($item->product ? $item->product->name : 'Unknown Product') }}</h4>
                                                
                                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mt-2 mb-3">
                                                    @if($item->color_snapshot)
                                                        <span class="bg-gray-100 px-2 py-1 rounded border border-gray-200">Color: <strong>{{ $item->color_snapshot }}</strong></span>
                                                    @endif
                                                    @if($item->size_snapshot)
                                                        <span class="bg-gray-100 px-2 py-1 rounded border border-gray-200">Size: <strong>{{ $item->size_snapshot }}</strong></span>
                                                    @endif
                                                    <span class="bg-gray-100 px-2 py-1 rounded border border-gray-200">Qty: <strong>{{ $item->quantity }}</strong></span>
                                                </div>
                                                
                                                <p class="font-bold text-gray-900 text-lg">₹{{ number_format($item->price, 2) }}</p>

                                                <!-- Item Timeline Visualization -->
                                                <div class="mt-6">
                                                    @php
                                                        $stages = ['placed', 'confirmed', 'shipped', 'out_for_delivery', 'delivered'];
                                                        $currentIndex = array_search($item->status, $stages);
                                                        if($currentIndex === false) { $currentIndex = -1; } // e.g. cancelled
                                                    @endphp
                                                    
                                                    @if(!in_array($item->status, ['cancelled', 'return_requested', 'returned', 'refunded']))
                                                    <div class="relative flex items-center justify-between w-full max-w-lg mb-2">
                                                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 rounded"></div>
                                                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-green-500 rounded transition-all" style="width: {{ $currentIndex >= 0 ? ($currentIndex / 4) * 100 : 0 }}%"></div>
                                                        
                                                        @foreach($stages as $index => $stage)
                                                            <div class="relative z-10 flex flex-col items-center">
                                                                <div class="w-4 h-4 rounded-full border-2 {{ $currentIndex >= $index ? 'bg-green-500 border-green-500' : 'bg-white border-gray-300' }}"></div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="flex justify-between max-w-lg text-[10px] uppercase font-bold text-gray-500">
                                                        <span>Placed</span>
                                                        <span>Confirmed</span>
                                                        <span>Shipped</span>
                                                        <span>Out for Delivery</span>
                                                        <span>Delivered</span>
                                                    </div>
                                                    @else
                                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $item->status == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                                            <i class="fa-solid fa-circle-info mr-2"></i> Item {{ str_replace('_', ' ', $item->status) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="shrink-0 flex flex-col gap-3 min-w-[140px] mt-4 md:mt-0">
                                                <a href="{{ route('orders.show', $order->id) }}" class="w-full text-center text-sm font-bold text-white bg-[#ff3f6c] px-4 py-2 rounded hover:bg-[#d82a54] transition-colors">View Details</a>

                                                @if(in_array($order->status, ['placed', 'confirmed']) && in_array($item->status, ['placed', 'confirmed']))
                                                    <form action="{{ route('orders.cancel', $item->id) }}" method="POST" x-data="{ open: false }">
                                                        @csrf
                                                        <button type="button" @click="open = true" class="w-full text-sm font-bold text-red-600 border border-red-600 px-4 py-2 rounded hover:bg-red-50 transition-colors">Cancel Order</button>
                                                        <div x-show="open" x-cloak class="fixed inset-0 z-50 bg-black/50 p-4 flex items-center justify-center">
                                                            <div @click.away="open = false" class="bg-white rounded-xl p-6 max-w-md w-full text-left">
                                                                <h3 class="font-bold text-lg">Why do you want to cancel this order?</h3>
                                                                <select name="cancellation_reason" required class="mt-4 w-full rounded border-gray-300">
                                                                    <option value="">Select a reason</option><option value="ordered_by_mistake">Ordered by mistake</option><option value="better_price">Found a better price</option><option value="delivery_too_long">Delivery is taking too long</option><option value="changed_mind">Changed my mind</option><option value="not_required">Product no longer required</option><option value="wrong_size_or_color">Ordered wrong size/color</option><option value="duplicate_order">Duplicate order</option><option value="other">Other</option>
                                                                </select>
                                                                <textarea name="cancellation_note" maxlength="1000" rows="3" class="mt-3 w-full rounded border-gray-300" placeholder="Additional comments (optional)"></textarea>
                                                                <div class="mt-4 flex justify-end gap-3"><button type="button" @click="open = false" class="px-4 py-2">Keep order</button><button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Confirm Cancellation</button></div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @elseif($order->status !== 'cancelled' && !in_array($order->status, ['delivered', 'returned', 'exchanged']))
                                                    <p class="text-xs text-gray-500 text-center">This order can no longer be cancelled.</p>
                                                @endif
                                                
                                                @if($item->status == 'delivered')
                                                    @php
                                                        // Check return eligibility
                                                        $isReturnable = $item->product ? $item->product->is_returnable : false;
                                                        $returnWindow = $item->product ? $item->product->return_window_days : 15;
                                                        $deliveryDate = $order->updated_at; // Mocking delivery date
                                                        $daysSinceDelivery = now()->diffInDays($deliveryDate);
                                                        $canReturn = $isReturnable && ($daysSinceDelivery <= $returnWindow);
                                                    @endphp
                                                    
                                                    @if($canReturn)
                                                        <button class="w-full text-sm font-bold text-[#ff3f6c] border border-[#ff3f6c] px-4 py-2 rounded hover:bg-pink-50 transition-colors">Return</button>
                                                        
                                                        @if($item->product && $item->product->is_exchangeable)
                                                            <button class="w-full text-sm font-bold text-blue-600 border border-blue-600 px-4 py-2 rounded hover:bg-blue-50 transition-colors">Exchange</button>
                                                        @endif
                                                    @else
                                                        <p class="text-xs text-gray-500 font-medium text-center bg-gray-50 px-2 py-1 rounded">Return window closed</p>
                                                    @endif
                                                    
                                                    <button class="w-full text-sm font-medium text-gray-600 border border-gray-300 px-4 py-2 rounded hover:bg-gray-50 transition-colors mt-2">Write Review</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">
                    @if(method_exists($orders, 'links'))
                        {{ $orders->links() }}
                    @endif
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-box text-4xl text-gray-400"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">No orders found</h2>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">Looks like you haven't made any purchases yet. Start shopping to fill this space!</p>
                    <a href="{{ route('home') }}" class="inline-block bg-black text-white px-8 py-3 rounded-full font-bold uppercase tracking-wider text-sm hover:bg-gray-800 transition-colors shadow-md">Browse Products</a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
