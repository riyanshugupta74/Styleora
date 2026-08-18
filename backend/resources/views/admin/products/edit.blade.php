@extends('layouts.admin')
@section('title', 'Edit Product')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            Edit Product: {{ $product->name }}
        </h1>
        <p class="text-gray-500 text-sm mt-1 ml-9">Update details for this product.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="p-6 md:p-8">
        @csrf
        @method('PUT')
        
        @if ($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-md mb-6 border border-red-200">
                <ul class="list-disc pl-5 text-sm font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Details -->
            <div class="md:col-span-2 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Basic Details</h3>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] transition-colors">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">SKU <span class="text-red-500">*</span></label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Brand</label>
                        <select name="brand_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] transition-colors bg-white">
                            <option value="">Select a brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] transition-colors bg-white">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="md:col-span-2 space-y-4 mt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Pricing & Inventory</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Price (₹) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Discount Price (₹)</label>
                        <input type="number" step="0.01" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Total Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] transition-colors">
                    </div>
                </div>
            </div>

            <!-- Description & Status -->
            <div class="md:col-span-2 space-y-4 mt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Description & Settings</h3>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="5" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c] transition-colors">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="status" value="1" {{ old('status', $product->status) == '1' ? 'checked' : '' }} class="text-[#ff3f6c] focus:ring-[#ff3f6c]">
                            <span class="text-sm font-medium text-gray-900">Active</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="status" value="0" {{ old('status', $product->status) == '0' ? 'checked' : '' }} class="text-[#ff3f6c] focus:ring-[#ff3f6c]">
                            <span class="text-sm font-medium text-gray-900">Inactive</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-bold text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#ff3f6c] text-white rounded-md font-bold text-sm hover:bg-[#e02e5c] shadow-sm transition-colors flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Update Product
            </button>
        </div>
    </form>
</div>
@endsection
