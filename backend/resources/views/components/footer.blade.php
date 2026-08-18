<footer class="bg-white border-t border-gray-200 pt-16 pb-8 mt-16">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            
            <!-- Company Info -->
            <div>
                <h3 class="font-outfit font-bold text-xl tracking-widest text-black mb-6">STYLEORA</h3>
                <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                    Your Style. Your Way. Discover the latest trends in fashion and explore a wide range of premium clothing designed for you.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white transition-colors">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white transition-colors">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white transition-colors">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white transition-colors">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Customer Service -->
            <div>
                <h4 class="font-bold text-sm tracking-wider uppercase mb-6">Customer Service</h4>
                <ul class="space-y-4">
                    <li><a href="{{ route('contact') }}" class="text-sm text-gray-500 hover:text-black transition">Contact Us</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-black transition">FAQ</a></li>
                    <li><a href="{{ route('track.order') }}" class="text-sm text-gray-500 hover:text-black transition">Track Order</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-black transition">Return Policy</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-black transition">Shipping Info</a></li>
                </ul>
            </div>

            <!-- About Us -->
            <div>
                <h4 class="font-bold text-sm tracking-wider uppercase mb-6">About Us</h4>
                <ul class="space-y-4">
                    <li><a href="#" class="text-sm text-gray-500 hover:text-black transition">Our Story</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-black transition">Careers</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-black transition">Privacy Policy</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-black transition">Terms & Conditions</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div>
                <h4 class="font-bold text-sm tracking-wider uppercase mb-6">Subscribe</h4>
                <p class="text-sm text-gray-500 mb-4">Be the first to know about new arrivals, sales & promos!</p>
                <form action="#" class="space-y-3">
                    <input type="email" placeholder="Your Email Address" required class="w-full bg-gray-50 border border-gray-200 px-4 py-3 text-sm focus:border-black focus:ring-0 outline-none transition">
                    <button type="submit" class="w-full bg-black text-white px-4 py-3 text-sm font-bold uppercase tracking-wider hover:bg-gray-800 transition">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-xs text-gray-500 mb-4 md:mb-0">
                &copy; {{ date('Y') }} STYLEORA. All Rights Reserved.
            </p>
            <div class="flex space-x-3 text-2xl text-gray-300">
                <i class="fa-brands fa-cc-visa hover:text-gray-600 transition"></i>
                <i class="fa-brands fa-cc-mastercard hover:text-gray-600 transition"></i>
                <i class="fa-brands fa-cc-paypal hover:text-gray-600 transition"></i>
                <i class="fa-brands fa-cc-amex hover:text-gray-600 transition"></i>
            </div>
        </div>
    </div>
</footer>
