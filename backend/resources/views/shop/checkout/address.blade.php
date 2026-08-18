<x-app-layout>
    @section('title', 'STYLEORA | Checkout - Address')

    <div class="bg-gray-50 min-h-screen pt-8 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Checkout Progress -->
            <div class="flex items-center justify-center mb-12">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold">1</div>
                    <span class="ml-3 font-bold text-gray-900">Address</span>
                </div>
                <div class="w-16 h-px bg-gray-300 mx-4"></div>
                <div class="flex items-center opacity-50">
                    <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold">2</div>
                    <span class="ml-3 font-bold text-gray-600">Payment</span>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100">
                    <h2 class="text-2xl font-outfit font-bold text-gray-900">Shipping Address</h2>
                    <p class="text-gray-500 text-sm mt-1">Where should we deliver your order?</p>
                </div>

                <div class="p-8">
                    @if($addresses->count() > 0)
                        <div class="mb-10">
                            <h3 class="font-bold text-gray-900 mb-4 uppercase tracking-wider text-sm">Saved Addresses</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($addresses as $addr)
                                    <form action="{{ route('checkout.address.process') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="address_id" value="{{ $addr->id }}">
                                        <button type="submit" class="w-full text-left p-4 border-2 border-gray-200 rounded-lg hover:border-black transition-colors focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
                                            <p class="font-bold text-gray-900 mb-1">{{ $addr->full_name }} <span class="text-sm font-normal text-gray-500 ml-2">{{ $addr->phone }}</span></p>
                                            <p class="text-sm text-gray-600 line-clamp-2">{{ $addr->address_line_1 }}, {{ $addr->address_line_2 }}</p>
                                            <p class="text-sm text-gray-600">{{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}</p>
                                            @if($addr->is_default)
                                                <span class="inline-block mt-2 text-xs font-bold bg-gray-100 px-2 py-1 rounded">DEFAULT</span>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                            
                            <div class="mt-8 flex items-center">
                                <div class="flex-grow h-px bg-gray-200"></div>
                                <span class="px-4 text-sm text-gray-400 font-medium uppercase">Or Add New Address</span>
                                <div class="flex-grow h-px bg-gray-200"></div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('checkout.address.process') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" name="full_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" value="{{ old('full_name') }}">
                                @error('full_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                                <input type="text" name="phone" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" value="{{ old('phone') }}">
                                @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 (Flat, House no., Building)</label>
                                <input type="text" name="address_line_1" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" value="{{ old('address_line_1') }}">
                                @error('address_line_1') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Area, Street, Sector, Village) - Optional</label>
                                <input type="text" name="address_line_2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" value="{{ old('address_line_2') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City / Town</label>
                                <input type="text" name="city" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" value="{{ old('city') }}">
                                @error('city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <input type="text" name="state" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" value="{{ old('state') }}">
                                @error('state') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                                <input type="text" name="pincode" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" value="{{ old('pincode') }}">
                                @error('pincode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end">
                            <button type="submit" class="bg-black text-white px-8 py-3 rounded-full font-bold uppercase tracking-wider text-sm hover:bg-gray-800 transition-colors shadow-md">Continue to Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
