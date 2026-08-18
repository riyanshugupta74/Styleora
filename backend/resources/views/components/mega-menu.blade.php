@props(['type'])

<div class="relative group h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
    <a href="{{ route('shop.category', $type) }}" class="text-sm font-semibold text-gray-900 hover:text-black py-5 transition-all uppercase border-b-2" :class="open ? 'border-black' : 'border-transparent'">
        {{ $type }}
    </a>
    
    <!-- Mega Menu Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         style="display: none;"
         class="absolute top-full left-1/2 transform -translate-x-1/2 w-[800px] bg-white shadow-xl border border-gray-100 rounded-b-lg overflow-hidden">
        
        <div class="p-8 flex justify-between">
            @if($type === 'women')
                <!-- Women's Categories -->
                <div class="flex-1 pr-6">
                    <h3 class="text-sm font-bold text-black mb-4 border-b border-gray-100 pb-2"><a href="{{ route('shop.category', ['category_slug' => 'women', 'subcategory' => 'women-dresses']) }}">Dresses</a></h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.search', ['q' => 'mini dress women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Mini Dresses</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'midi dress women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Midi Dresses</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'maxi dress women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Maxi Dresses</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'aline dress women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">A-Line Dresses</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'bodycon dress women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Bodycon Dresses</a></li>
                    </ul>
                </div>
                <div class="flex-1 px-4">
                    <h3 class="text-sm font-bold text-black mb-4 border-b border-gray-100 pb-2"><a href="{{ route('shop.category', ['category_slug' => 'women', 'subcategory' => 'women-tops']) }}">Tops</a></h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.search', ['q' => 't-shirts women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">T-Shirts</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'shirts women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Shirts</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'crop tops women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Crop Tops</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'tank tops women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Tank Tops</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'blouses women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Blouses</a></li>
                    </ul>
                </div>
                <div class="flex-1 px-4">
                    <h3 class="text-sm font-bold text-black mb-4 border-b border-gray-100 pb-2"><a href="{{ route('shop.category', ['category_slug' => 'women', 'subcategory' => 'women-bottomwear']) }}">Bottomwear</a></h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.search', ['q' => 'jeans women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Jeans</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'trousers women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Trousers</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'cargo pants women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Cargo Pants</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'skirts women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Skirts</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'shorts women']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Shorts</a></li>
                    </ul>
                </div>
                <div class="flex-1 px-4">
                    <h3 class="text-sm font-bold text-black mb-4 border-b border-gray-100 pb-2">More</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.category', ['category_slug' => 'women', 'subcategory' => 'women-activewear']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition font-semibold">Activewear</a></li>
                        <li><a href="{{ route('shop.category', ['category_slug' => 'women', 'subcategory' => 'women-outerwear']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition font-semibold">Outerwear</a></li>
                        <li><a href="{{ route('shop.category', ['category_slug' => 'women', 'subcategory' => 'women-accessories']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition font-semibold">Accessories</a></li>
                        <li class="pt-2"><a href="{{ route('shop.collection', 'sale') }}" class="text-sm text-red-600 hover:text-red-700 hover:underline transition font-bold">Sale - Up to 50% Off</a></li>
                    </ul>
                </div>
            @else
                <!-- Men's Categories -->
                <div class="flex-1 pr-6">
                    <h3 class="text-sm font-bold text-black mb-4 border-b border-gray-100 pb-2"><a href="{{ route('shop.category', ['category_slug' => 'men', 'subcategory' => 'men-tops']) }}">Tops</a></h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.search', ['q' => 't-shirts men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">T-Shirts</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'shirts men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Shirts</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'polo shirts men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Polo Shirts</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'tank tops men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Tank Tops</a></li>
                    </ul>
                </div>
                <div class="flex-1 px-4">
                    <h3 class="text-sm font-bold text-black mb-4 border-b border-gray-100 pb-2"><a href="{{ route('shop.category', ['category_slug' => 'men', 'subcategory' => 'men-bottomwear']) }}">Bottomwear</a></h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.search', ['q' => 'jeans men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Jeans</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'cargo pants men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Cargo Pants</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'trousers men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Trousers</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'shorts men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Shorts</a></li>
                    </ul>
                </div>
                <div class="flex-1 px-4">
                    <h3 class="text-sm font-bold text-black mb-4 border-b border-gray-100 pb-2"><a href="{{ route('shop.category', ['category_slug' => 'men', 'subcategory' => 'men-activewear']) }}">Activewear</a></h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.search', ['q' => 'track pants men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Track Pants</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'sports t-shirts men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Sports T-Shirts</a></li>
                        <li><a href="{{ route('shop.search', ['q' => 'jackets men']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition">Jackets</a></li>
                    </ul>
                </div>
                <div class="flex-1 px-4">
                    <h3 class="text-sm font-bold text-black mb-4 border-b border-gray-100 pb-2">More</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.category', ['category_slug' => 'men', 'subcategory' => 'men-outerwear']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition font-semibold">Outerwear</a></li>
                        <li><a href="{{ route('shop.category', ['category_slug' => 'men', 'subcategory' => 'men-accessories']) }}" class="text-sm text-gray-600 hover:text-black hover:underline transition font-semibold">Accessories</a></li>
                        <li class="pt-2"><a href="{{ route('shop.collection', 'sale') }}" class="text-sm text-red-600 hover:text-red-700 hover:underline transition font-bold">Sale - Up to 50% Off</a></li>
                    </ul>
                </div>
            @endif
            
            <!-- Promotional Image -->
            <div class="w-1/4 pl-4 ml-4 border-l border-gray-100">
                <a href="{{ route('shop.category', $type) }}" class="block group">
                    <div class="relative overflow-hidden rounded bg-gray-100 h-full min-h-[200px]">
                        <!-- Using picsum as placeholder for promotional banner -->
                        <img src="https://picsum.photos/seed/{{ $type }}/300/400" alt="New Collection" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-black bg-opacity-20 flex flex-col justify-end p-4">
                            <span class="text-white font-bold text-lg leading-tight mb-1">New<br>Collection</span>
                            <span class="text-white text-xs underline">Shop Now</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
    </div>
</div>
