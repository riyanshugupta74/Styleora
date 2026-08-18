<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-black uppercase tracking-tight" style="font-family: 'Outfit', sans-serif;">Create Account</h2>
        <p class="text-gray-500 text-xs mt-1">Join STYLEORA and elevate your wardrobe.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" autoComplete="off" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-gray-900 uppercase tracking-wider mb-1.5">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="off" 
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors" 
                   placeholder="John Doe">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-red-600 text-xs font-medium" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-gray-900 uppercase tracking-wider mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="off" 
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors" 
                   placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-red-600 text-xs font-medium" />
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
            <label for="password" class="block text-xs font-bold text-gray-900 uppercase tracking-wider mb-1.5">Password</label>
            <div class="relative">
                <input id="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors pr-10" 
                       placeholder="••••••••">
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-black focus:outline-none transition-colors">
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-red-600 text-xs font-medium" />
        </div>

        <!-- Confirm Password -->
        <div x-data="{ showPassword: false }">
            <label for="password_confirmation" class="block text-xs font-bold text-gray-900 uppercase tracking-wider mb-1.5">Confirm Password</label>
            <div class="relative">
                <input id="password_confirmation" x-bind:type="showPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors pr-10" 
                       placeholder="••••••••">
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-black focus:outline-none transition-colors">
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-red-600 text-xs font-medium" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-black hover:bg-gray-900 text-white font-bold text-sm py-3.5 rounded-lg tracking-wider uppercase transition-colors shadow-sm">
                Create Account
            </button>
        </div>
    </form>

    <div class="mt-8 text-center border-t border-gray-100 pt-6">
        <p class="text-xs text-gray-600">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-black hover:underline transition-all">
                Sign in
            </a>
        </p>
    </div>
</x-guest-layout>
