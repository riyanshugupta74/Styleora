<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - STYLEORA</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #4b5563;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        .sidebar-link:hover {
            background-color: #f3f4f6;
            color: #111827;
        }
        .sidebar-link.active {
            background-color: #fff1f2;
            color: #e11d48;
        }
        .sidebar-link i { width: 1.25rem; text-align: center; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ sidebarOpen: true }">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="bg-white w-64 border-r border-gray-200 flex-shrink-0 flex flex-col h-full transition-all duration-300" :class="{'w-64': sidebarOpen, 'w-0 overflow-hidden opacity-0 md:w-20 md:opacity-100': !sidebarOpen}">
            
            <!-- Logo Area -->
            <div class="h-16 flex items-center px-6 border-b border-gray-100">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-black tracking-widest uppercase text-gray-900 overflow-hidden whitespace-nowrap" x-show="sidebarOpen">
                    STYLEORA<span class="text-[#ff3f6c]">.</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-black uppercase text-gray-900 mx-auto hidden md:block" x-show="!sidebarOpen">
                    S<span class="text-[#ff3f6c]">.</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> <span x-show="sidebarOpen">Dashboard</span>
                </a>
                
                <div class="mt-4 mb-2 px-3 text-xs font-bold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">Catalog</div>
                <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-shirt"></i> <span x-show="sidebarOpen">Products</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i> <span x-show="sidebarOpen">Categories</span>
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked"></i> <span x-show="sidebarOpen">Inventory</span>
                </a>
                
                <div class="mt-4 mb-2 px-3 text-xs font-bold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">Sales & Operations</div>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bag-shopping"></i> <span x-show="sidebarOpen">Orders</span>
                </a>
                <a href="{{ route('admin.returns.index') }}" class="sidebar-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-rotate-left"></i> <span x-show="sidebarOpen">Returns</span>
                </a>
                <a href="{{ route('admin.exchanges.index') }}" class="sidebar-link {{ request()->routeIs('admin.exchanges.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-right-left"></i> <span x-show="sidebarOpen">Exchanges</span>
                </a>
                <a href="{{ route('admin.refunds.index') }}" class="sidebar-link {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-bill-transfer"></i> <span x-show="sidebarOpen">Refunds</span>
                </a>

                <div class="mt-4 mb-2 px-3 text-xs font-bold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">Customers & Marketing</div>
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> <span x-show="sidebarOpen">Customers</span>
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fa-regular fa-star"></i> <span x-show="sidebarOpen">Reviews</span>
                </a>
                
                <div class="mt-4 mb-2 px-3 text-xs font-bold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">Storefront</div>
                <a href="{{ route('admin.banners.index') }}" class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <i class="fa-regular fa-images"></i> <span x-show="sidebarOpen">Banners & Homepage</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="mt-4 mb-2 px-3 text-xs font-bold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">System</div>
                    <a href="{{ route('admin.audit-logs') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
                        <i class="fa-solid fa-clipboard-list"></i> <span x-show="sidebarOpen">Audit Logs</span>
                    </a>
                @endif
            </div>


            <!-- User Area -->
            <div class="p-4 border-t border-gray-100 relative" x-data="{ userMenuOpen: false }">
                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-3 w-full text-left p-2 hover:bg-gray-50 rounded-md transition-colors">
                    <div class="w-8 h-8 rounded-full bg-[#ff3f6c] text-white flex items-center justify-center font-bold text-sm shrink-0">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 overflow-hidden" x-show="sidebarOpen">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </button>

                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute bottom-full left-4 right-4 mb-2 bg-white rounded-md shadow-lg border border-gray-100 overflow-hidden z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><i class="fa-regular fa-user mr-2 w-4"></i> Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-2 w-4"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full bg-gray-50 min-w-0">
            
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10 shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-900 transition-colors">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    
                    <!-- Context-Aware Admin Search -->
                    <div class="hidden sm:block relative ml-4" x-data="{
                        searchQuery: '{{ request('search') }}',
                        get searchAction() {
                            const path = window.location.pathname;
                            if (path.includes('/admin/orders')) return '{{ route('admin.orders.index') }}';
                            if (path.includes('/admin/products')) return '{{ route('admin.products.index') }}';
                            if (path.includes('/admin/customers')) return '{{ route('admin.customers.index') }}';
                            if (path.includes('/admin/categories')) return '{{ route('admin.categories.index') }}';
                            if (path.includes('/admin/reviews')) return '{{ route('admin.reviews.index') }}';
                            if (path.includes('/admin/inventory')) return '{{ route('admin.inventory.index') }}';
                            if (path.includes('/admin/banners')) return '{{ route('admin.banners.index') }}';
                            return '{{ route('admin.orders.index') }}';
                        },
                        get placeholder() {
                            const path = window.location.pathname;
                            if (path.includes('/admin/orders')) return 'Search orders by ID, customer, product...';
                            if (path.includes('/admin/products')) return 'Search products by name, SKU, brand...';
                            if (path.includes('/admin/customers')) return 'Search customers by name, email, phone...';
                            if (path.includes('/admin/categories')) return 'Search categories...';
                            if (path.includes('/admin/reviews')) return 'Search reviews by product, customer...';
                            if (path.includes('/admin/inventory')) return 'Search inventory by product, SKU...';
                            return 'Search orders, products, customers...';
                        },
                        submitSearch() {
                            if (this.searchQuery.trim()) {
                                window.location.href = this.searchAction + '?search=' + encodeURIComponent(this.searchQuery.trim());
                            } else {
                                window.location.href = this.searchAction;
                            }
                        },
                        clearSearch() {
                            this.searchQuery = '';
                            window.location.href = this.searchAction;
                        }
                    }">
                        <form @submit.prevent="submitSearch()" class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="searchQuery" :placeholder="placeholder" class="pl-10 pr-20 py-2 border border-gray-200 rounded-md text-sm bg-gray-50 focus:bg-white focus:border-[#ff3f6c] focus:ring-1 focus:ring-[#ff3f6c] w-64 md:w-96 transition-all">
                            <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                <button type="button" x-show="searchQuery.length > 0" @click="clearSearch()" class="text-gray-400 hover:text-gray-600 p-1" x-cloak>
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                                <button type="submit" class="bg-gray-900 text-white px-2.5 py-1 rounded text-xs font-bold hover:bg-[#ff3f6c] transition-colors">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="relative text-gray-400 hover:text-gray-900 transition-colors">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#ff3f6c] text-white text-[9px] font-bold flex items-center justify-center rounded-full border-2 border-white">3</span>
                    </button>
                    <a href="{{ route('home') }}" target="_blank" class="text-sm font-bold text-[#ff3f6c] hover:text-[#e02e5c] transition-colors flex items-center gap-2 px-3 py-1.5 rounded-md border border-[#ff3f6c]/20 hover:border-[#ff3f6c]/40 bg-[#ff3f6c]/5">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Storefront
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-50 text-green-700 text-sm font-bold p-4 rounded-md mb-6 border border-green-200 flex items-start gap-3 shadow-sm">
                        <i class="fa-solid fa-circle-check mt-0.5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 text-red-700 text-sm font-bold p-4 rounded-md mb-6 border border-red-200 flex items-start gap-3 shadow-sm">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

    </div>

    @stack('scripts')
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
    </style>
</body>
</html>
