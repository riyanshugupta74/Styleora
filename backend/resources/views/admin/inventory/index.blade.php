@extends('layouts.admin')
@section('title', 'Inventory Management')
@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Inventory</h1>
        <p class="text-gray-500 text-sm mt-1">Real-time SKU-level stock management.</p>
    </div>
    <div class="flex items-center gap-3 text-sm">
        <a href="{{ route('admin.inventory.index', ['filter' => 'low_stock']) }}" class="px-4 py-2 rounded-md font-bold border {{ request('filter') == 'low_stock' ? 'bg-orange-100 text-orange-700 border-orange-200' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Low Stock
        </a>
        <a href="{{ route('admin.inventory.index', ['filter' => 'out_of_stock']) }}" class="px-4 py-2 rounded-md font-bold border {{ request('filter') == 'out_of_stock' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
            <i class="fa-solid fa-circle-xmark mr-1"></i> Out of Stock
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 rounded-md font-bold border bg-white text-gray-700 border-gray-200 hover:bg-gray-50">All</a>
    </div>
</div>

<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('admin.inventory.index') }}" method="GET" class="flex gap-3">
        @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by SKU or product name..." class="flex-1 px-4 py-2 border border-gray-200 rounded-md text-sm focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-bold text-sm">Search</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <tr>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4">Color</th>
                    <th class="px-6 py-4">Size</th>
                    <th class="px-6 py-4">Current Stock</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Adjust</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($variants as $variant)
                <tr class="hover:bg-gray-50" x-data="{ adjustOpen: false }">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900 truncate max-w-[180px]" title="{{ $variant->product->name }}">{{ $variant->product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $variant->product->brand?->name }}</p>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $variant->sku ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        @if($variant->color)
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full border border-gray-200" style="background-color:{{ $variant->color->hex_code }}"></span>
                                {{ $variant->color->name }}
                            </span>
                        @else <span class="text-gray-400">—</span> @endif
                    </td>
                    <td class="px-6 py-4 font-medium">{{ $variant->size?->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-lg {{ $variant->stock == 0 ? 'text-red-600' : ($variant->stock < 10 ? 'text-orange-500' : 'text-green-600') }}">
                            {{ $variant->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($variant->stock == 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Out of Stock</span>
                        @elseif($variant->stock < 10)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">Low Stock</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">In Stock</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button @click="adjustOpen = true" class="text-[#ff3f6c] hover:text-[#e02e5c] font-bold text-sm"><i class="fa-solid fa-sliders mr-1"></i>Adjust</button>

                        <!-- Adjust Modal -->
                        <div x-show="adjustOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
                            <div class="fixed inset-0 bg-black/40" @click="adjustOpen = false"></div>
                            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Adjust Stock</h3>
                                <p class="text-sm text-gray-500 mb-4">{{ $variant->product->name }} — {{ $variant->color?->name }} / {{ $variant->size?->name }}</p>
                                <div class="bg-gray-50 rounded-lg p-3 mb-4 flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Current Stock</span>
                                    <span class="font-bold text-lg text-gray-900">{{ $variant->stock }}</span>
                                </div>
                                <form action="{{ route('admin.inventory.adjust', $variant->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Adjustment (use + or - numbers)</label>
                                        <input type="number" name="adjustment" required placeholder="e.g. +10 or -5" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] text-lg font-bold text-center">
                                        <p class="text-xs text-gray-500 mt-1">Enter a positive number to add stock, negative to remove.</p>
                                    </div>
                                    <div class="mb-6">
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Reason *</label>
                                        <select name="reason" required class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
                                            <option value="">Select reason...</option>
                                            <option value="New stock received">New stock received</option>
                                            <option value="Damaged/defective">Damaged/defective removal</option>
                                            <option value="Return received">Return received</option>
                                            <option value="Manual correction">Manual correction</option>
                                            <option value="Inventory audit">Inventory audit</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="adjustOpen = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-bold text-gray-700">Cancel</button>
                                        <button type="submit" class="px-4 py-2 bg-[#ff3f6c] text-white rounded-md text-sm font-bold">Apply Adjustment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <i class="fa-solid fa-boxes-stacked text-4xl text-gray-300 mb-3 block"></i>
                    <p class="font-medium">No inventory records found.</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($variants->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $variants->links() }}</div>
    @endif
</div>
@endsection
