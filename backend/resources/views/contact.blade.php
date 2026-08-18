<x-app-layout>
    @section('title', 'STYLEORA | Customer Service')

    <div class="bg-white min-h-screen pt-8 pb-20">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-bold text-gray-500 mb-8 uppercase tracking-wider">
                <a href="{{ route('home') }}" class="hover:text-black">Home</a>
                <span class="mx-2">/</span>
                <span class="text-black">Customer Service</span>
            </nav>

            <div class="max-w-4xl mx-auto">
                <h1 class="font-outfit text-3xl md:text-4xl font-black uppercase tracking-wider text-gray-900 mb-4 text-center">Customer Service</h1>
                <p class="text-gray-500 text-center mb-12 text-lg">We're here to help! Reach out to us through any of the channels below.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <!-- Email -->
                    <div class="bg-gray-50 rounded-xl p-8 text-center hover:shadow-lg transition-shadow border border-gray-100">
                        <div class="w-16 h-16 bg-[#ff3f6c]/10 rounded-full flex items-center justify-center mx-auto mb-5">
                            <i class="fa-solid fa-envelope text-2xl text-[#ff3f6c]"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Email Us</h3>
                        <p class="text-gray-500 text-sm mb-4">We'll respond within 24 hours</p>
                        <a href="mailto:support@styleora.com" class="text-[#ff3f6c] font-bold text-sm hover:underline">support@styleora.com</a>
                    </div>

                    <!-- Phone -->
                    <div class="bg-gray-50 rounded-xl p-8 text-center hover:shadow-lg transition-shadow border border-gray-100">
                        <div class="w-16 h-16 bg-[#ff3f6c]/10 rounded-full flex items-center justify-center mx-auto mb-5">
                            <i class="fa-solid fa-phone text-2xl text-[#ff3f6c]"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Call Us</h3>
                        <p class="text-gray-500 text-sm mb-4">Mon-Sat, 9AM - 9PM IST</p>
                        <a href="tel:+911800123456" class="text-[#ff3f6c] font-bold text-sm hover:underline">1800-123-456</a>
                    </div>

                    <!-- Live Chat -->
                    <div class="bg-gray-50 rounded-xl p-8 text-center hover:shadow-lg transition-shadow border border-gray-100">
                        <div class="w-16 h-16 bg-[#ff3f6c]/10 rounded-full flex items-center justify-center mx-auto mb-5">
                            <i class="fa-solid fa-comments text-2xl text-[#ff3f6c]"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Live Chat</h3>
                        <p class="text-gray-500 text-sm mb-4">Available 24/7</p>
                        <span class="text-[#ff3f6c] font-bold text-sm">Coming Soon</span>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="mb-16">
                    <h2 class="text-xl font-black uppercase tracking-widest text-gray-900 mb-8 flex items-center gap-4">
                        <span class="w-2 h-8 bg-[#ff3f6c]"></span> Frequently Asked Questions
                    </h2>

                    <div class="space-y-4" x-data="{ openFaq: null }">
                        @php
                            $faqs = [
                                ['q' => 'How can I track my order?', 'a' => 'You can track your order by visiting the "Track Order" page from the top navigation bar. Enter your order ID and email to get real-time updates on your delivery status.'],
                                ['q' => 'What is your return policy?', 'a' => 'We offer a 30-day easy return policy. If you\'re not satisfied with your purchase, you can initiate a return from your "My Orders" page. Items must be in original condition with tags attached.'],
                                ['q' => 'How long does delivery take?', 'a' => 'Standard delivery takes 5-7 business days. Express delivery (available in select cities) takes 2-3 business days. Free shipping on orders above ₹999.'],
                                ['q' => 'Can I cancel my order?', 'a' => 'You can cancel your order before it is shipped. Go to "My Orders", select the order, and click "Cancel". Refunds are processed within 5-7 business days.'],
                                ['q' => 'What payment methods do you accept?', 'a' => 'We accept Credit/Debit Cards (Visa, Mastercard, RuPay), UPI, Net Banking, Wallets (PayTM, PhonePe), and Cash on Delivery.'],
                            ];
                        @endphp

                        @foreach($faqs as $i => $faq)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button @click="openFaq === {{ $i }} ? openFaq = null : openFaq = {{ $i }}" class="w-full text-left px-6 py-4 flex items-center justify-between bg-white hover:bg-gray-50 transition-colors">
                                <span class="font-bold text-sm text-gray-900">{{ $faq['q'] }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="openFaq === {{ $i }} ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="openFaq === {{ $i }}" x-collapse x-cloak class="px-6 pb-4 text-sm text-gray-600 leading-relaxed">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Support Hours -->
                <div class="bg-gray-900 rounded-xl p-8 md:p-12 text-white text-center">
                    <h3 class="font-outfit text-2xl font-bold mb-4">Need More Help?</h3>
                    <p class="text-gray-300 mb-6">Our customer support team is available to assist you.</p>
                    <div class="flex flex-wrap justify-center gap-8 text-sm">
                        <div>
                            <p class="font-bold text-white mb-1">Monday - Saturday</p>
                            <p class="text-gray-400">9:00 AM - 9:00 PM IST</p>
                        </div>
                        <div>
                            <p class="font-bold text-white mb-1">Sunday</p>
                            <p class="text-gray-400">10:00 AM - 6:00 PM IST</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
