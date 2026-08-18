@extends('layouts.admin')
@section('title', 'Manage Products')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>
        <p class="text-gray-500 text-sm mt-1">Manage your catalog, inventory, and pricing.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="bg-[#ff3f6c] hover:bg-[#e02e5c] text-white px-4 py-2 rounded-md font-bold text-sm transition-colors flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-plus"></i> Add Product
    </a>
</div>

<!-- Filters & Search -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
    <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1">
        <div class="relative w-full md:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-md text-sm focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
        </div>
        <select name="status" class="w-full md:w-48 px-3 py-2 border border-gray-200 rounded-md text-sm focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
            <option value="">All Statuses</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-bold text-sm transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center px-2">Clear</a>
        @endif
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider font-bold">
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4">Price</th>
                    <th class="px-6 py-4">Stock</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-16 bg-gray-100 rounded overflow-hidden shrink-0">
                                @if($product->images && $product->images->count() > 0)
                                    <img src="{{ $product->images->first()->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Img</div>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 truncate max-w-[200px]" title="{{ $product->name }}">{{ $product->name }}</p>
                                <p class="text-gray-500 text-xs mt-0.5">SKU: {{ $product->sku }}</p>
                                <p class="text-gray-400 text-[10px] mt-0.5">{{ $product->brand ? $product->brand->name : 'No Brand' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-800">
                            {{ $product->category ? $product->category->name : 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($product->discount_price)
                            <p class="font-bold text-gray-900">₹{{ number_format($product->discount_price, 2) }}</p>
                            <p class="text-xs text-gray-500 line-through">₹{{ number_format($product->price, 2) }}</p>
                        @else
                            <p class="font-bold text-gray-900">₹{{ number_format($product->price, 2) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-orange-500' : 'text-red-600') }}">
                                {{ $product->stock }} units
                            </span>
                            <span class="text-[10px] text-gray-400">{{ $product->variants->count() }} Variants</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($product->status == 1)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="p-2 text-gray-400 hover:text-[#ff3f6c] transition-colors" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.products.toggle', $product->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-gray-900 transition-colors" title="{{ $product->status == 1 ? 'Deactivate' : 'Activate' }}">
                                    @if($product->status == 1)
                                        <i class="fa-solid fa-eye-slash"></i>
                                    @else
                                        <i class="fa-solid fa-eye"></i>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fa-solid fa-shirt text-4xl text-gray-300 mb-3"></i>
                            <p class="text-lg font-medium text-gray-900">No products found</p>
                            <p class="text-sm mt-1">Try adjusting your filters or add a new product.</p>
                            <a href="{{ route('admin.products.create') }}" class="mt-4 text-[#ff3f6c] hover:underline font-bold text-sm">Add New Product</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
