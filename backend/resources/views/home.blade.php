<x-app-layout>
    @section('title', 'STYLEORA | Premium Fashion Marketplace')

    <div class="pb-16 pt-2">
        <!-- Hero Section -->
        <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-12">
            <div class="relative w-full h-[500px] md:h-[600px] rounded-xl overflow-hidden shadow-xl bg-gray-900 group">
                <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&q=80&w=1600" alt="Fashion Hero" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
                <div class="absolute inset-0 flex flex-col justify-center px-10 md:px-20 max-w-2xl">
                    <span class="text-[#ff3f6c] font-bold tracking-widest uppercase mb-2">New Season Arrival</span>
                    <h1 class="text-white font-outfit font-black text-5xl md:text-7xl leading-none mb-6">THE GRAND<br>FASHION SALE</h1>
                    <p class="text-gray-200 text-lg md:text-xl mb-8 font-light">Elevate your style with up to <span class="font-bold text-white">70% OFF</span> on premium brands. Limited time only.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('shop.women') }}" class="bg-[#ff3f6c] hover:bg-[#ed3a64] text-white font-bold py-4 px-10 text-center tracking-wider transition-colors rounded-sm">SHOP WOMEN</a>
                        <a href="{{ route('shop.men') }}" class="bg-white hover:bg-gray-100 text-black font-bold py-4 px-10 text-center tracking-wider transition-colors rounded-sm">SHOP MEN</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Deal Highlights (Circular Cards) -->
        <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
            <h2 class="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 mb-8 flex items-center gap-4">
                <span class="w-2 h-8 bg-[#ff3f6c]"></span> Top Categories
            </h2>
            <div class="flex overflow-x-auto gap-6 md:gap-8 pb-4 scrollbar-hide snap-x">
                @foreach($highlightCats as $cat)
                <a href="{{ route('shop.search', ['category' => $cat->slug]) }}" class="flex-shrink-0 snap-start group flex flex-col items-center gap-3">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full overflow-hidden border-2 border-transparent group-hover:border-[#ff3f6c] p-1 transition-all">
                        <img src="{{ $cat->image ?? 'https://placehold.co/400x400/eeeeee/999999?text='.urlencode($cat->name) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover rounded-full group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <span class="font-bold text-sm text-gray-800 uppercase tracking-wider group-hover:text-[#ff3f6c] transition-colors">{{ $cat->name }}</span>
                </a>
                @endforeach
            </div>
        </section>

        <!-- Big Sale Banner -->
        <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
            <a href="{{ route('shop.sale') }}" class="block relative w-full h-[200px] md:h-[300px] rounded-xl overflow-hidden shadow-md group">
                <img src="https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&q=80&w=1600" alt="Sale" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                    <h3 class="text-white font-outfit font-black text-3xl md:text-5xl tracking-widest mb-2">DEAL OF THE DAY</h3>
                    <p class="text-white/90 text-lg md:text-xl font-medium mb-4 tracking-wider">FLAT 50% - 70% OFF</p>
                    <span class="bg-white text-black font-bold py-2 px-6 text-sm uppercase tracking-wider hover:bg-gray-100 transition-colors">Explore Deals</span>
                </div>
            </a>
        </section>

        <!-- Trending Collection Grid -->
        <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
            <div class="flex justify-between items-end mb-8">
                <h2 class="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 flex items-center gap-4">
                    <span class="w-2 h-8 bg-purple-600"></span> Trending Now
                </h2>
                <a href="{{ route('shop.trending') }}" class="text-sm font-bold text-gray-500 hover:text-black uppercase tracking-wider transition-colors hidden sm:block">View All <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
                @forelse($trendingProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full text-center text-gray-500 py-10">No products found.</div>
                @endforelse
            </div>
            <div class="mt-6 text-center sm:hidden">
                <a href="{{ route('shop.trending') }}" class="inline-block border border-gray-300 text-gray-700 font-bold py-3 px-8 text-sm uppercase tracking-wider rounded w-full">View All Trending</a>
            </div>
        </section>

        <!-- Sale Collection Grid -->
        <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
            <div class="flex justify-between items-end mb-8">
                <h2 class="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 flex items-center gap-4">
                    <span class="w-2 h-8 bg-[#ff3f6c]"></span> Mega Sale
                </h2>
                <a href="{{ route('shop.sale') }}" class="text-sm font-bold text-gray-500 hover:text-black uppercase tracking-wider transition-colors hidden sm:block">View All <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
                @forelse($saleProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full text-center text-gray-500 py-10">No products found.</div>
                @endforelse
            </div>
            <div class="mt-6 text-center sm:hidden">
                <a href="{{ route('shop.sale') }}" class="inline-block border border-gray-300 text-gray-700 font-bold py-3 px-8 text-sm uppercase tracking-wider rounded w-full">View All Sale</a>
            </div>
        </section>

        <!-- New Arrivals Grid -->
        <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
            <div class="flex justify-between items-end mb-8">
                <h2 class="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 flex items-center gap-4">
                    <span class="w-2 h-8 bg-blue-600"></span> New Arrivals
                </h2>
                <a href="{{ route('shop.new-arrivals') }}" class="text-sm font-bold text-gray-500 hover:text-black uppercase tracking-wider transition-colors hidden sm:block">View All <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
                @forelse($newProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full text-center text-gray-500 py-10">No products found.</div>
                @endforelse
            </div>
            <div class="mt-6 text-center sm:hidden">
                <a href="{{ route('shop.new-arrivals') }}" class="inline-block border border-gray-300 text-gray-700 font-bold py-3 px-8 text-sm uppercase tracking-wider rounded w-full">View All New</a>
            </div>
        </section>
        
        <!-- App Download Banner -->
        <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-100 rounded-xl p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-xl text-center md:text-left">
                    <h3 class="text-2xl md:text-4xl font-black font-outfit uppercase tracking-tight mb-4">Get the Styleora App</h3>
                    <p class="text-gray-600 mb-6 text-lg">Download our app for exclusive deals, early access to sales, and a faster checkout experience.</p>
                    <div class="flex gap-4 justify-center md:justify-start">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store" class="h-10 cursor-pointer hover:opacity-80 transition-opacity">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Play Store" class="h-10 cursor-pointer hover:opacity-80 transition-opacity">
                    </div>
                </div>
                <div class="hidden md:block w-48 opacity-80">
                    <i class="fa-solid fa-mobile-screen-button text-[150px] text-gray-300"></i>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
