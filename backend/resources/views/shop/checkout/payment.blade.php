<x-app-layout>
    @section('title', 'STYLEORA | Checkout - Payment')

    <div class="bg-gray-50 min-h-screen pt-8 pb-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Checkout Progress -->
            <div class="flex items-center justify-center mb-12">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-[#20bb79] text-white rounded-full flex items-center justify-center font-bold"><i class="fa-solid fa-check"></i></div>
                    <span class="ml-3 font-bold text-[#20bb79]">Address</span>
                </div>
                <div class="w-16 h-px bg-[#20bb79] mx-4"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold">2</div>
                    <span class="ml-3 font-bold text-gray-900">Payment</span>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Payment Options -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-100">
                            <h2 class="text-2xl font-outfit font-bold text-gray-900">Payment Method</h2>
                            <p class="text-gray-500 text-sm mt-1">Select how you want to pay</p>
                        </div>

                        <div class="p-8">
                            <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                                @csrf
                                <div class="space-y-4">
                                    
                                    <!-- UPI Option -->
                                    <label class="block relative border-2 border-gray-200 rounded-lg p-5 cursor-pointer hover:border-gray-300 transition-colors has-[:checked]:border-black has-[:checked]:bg-gray-50">
                                        <div class="flex items-center">
                                            <input type="radio" name="payment_method" value="upi" class="h-4 w-4 text-black focus:ring-black border-gray-300" required>
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-gray-900 block">UPI (Google Pay, PhonePe, Paytm)</span>
                                                    <i class="fa-solid fa-mobile-screen text-xl text-gray-400"></i>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">Pay instantly using any UPI app</p>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Credit/Debit Card Option -->
                                    <label class="block relative border-2 border-gray-200 rounded-lg p-5 cursor-pointer hover:border-gray-300 transition-colors has-[:checked]:border-black has-[:checked]:bg-gray-50">
                                        <div class="flex items-center">
                                            <input type="radio" name="payment_method" value="card" class="h-4 w-4 text-black focus:ring-black border-gray-300" required>
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-gray-900 block">Credit / Debit Card</span>
                                                    <div class="flex gap-2">
                                                        <i class="fa-brands fa-cc-visa text-xl text-gray-400"></i>
                                                        <i class="fa-brands fa-cc-mastercard text-xl text-gray-400"></i>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">Pay securely with your bank card</p>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- COD Option -->
                                    <label class="block relative border-2 border-gray-200 rounded-lg p-5 cursor-pointer hover:border-gray-300 transition-colors has-[:checked]:border-black has-[:checked]:bg-gray-50">
                                        <div class="flex items-center">
                                            <input type="radio" name="payment_method" value="cod" class="h-4 w-4 text-black focus:ring-black border-gray-300" required>
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-gray-900 block">Cash on Delivery</span>
                                                    <i class="fa-solid fa-money-bill-wave text-xl text-gray-400"></i>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">Pay in cash when your order arrives</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="mt-8 text-sm text-gray-500 flex items-center justify-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <i class="fa-solid fa-shield-halved text-gray-400 mr-2 text-lg"></i>
                                    Payments are 100% secure and encrypted.
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-8">
                        <h3 class="font-outfit text-xl font-bold mb-6 text-gray-900 border-b border-gray-100 pb-4">Order Summary</h3>
                        
                        <div class="space-y-4 mb-6 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Cart Total</span>
                                <span>₹{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="text-[#20bb79] font-medium">FREE</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Discount</span>
                                <span>-₹0.00</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 mb-8">
                            <div class="flex justify-between items-center text-lg font-bold text-gray-900">
                                <span>Total Amount</span>
                                <span>₹{{ number_format($total, 2) }}</span>
                            </div>
                            <p class="text-xs text-teal-600 font-bold mt-1 text-right">inclusive of all taxes</p>
                        </div>

                        <button onclick="document.getElementById('payment-form').submit()" class="w-full bg-[#ff3f6c] text-white py-4 rounded-full font-bold hover:bg-[#ed3a64] transition-colors shadow-md tracking-wider uppercase text-sm flex items-center justify-center">
                            <i class="fa-solid fa-lock mr-2"></i> Pay & Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
