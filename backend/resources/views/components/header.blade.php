<header 
    x-data="{ 
        mobileMenuOpen: false,
        wishlistCount: {{ $wishlistCount ?? 0 }},
        cartCount: {{ $cartCount ?? 0 }}
    }"
    @wishlist-updated.window="wishlistCount = $event.detail.count"
    @cart-updated.window="cartCount = $event.detail.count"
    class="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 transition-all duration-300"
>
    <!-- Top Announcement Bar -->
    <div class="bg-black text-white text-[10px] sm:text-xs py-2 px-4 flex justify-center sm:justify-between items-center">
        <div class="flex space-x-6 text-center sm:text-left w-full sm:w-auto justify-center sm:justify-start">
            <a href="#" class="hover:text-gray-300 transition tracking-wider uppercase font-medium">✨ EXTRA 10% OFF on first order! Code: STYLE10</a>
        </div>
        <div class="hidden sm:flex space-x-4 tracking-wider uppercase font-medium">
            <a href="{{ route('track.order') }}" class="hover:text-gray-300 transition">Track Order</a>
            <span class="text-gray-500">|</span>
            <a href="{{ route('contact') }}" class="hover:text-gray-300 transition">Customer Service</a>
        </div>
    </div>

    <!-- Main Header -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 sm:h-20">
            
            <!-- Mobile Menu Button -->
            <div class="flex items-center lg:hidden">
                <button @click="mobileMenuOpen = true" class="text-gray-900 p-2 -ml-2 rounded-md hover:bg-gray-100 focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center justify-center lg:justify-start w-full lg:w-auto absolute lg:relative left-0 lg:left-auto pointer-events-none lg:pointer-events-auto">
                <a href="{{ route('home') }}" class="font-outfit font-black text-2xl tracking-[0.2em] text-black flex items-center pointer-events-auto">
                    STYLEORA
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex space-x-12 items-center h-full ml-12">
                <a href="{{ route('shop.men') }}" class="text-sm font-bold text-gray-900 hover:text-black py-7 transition-all border-b-4 border-transparent hover:border-[#ff3f6c] tracking-wider uppercase">MEN</a>
                <a href="{{ route('shop.women') }}" class="text-sm font-bold text-gray-900 hover:text-black py-7 transition-all border-b-4 border-transparent hover:border-[#ff3f6c] tracking-wider uppercase">WOMEN</a>
                <a href="{{ route('shop.sale') }}" class="text-sm font-bold text-red-600 hover:text-red-700 py-7 transition-all border-b-4 border-transparent hover:border-red-600 tracking-wider uppercase">SALE</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('shop.new-arrivals') }}" class="text-sm font-bold text-gray-900 hover:text-black py-7 transition-all border-b-4 border-transparent hover:border-black tracking-wider uppercase">NEW ARRIVALS</a>
                <a href="{{ route('shop.trending') }}" class="text-sm font-bold text-gray-900 hover:text-black py-7 transition-all border-b-4 border-transparent hover:border-black tracking-wider uppercase">TRENDING</a>
            </nav>

            <!-- Right Icons -->
            <div class="flex items-center space-x-5 sm:space-x-7 z-10 bg-white lg:bg-transparent px-2 lg:px-0">
                
                <!-- Advanced Search with AJAX -->
                <div class="hidden md:flex relative group" x-data="{ 
                    searchOpen: false, 
                    query: '', 
                    results: [], 
                    isLoading: false,
                    fetchSuggestions() {
                        if(this.query.length < 2) {
                            this.results = [];
                            return;
                        }
                        this.isLoading = true;
                        fetch(`/search/suggestions?q=${this.query}`)
                            .then(res => res.json())
                            .then(data => {
                                this.results = data;
                                this.isLoading = false;
                            });
                    } 
                }">
                    <button @click="searchOpen = true" class="text-gray-800 hover:text-[#ff3f6c] transition p-2 flex flex-col items-center gap-1 group">
                        <i class="fa-solid fa-magnifying-glass text-lg group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-bold tracking-wider hidden xl:block">SEARCH</span>
                    </button>

                    <!-- Search Overlay / Dropdown -->
                    <div x-show="searchOpen" @click.away="searchOpen = false" x-cloak class="absolute right-0 top-full mt-2 w-[500px] bg-white shadow-2xl border border-gray-100 p-4 rounded-xl z-50">
                        <form action="{{ route('shop.search') }}" method="GET" class="relative">
                            <input type="text" name="q" x-model="query" @input.debounce.300ms="fetchSuggestions" placeholder="Search for products, categories..." class="w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-[#ff3f6c] focus:ring-0 rounded-lg py-3 px-5 pl-12 text-sm transition-all" autofocus autocomplete="off">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-4 text-gray-400 text-sm"></i>
                            <div x-show="isLoading" class="absolute right-4 top-4">
                                <i class="fa-solid fa-circle-notch fa-spin text-gray-400"></i>
                            </div>
                        </form>

                        <!-- Search Results -->
                        <div x-show="results.length > 0" class="mt-4 flex flex-col gap-3 max-h-[60vh] overflow-y-auto">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Suggestions</p>
                            <template x-for="product in results" :key="product.id">
                                <a :href="product.url" class="flex items-center gap-4 p-2 hover:bg-gray-50 rounded-lg transition">
                                    <img :src="product.image" class="w-12 h-16 object-cover rounded-md">
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider" x-text="product.brand"></p>
                                        <p class="text-sm text-gray-900 font-medium line-clamp-1" x-text="product.name"></p>
                                        <div class="flex items-center gap-2 mt-1 text-sm">
                                            <span class="font-bold text-gray-900" x-text="'₹' + (product.discount_price ? product.discount_price : product.price)"></span>
                                            <span x-show="product.discount_price" class="text-gray-400 line-through text-xs" x-text="'₹' + product.price"></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                            <a :href="`/search?q=${query}`" class="block text-center text-sm font-bold text-[#ff3f6c] hover:underline mt-2 p-2">View all results</a>
                        </div>
                        <div x-show="query.length > 1 && results.length === 0 && !isLoading" class="mt-4 p-4 text-center text-gray-500 text-sm">
                            No products found matching your search.
                        </div>
                    </div>
                </div>

                <!-- Mobile Search Icon (Links to search page) -->
                <a href="{{ route('shop.search') }}" class="md:hidden text-gray-800 hover:text-[#ff3f6c] p-2">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </a>

                <!-- Account -->
                <div class="relative group" x-data="{ userMenuOpen: false }">
                    <button @click="userMenuOpen = !userMenuOpen" @mouseenter="userMenuOpen = true" @mouseleave="setTimeout(() => { if(!$el.matches(':hover') && !$refs.dropdown.matches(':hover')) userMenuOpen = false }, 100)" class="text-gray-800 hover:text-[#ff3f6c] transition p-2 flex flex-col items-center gap-1 group">
                        <i class="fa-regular fa-user text-lg group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-bold tracking-wider hidden xl:block">PROFILE</span>
                    </button>
                    
                    <div x-ref="dropdown" x-show="userMenuOpen" @mouseenter="userMenuOpen = true" @mouseleave="userMenuOpen = false" x-cloak class="absolute right-0 top-full w-56 bg-white shadow-2xl border border-gray-100 py-3 rounded-xl z-50">
                        @auth
                            <div class="px-5 py-3 border-b border-gray-100 mb-2">
                                <p class="text-xs text-gray-500 font-medium">Hello,</p>
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            </div>
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 mx-4 my-2 px-4 py-2 bg-gray-900 text-white text-sm font-bold rounded-md hover:bg-[#ff3f6c] transition">
                                    <i class="fa-solid fa-gauge"></i> Admin Dashboard
                                </a>
                                <div class="border-t border-gray-100 mt-1 mb-1"></div>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="block px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-pink-50 hover:text-[#ff3f6c] transition-colors"><i class="fa-solid fa-user w-5 mr-2"></i> My Profile</a>
                            <a href="{{ route('orders.index') }}" class="block px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-pink-50 hover:text-[#ff3f6c] transition-colors"><i class="fa-solid fa-box-open w-5 mr-2"></i> My Orders</a>
                            <a href="{{ route('wishlist.index') }}" class="block px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-pink-50 hover:text-[#ff3f6c] transition-colors"><i class="fa-solid fa-heart w-5 mr-2"></i> Wishlist</a>
                            <div class="border-t border-gray-100 mt-2 pt-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-5 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors"><i class="fa-solid fa-arrow-right-from-bracket w-5 mr-2"></i> Logout</button>
                                </form>
                            </div>
                        @else
                            <div class="px-5 py-3 border-b border-gray-100 mb-2">
                                <p class="text-sm font-bold text-gray-900 mb-1">Welcome to Styleora</p>
                                <p class="text-xs text-gray-500">Sign in to access your orders, offers and wishlist.</p>
                            </div>
                            <a href="{{ route('login') }}" class="block mx-4 my-2 px-4 py-2.5 bg-[#ff3f6c] text-white text-center text-sm font-bold rounded-md hover:bg-[#ed3a64] transition shadow-md">SIGN IN</a>
                            <a href="{{ route('register') }}" class="block px-5 py-2 text-sm text-center font-medium text-gray-600 hover:text-[#ff3f6c] hover:underline transition">Create an account</a>
                        @endauth
                    </div>
                </div>

                <!-- Dynamic Wishlist Icon -->
                <a href="{{ route('wishlist.index') }}" class="text-gray-800 hover:text-[#ff3f6c] transition p-2 flex flex-col items-center gap-1 group relative">
                    <i class="fa-regular fa-heart text-lg group-hover:scale-110 transition-transform"></i>
                    <span class="text-[10px] font-bold tracking-wider hidden xl:block">WISHLIST</span>
                    <!-- Hide if 0 using x-show -->
                    <span x-show="wishlistCount > 0" x-cloak class="absolute top-0 right-0 bg-[#ff3f6c] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white" x-text="wishlistCount"></span>
                </a>

                <!-- Dynamic Bag Icon -->
                <a href="{{ route('cart.index') }}" class="text-gray-800 hover:text-[#ff3f6c] transition p-2 flex flex-col items-center gap-1 group relative">
                    <i class="fa-solid fa-bag-shopping text-xl group-hover:scale-110 transition-transform"></i>
                    <span class="text-[10px] font-bold tracking-wider hidden xl:block">BAG</span>
                    <span x-show="cartCount > 0" x-cloak class="absolute top-0 right-0 bg-[#ff3f6c] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white" x-text="cartCount"></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-[100] lg:hidden" style="display: none;">
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
        
        <!-- Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 max-w-[280px] w-full bg-white shadow-2xl overflow-y-auto pb-6 z-50">
            
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50">
                <span class="font-outfit font-black text-xl tracking-widest text-black">STYLEORA</span>
                <button @click="mobileMenuOpen = false" class="text-gray-500 hover:text-red-500 p-2 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Mobile Auth -->
            @guest
                <div class="p-5 border-b border-gray-100 flex flex-col space-y-3 bg-gray-50/50">
                    <a href="{{ route('login') }}" class="w-full bg-[#ff3f6c] text-white text-center py-2.5 text-sm font-bold rounded-md shadow-sm">LOG IN</a>
                    <a href="{{ route('register') }}" class="w-full border-2 border-gray-200 text-gray-700 text-center py-2.5 text-sm font-bold rounded-md">SIGN UP</a>
                </div>
            @endguest

            <div class="py-2">
                <div class="flex flex-col border-b border-gray-100">
                    <a href="{{ route('shop.men') }}" class="px-6 py-4 text-sm font-bold tracking-wider text-gray-900 flex items-center justify-between">MEN <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i></a>
                    <a href="{{ route('shop.women') }}" class="px-6 py-4 text-sm font-bold tracking-wider text-gray-900 border-t border-gray-50 flex items-center justify-between">WOMEN <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i></a>
                </div>
                
                <div class="p-2 mt-2 space-y-1">
                    <a href="{{ route('shop.sale') }}" class="block px-6 py-3 text-sm font-bold tracking-wider text-red-600 rounded-md mx-2">SALE</a>
                    <a href="{{ route('shop.new-arrivals') }}" class="block px-6 py-3 text-sm font-bold tracking-wider text-gray-700 rounded-md mx-2">NEW ARRIVALS</a>
                    <a href="{{ route('shop.trending') }}" class="block px-6 py-3 text-sm font-bold tracking-wider text-gray-700 rounded-md mx-2">TRENDING NOW</a>
                </div>
            </div>
        </div>
    </div>
</header>
