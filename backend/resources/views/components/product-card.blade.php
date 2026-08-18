@props(['product'])

<div class="group relative bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
    
    <!-- Image Wrapper -->
    <a href="{{ route('shop.product', $product->slug) }}" class="block relative aspect-[4/5] bg-gray-50 overflow-hidden">
        @php
            $primaryImage = $product->images->where('is_primary', 1)->first() 
                            ?? $product->images->first();
            $imageUrl = $primaryImage ? $primaryImage->image_path : 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&q=80&w=600&h=800';
        @endphp
        
        <img src="{{ $imageUrl }}" 
             alt="{{ $product->name }}" 
             loading="lazy"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
             
        <!-- Badges -->
        <div class="absolute top-3 left-3 flex flex-col gap-1 z-10">
            @if($product->discount_price && $product->discount_price < $product->price)
                <span class="bg-[#ff3f6c] text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm tracking-wider">
                    SALE {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                </span>
            @endif
            @if($product->is_new_arrival)
                <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm tracking-wider">
                    NEW
                </span>
            @endif
            @if($product->is_trending)
                <span class="bg-purple-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm tracking-wider">
                    TRENDING
                </span>
            @endif
        </div>
    </a>

    <!-- Wishlist Button (AJAX) -->
    @php
        $inWishlist = in_array($product->id, $wishlistProductIds ?? []);
    @endphp

    <div x-data="{ 
            inWishlist: {{ $inWishlist ? 'true' : 'false' }}, 
            loading: false,
            toggle() {
                @if(!Auth::check())
                    window.location.href = '{{ route('login') }}';
                    return;
                @endif
                
                this.loading = true;
                fetch('{{ route('api.wishlist.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: {{ $product->id }} })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if(data.success) {
                        this.inWishlist = data.action === 'added';
                        // Update global header count
                        window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                        // Show toast
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: data.message, type: 'success' } }));
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: data.message || 'Error', type: 'error' } }));
                    }
                })
                .catch(() => {
                    this.loading = false;
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Network error', type: 'error' } }));
                });
            }
         }" 
         class="absolute top-3 right-3 z-20">
        <button @click="toggle" 
                class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:scale-110 transition-transform focus:outline-none"
                :class="inWishlist ? 'text-[#ff3f6c]' : 'text-gray-400 hover:text-black'">
            <i class="fa-heart text-sm transition-colors" :class="inWishlist ? 'fa-solid' : 'fa-regular'" x-show="!loading"></i>
            <i class="fa-solid fa-spinner fa-spin text-xs text-gray-500" x-show="loading" x-cloak></i>
        </button>
    </div>

    <!-- Product Info -->
    <div class="p-4">
        <p class="text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider line-clamp-1">{{ $product->brand->name ?? 'STYLEORA' }}</p>
        <a href="{{ route('shop.product', $product->slug) }}" class="block">
            <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-1 group-hover:text-[#ff3f6c] transition-colors">{{ $product->name }}</h3>
        </a>
        
        <div class="flex items-center gap-2 mb-3">
            @if($product->discount_price && $product->discount_price < $product->price)
                <span class="text-sm font-bold text-gray-900">₹{{ number_format($product->discount_price) }}</span>
                <span class="text-xs text-gray-400 line-through">₹{{ number_format($product->price) }}</span>
            @else
                <span class="text-sm font-bold text-gray-900">₹{{ number_format($product->price) }}</span>
            @endif
        </div>

        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="w-full bg-white border border-gray-200 text-gray-900 py-2 rounded-lg text-sm font-bold hover:bg-black hover:text-white hover:border-black transition-all">
                ADD TO BAG
            </button>
        </form>
    </div>
</div>