@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <p class="text-gray-500 text-sm mt-1">Manage product categories and their status.</p>
    </div>
    <button onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="bg-[#ff3f6c] hover:bg-[#e02e5c] text-white px-4 py-2 rounded-md font-bold text-sm flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-plus"></i> Add Category
    </button>
</div>

<!-- Search -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('admin.categories.index') }}" method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..." class="flex-1 px-4 py-2 border border-gray-200 rounded-md text-sm focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-bold text-sm">Search</button>
        @if(request('search')) <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 flex items-center">Clear</a> @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
            <tr>
                <th class="px-6 py-4">Category Name</th>
                <th class="px-6 py-4">Slug</th>
                <th class="px-6 py-4">Products</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-100">
            @forelse($categories as $cat)
            <tr class="hover:bg-gray-50 transition-colors" x-data="{ editOpen: false }">
                <td class="px-6 py-4 font-bold text-gray-900">{{ $cat->name }}</td>
                <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $cat->slug }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                        {{ $cat->products_count }} products
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($cat->status == 1)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button @click="editOpen = true" class="p-2 text-gray-400 hover:text-[#ff3f6c] transition-colors" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                        <form action="{{ route('admin.categories.toggle', $cat->id) }}" method="POST" class="inline">@csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-gray-900" title="Toggle Status">
                                <i class="fa-solid {{ $cat->status == 1 ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            </button>
                        </form>
                        @if($cat->products_count == 0)
                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete category \'{{ $cat->name }}\'? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                        @endif
                    </div>

                    <!-- Edit Modal (inline per row) -->
                    <div x-show="editOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
                        <div class="fixed inset-0 bg-black/40" @click="editOpen = false"></div>
                        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Category</h3>
                            <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Category Name</label>
                                    <input type="text" name="name" value="{{ $cat->name }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
                                </div>
                                <div class="mb-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
                                        <option value="1" {{ $cat->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $cat->status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="editOpen = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-bold text-gray-700">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-[#ff3f6c] text-white rounded-md text-sm font-bold">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">
                <i class="fa-solid fa-tags text-4xl text-gray-300 mb-3 block"></i>
                <p class="font-medium">No categories found.</p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    @if($categories->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $categories->links() }}</div>
    @endif
</div>

<!-- Add Category Modal -->
<div id="add-category-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/40" onclick="document.getElementById('add-category-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Category</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. Men's T-Shirts" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('add-category-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-bold text-gray-700">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#ff3f6c] text-white rounded-md text-sm font-bold">Create Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
