<x-app-layout>
    @section('title', 'STYLEORA | Search Results')

    <div class="bg-white min-h-screen pt-8 pb-20">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h1 class="font-outfit text-3xl font-bold text-gray-900 mb-2">
                    Search Results for "{{ isset($mappedQuery) && $mappedQuery != $query ? $mappedQuery : $query }}"
                </h1>
                @if(isset($mappedQuery) && $mappedQuery != strtolower(trim($query)))
                    <p class="text-sm text-[#ff3f6c] mb-2 font-medium">Search instead for <a href="?q={{ $query }}&exact=1" class="underline">"{{ $query }}"</a></p>
                @endif
                <p class="text-sm text-gray-500">Showing {{ $products->count() }} of {{ $products->total() }} results</p>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                    @foreach($products as $product)
                        <div class="text-black">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @else
                <div class="py-20 text-center max-w-lg mx-auto">
                    <i class="fa-solid fa-magnifying-glass text-4xl text-gray-300 mb-6 block"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">No results found</h3>
                    <p class="text-gray-500 mb-8">We couldn't find any products matching "{{ $query }}". Please try checking your spelling or use more general terms.</p>
                    
                    <!-- Search again form -->
                    <form action="{{ route('shop.search') }}" method="GET" class="relative max-w-md mx-auto">
                        <input type="text" name="q" value="{{ $query }}" class="w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-black focus:ring-0 rounded-full py-3 px-5 pl-12 text-sm transition-all" required>
                        <i class="fa-solid fa-magnifying-glass absolute left-5 top-4 text-gray-400"></i>
                        <button type="submit" class="absolute right-2 top-1.5 bg-black text-white px-4 py-1.5 rounded-full text-sm font-medium hover:bg-gray-800 transition">Search</button>
                    </form>
                </div>
            @endif
            
        </div>
    </div>
</x-app-layout>
