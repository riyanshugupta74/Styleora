<x-app-layout>
    @section('title', 'STYLEORA | My Wishlist')

    <div class="bg-white min-h-screen pt-8 pb-20">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-outfit text-3xl font-bold text-gray-900 mb-8">My Wishlist</h1>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($items->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($items as $item)
                        <div class="relative group">
                            <x-product-card :product="$item->product" />
                            <form action="{{ route('wishlist.remove') }}" method="POST" class="absolute top-2 right-2 z-20">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                <button type="submit" class="bg-white w-8 h-8 rounded-full flex items-center justify-center text-red-500 shadow hover:bg-gray-50"><i class="fa-solid fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-20 text-center max-w-lg mx-auto">
                    <i class="fa-regular fa-heart text-4xl text-gray-300 mb-6 block"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Your wishlist is empty</h3>
                    <p class="text-gray-500 mb-8">Save items you love here to shop them later.</p>
                    <a href="{{ route('home') }}" class="inline-block bg-black text-white px-8 py-3 rounded-full font-medium hover:bg-gray-800 transition">Continue Shopping</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
