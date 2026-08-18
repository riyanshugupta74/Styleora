<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'STYLEORA'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700|outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="font-inter text-gray-900 antialiased bg-[#f9fafb] bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-white via-[#f9fafb] to-gray-100 flex flex-col min-h-screen selection:bg-black selection:text-white">
        
        <x-header />

        <!-- Main Content -->
        <main class="flex-grow pt-[116px]"> <!-- padding top to account for sticky header -->
            {{ $slot }}
        </main>
        
        <x-footer />
        
        @stack('scripts')

        <!-- Toast Notification System -->
        <div x-data="{ 
                show: false, 
                message: '', 
                type: 'success',
                init() {
                    window.addEventListener('notify', (e) => {
                        this.message = e.detail.message;
                        this.type = e.detail.type;
                        this.show = true;
                        setTimeout(() => { this.show = false }, 3000);
                    });
                    
                    @if(session()->has('success'))
                        this.message = '{{ session('success') }}';
                        this.type = 'success';
                        this.show = true;
                        setTimeout(() => { this.show = false }, 3000);
                    @endif
                    
                    @if(session()->has('error'))
                        this.message = '{{ session('error') }}';
                        this.type = 'error';
                        this.show = true;
                        setTimeout(() => { this.show = false }, 3000);
                    @endif
                }
            }"
            x-show="show" 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-10"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
            class="fixed bottom-5 right-5 z-[100] px-6 py-4 rounded-lg shadow-lg text-white font-bold flex items-center gap-3"
            :class="type === 'success' ? 'bg-[#20bb79]' : 'bg-red-500'"
        >
            <i class="fa-solid" :class="type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            <span x-text="message"></span>
        </div>
    </body>
</html>
