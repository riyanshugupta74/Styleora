<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine JS for password toggle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.0/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="font-sans antialiased text-black bg-gray-50 selection:bg-black selection:text-white">
    <div class="min-h-screen flex flex-col justify-center items-center relative py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Back to Home -->
        <div class="absolute top-6 right-6 sm:top-8 sm:right-8">
            <a href="/" class="text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-black flex items-center gap-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Storefront
            </a>
        </div>

        <!-- Logo -->
        <div class="mb-8 text-center sm:mx-auto sm:w-full sm:max-w-md">
            <a href="/" class="text-3xl font-bold tracking-widest text-black uppercase hover:opacity-80 transition-opacity" style="font-family: 'Outfit', sans-serif;">
                STYLEORA
            </a>
        </div>

        <!-- Form Card -->
        <div class="w-full sm:max-w-md p-8 bg-white border border-gray-200 rounded-2xl shadow-sm">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
