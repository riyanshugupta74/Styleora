<x-app-layout>
    @section('title', 'STYLEORA | Shopping Cart')

    <div class="bg-white min-h-screen pt-8 pb-20">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-outfit text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(count($cart) > 0)
                <div class="flex flex-col lg:flex-row gap-12">
                    <div class="w-full lg:w-2/3">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="pb-4 font-semibold text-gray-900">Product</th>
                                    <th class="pb-4 font-semibold text-gray-900">Price</th>
                                    <th class="pb-4 font-semibold text-gray-900">Quantity</th>
                                    <th class="pb-4 font-semibold text-gray-900">Total</th>
                                    <th class="pb-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @foreach($cart as $id => $details)
                                    @php $total += $details['price'] * $details['quantity']; @endphp
                                    <tr class="border-b border-gray-100">
                                        <td class="py-6 flex items-center gap-4">
                                            @php
                                                $imgSrc = (str_starts_with($details['image'], 'http') || file_exists(public_path('storage/' . $details['image']))) 
                                                    ? (str_starts_with($details['image'], 'http') ? $details['image'] : asset('storage/' . $details['image'])) 
                                                    : asset('images/product-placeholder.jpg');
                                            @endphp
                                            <img src="{{ $imgSrc }}" class="w-20 h-24 object-cover rounded" alt="{{ $details['name'] }}">
                                            <span class="font-medium text-gray-900">{{ $details['name'] }}</span>
                                        </td>
                                        <td class="py-6 text-gray-600">₹{{ number_format($details['price'], 2) }}</td>
                                        <td class="py-6 text-gray-600">{{ $details['quantity'] }}</td>
                                        <td class="py-6 font-semibold text-gray-900">₹{{ number_format($details['price'] * $details['quantity'], 2) }}</td>
                                        <td class="py-6 text-right">
                                            <form action="{{ route('cart.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $id }}">
                                                <button type="submit" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="w-full lg:w-1/3">
                        <div class="bg-gray-50 p-8 rounded-lg border border-gray-200">
                            <h2 class="font-outfit text-xl font-bold mb-6">Order Summary</h2>
                            <div class="flex justify-between mb-4 text-gray-600">
                                <span>Subtotal</span>
                                <span>₹{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between mb-6 text-gray-600 border-b border-gray-200 pb-6">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="flex justify-between mb-8 text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span>₹{{ number_format($total, 2) }}</span>
                            </div>
                            <a href="{{ route('checkout.address') }}" class="block w-full bg-black text-white text-center py-4 rounded-full font-bold hover:bg-gray-800 transition tracking-wider uppercase text-sm">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="py-20 text-center max-w-lg mx-auto">
                    <i class="fa-solid fa-cart-shopping text-4xl text-gray-300 mb-6 block"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Your cart is empty</h3>
                    <p class="text-gray-500 mb-8">Looks like you haven't added anything to your cart yet.</p>
                    <a href="{{ route('home') }}" class="inline-block bg-black text-white px-8 py-3 rounded-full font-medium hover:bg-gray-800 transition">Continue Shopping</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
