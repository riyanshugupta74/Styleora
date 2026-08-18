<x-app-layout>
    @section('title', 'STYLEORA | ' . $title)

    <div class="bg-white min-h-screen pt-4 pb-20" x-data="{ mobileFiltersOpen: false, mobileSortOpen: false }">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-bold text-gray-500 mb-6 uppercase tracking-wider">
                <a href="{{ route('home') }}" class="hover:text-black">Home</a>
                <span class="mx-2">/</span>
                <span class="text-black">{{ $title }}</span>
            </nav>

            <div class="flex items-end justify-between mb-6 pb-4 border-b border-gray-200">
                <h1 class="font-outfit text-2xl font-black uppercase tracking-wider text-gray-900">{{ $title }} <span class="text-gray-400 text-sm font-medium ml-2">- {{ $products->total() }} items</span></h1>
                
                <!-- Sorting -->
                <div class="hidden md:flex items-center gap-3">
                    <label class="text-xs font-bold text-gray-900 uppercase tracking-wider">Sort By</label>
                    <form id="sort-form" method="GET" action="{{ url()->current() }}">
                        @foreach(request()->except('sort', 'page') as $key => $val)
                            @if(is_array($val))
                                @foreach($val as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endif
                        @endforeach
                        <select name="sort" onchange="document.getElementById('sort-form').submit()" class="border-gray-300 text-sm focus:border-black focus:ring-0 rounded-sm py-2 bg-white font-medium cursor-pointer">
                            <option value="recommended" {{ request('sort') == 'recommended' ? 'selected' : '' }}>Recommended</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>What's New</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="discount" {{ request('sort') == 'discount' ? 'selected' : '' }}>Better Discount</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="w-full lg:w-[250px] shrink-0 border-r border-gray-200 pr-6 hidden lg:block">
                    <form id="filter-form" method="GET" action="{{ url()->current() }}" class="space-y-6 sticky top-28">
                        
                        <!-- Preserve Sort -->
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-bold text-sm uppercase tracking-widest text-gray-900">Filters</h2>
                            <a href="{{ url()->current() }}" class="text-xs font-bold text-[#ff3f6c] uppercase">Clear All</a>
                        </div>

                        <!-- Brand Filter -->
                        <div class="border-b border-gray-200 pb-5">
                            <h3 class="font-bold text-gray-900 mb-3 uppercase text-xs tracking-wider">Brand</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                                @php
                                    $selectedBrands = request('brand') ? (is_array(request('brand')) ? request('brand') : explode(',', request('brand'))) : [];
                                @endphp
                                @foreach($filters['brands'] as $brand)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="brand[]" value="{{ $brand->id }}" onchange="this.form.submit()" {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }} class="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] rounded-sm cursor-pointer">
                                        <span class="text-sm text-gray-700 group-hover:text-black transition-colors">{{ $brand->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="border-b border-gray-200 pb-5">
                            <h3 class="font-bold text-gray-900 mb-3 uppercase text-xs tracking-wider">Price</h3>
                            <div class="space-y-2">
                                @php
                                    $priceMin = request('price_min');
                                    $priceMax = request('price_max');
                                @endphp
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="price_range" value="" onchange="window.location.href='{{ request()->fullUrlWithQuery(['price_min' => null, 'price_max' => null]) }}'" {{ !$priceMin && !$priceMax ? 'checked' : '' }} class="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] cursor-pointer">
                                    <span class="text-sm text-gray-700">All Prices</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="price_range" value="0-999" onchange="window.location.href='{{ request()->fullUrlWithQuery(['price_min' => 0, 'price_max' => 999]) }}'" {{ $priceMax == 999 ? 'checked' : '' }} class="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] cursor-pointer">
                                    <span class="text-sm text-gray-700">Under Rs. 999</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="price_range" value="1000-1999" onchange="window.location.href='{{ request()->fullUrlWithQuery(['price_min' => 1000, 'price_max' => 1999]) }}'" {{ $priceMin == 1000 && $priceMax == 1999 ? 'checked' : '' }} class="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] cursor-pointer">
                                    <span class="text-sm text-gray-700">Rs. 1000 - Rs. 1999</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="price_range" value="2000" onchange="window.location.href='{{ request()->fullUrlWithQuery(['price_min' => 2000, 'price_max' => null]) }}'" {{ $priceMin == 2000 && !$priceMax ? 'checked' : '' }} class="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] cursor-pointer">
                                    <span class="text-sm text-gray-700">Rs. 2000 and above</span>
                                </label>
                            </div>
                        </div>

                        <!-- Color Filter -->
                        <div class="border-b border-gray-200 pb-5">
                            <h3 class="font-bold text-gray-900 mb-3 uppercase text-xs tracking-wider">Color</h3>
                            <div class="grid grid-cols-6 gap-2">
                                @php
                                    $selectedColors = request('color') ? (is_array(request('color')) ? request('color') : explode(',', request('color'))) : [];
                                @endphp
                                @foreach($filters['colors'] as $color)
                                    <label class="relative cursor-pointer group" title="{{ $color->name }}">
                                        <input type="checkbox" name="color[]" value="{{ $color->id }}" onchange="this.form.submit()" {{ in_array($color->id, $selectedColors) ? 'checked' : '' }} class="peer sr-only">
                                        <div class="w-6 h-6 rounded-full border border-gray-300 shadow-sm peer-checked:ring-2 peer-checked:ring-[#ff3f6c] peer-checked:ring-offset-1 transition-all" style="background-color: {{ $color->hex_code }}"></div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </aside>

                <!-- Product Grid -->
                <div class="flex-1 w-full">
                    <!-- Mobile Filters Toggle -->
                    <div class="flex lg:hidden justify-between items-center mb-4 border-b border-gray-200 pb-4">
                        <button @click="mobileFiltersOpen = true" class="font-bold text-sm uppercase tracking-wider text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-filter"></i> Filters
                        </button>
                        <button @click="mobileSortOpen = true" class="font-bold text-sm uppercase tracking-wider text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-sort"></i> Sort
                        </button>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                        @forelse($products as $product)
                            <div class="text-black">
                                <x-product-card :product="$product" />
                            </div>
                        @empty
                            <div class="col-span-full py-20 text-center">
                                <i class="fa-solid fa-box-open text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                                <p class="text-gray-500">We couldn't find any products matching your selection.</p>
                                <a href="{{ url()->current() }}" class="inline-block mt-4 text-[#ff3f6c] font-bold border border-[#ff3f6c] px-6 py-2 uppercase tracking-widest text-xs hover:bg-[#ff3f6c] hover:text-white transition-colors">Clear all filters</a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center border-t border-gray-100 pt-8">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Mobile Filters Drawer -->
        <div x-show="mobileFiltersOpen" style="display: none;" class="fixed inset-0 z-50 flex lg:hidden">
            <div x-show="mobileFiltersOpen" x-transition.opacity class="fixed inset-0 bg-black/50" @click="mobileFiltersOpen = false"></div>
            <div x-show="mobileFiltersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-full max-w-xs flex-col overflow-y-auto bg-white pb-12 shadow-xl">
                <div class="flex px-4 pb-2 pt-5 items-center justify-between border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest">Filters</h2>
                    <button type="button" class="-m-2 p-2 text-gray-400 hover:text-gray-500" @click="mobileFiltersOpen = false">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="px-4 py-6">
                    <form id="mobile-filter-form" method="GET" action="{{ url()->current() }}">
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                        
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-900 mb-3 uppercase text-xs tracking-wider">Brand</h3>
                            <div class="space-y-3">
                                @foreach($filters['brands'] as $brand)
                                    <label class="flex items-center gap-3">
                                        <input type="checkbox" name="brand[]" value="{{ $brand->id }}" {{ in_array($brand->id, $selectedBrands ?? []) ? 'checked' : '' }} class="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c]">
                                        <span class="text-sm">{{ $brand->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-8 flex gap-4">
                            <a href="{{ url()->current() }}" class="flex-1 py-3 text-center border border-gray-300 text-sm font-bold uppercase">Clear</a>
                            <button type="submit" class="flex-1 py-3 text-center bg-[#ff3f6c] text-white text-sm font-bold uppercase">Apply</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Sort Drawer -->
        <div x-show="mobileSortOpen" style="display: none;" class="fixed inset-0 z-50 flex lg:hidden items-end justify-center">
            <div x-show="mobileSortOpen" x-transition.opacity class="fixed inset-0 bg-black/50" @click="mobileSortOpen = false"></div>
            <div x-show="mobileSortOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="relative w-full bg-white rounded-t-xl pb-8 shadow-xl">
                <div class="flex px-4 py-4 items-center justify-between border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest">Sort By</h2>
                    <button type="button" class="-m-2 p-2 text-gray-400 hover:text-gray-500" @click="mobileSortOpen = false">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="px-4 py-4">
                    <form method="GET" action="{{ url()->current() }}">
                        @foreach(request()->except('sort', 'page') as $key => $val)
                            @if(is_array($val)) @foreach($val as $v) <input type="hidden" name="{{ $key }}[]" value="{{ $v }}"> @endforeach
                            @else <input type="hidden" name="{{ $key }}" value="{{ $val }}"> @endif
                        @endforeach
                        <div class="flex flex-col">
                            @php $currentSort = request('sort', 'newest'); @endphp
                            <label class="py-3 border-b border-gray-100 flex items-center justify-between">
                                <span class="{{ $currentSort == 'recommended' ? 'font-bold text-[#ff3f6c]' : '' }}">Recommended</span>
                                <input type="radio" name="sort" value="recommended" onchange="this.form.submit()" {{ $currentSort == 'recommended' ? 'checked' : '' }} class="text-[#ff3f6c] focus:ring-0 w-4 h-4">
                            </label>
                            <label class="py-3 border-b border-gray-100 flex items-center justify-between">
                                <span class="{{ $currentSort == 'newest' ? 'font-bold text-[#ff3f6c]' : '' }}">What's New</span>
                                <input type="radio" name="sort" value="newest" onchange="this.form.submit()" {{ $currentSort == 'newest' ? 'checked' : '' }} class="text-[#ff3f6c] focus:ring-0 w-4 h-4">
                            </label>
                            <label class="py-3 border-b border-gray-100 flex items-center justify-between">
                                <span class="{{ $currentSort == 'price_low' ? 'font-bold text-[#ff3f6c]' : '' }}">Price: Low to High</span>
                                <input type="radio" name="sort" value="price_low" onchange="this.form.submit()" {{ $currentSort == 'price_low' ? 'checked' : '' }} class="text-[#ff3f6c] focus:ring-0 w-4 h-4">
                            </label>
                            <label class="py-3 border-b border-gray-100 flex items-center justify-between">
                                <span class="{{ $currentSort == 'price_high' ? 'font-bold text-[#ff3f6c]' : '' }}">Price: High to Low</span>
                                <input type="radio" name="sort" value="price_high" onchange="this.form.submit()" {{ $currentSort == 'price_high' ? 'checked' : '' }} class="text-[#ff3f6c] focus:ring-0 w-4 h-4">
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af; 
        }
    </style>
</x-app-layout>
