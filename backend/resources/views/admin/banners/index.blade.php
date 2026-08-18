@extends('layouts.admin')
@section('title', 'Homepage Banners')
@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Homepage Banners</h1>
        <p class="text-gray-500 text-sm mt-1">Manage hero and promotional banners on the customer homepage.</p>
    </div>
    <button onclick="document.getElementById('add-banner-modal').classList.remove('hidden')" class="bg-[#ff3f6c] hover:bg-[#e02e5c] text-white px-4 py-2 rounded-md font-bold text-sm flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-plus"></i> Add Banner
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($banners as $banner)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ editOpen: false }">
        <!-- Image Preview -->
        <div class="aspect-[16/6] bg-gray-100 relative overflow-hidden">
            @if($banner->image)
                <img src="{{ Storage::url($banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <i class="fa-regular fa-image text-4xl"></i>
                </div>
            @endif
            <div class="absolute top-2 right-2 flex gap-1">
                @if($banner->status == 1)
                    <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">LIVE</span>
                @else
                    <span class="bg-gray-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">HIDDEN</span>
                @endif
            </div>
        </div>
        <!-- Content -->
        <div class="p-4">
            <p class="font-bold text-gray-900 truncate">{{ $banner->title }}</p>
            @if($banner->subtitle)<p class="text-xs text-gray-500 mt-0.5 truncate">{{ $banner->subtitle }}</p>@endif
            @if($banner->button_url)<p class="text-xs text-[#ff3f6c] mt-1 truncate font-medium">→ {{ $banner->button_url }}</p>@endif
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-500 capitalize">{{ $banner->position }}</span>
                <div class="flex items-center gap-2">
                    <button @click="editOpen = true" class="p-1.5 text-gray-400 hover:text-[#ff3f6c]" title="Edit"><i class="fa-solid fa-pen-to-square text-sm"></i></button>
                    <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST" class="inline">@csrf
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-gray-900" title="Toggle">
                            <i class="fa-solid {{ $banner->status == 1 ? 'fa-eye-slash' : 'fa-eye' }} text-sm"></i>
                        </button>
                    </form>
                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this banner?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600" title="Delete"><i class="fa-solid fa-trash text-sm"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="editOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
            <div class="fixed inset-0 bg-black/40" @click="editOpen = false"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Banner</h3>
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" value="{{ $banner->title }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Subtitle</label>
                            <input type="text" name="subtitle" value="{{ $banner->subtitle }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Button Text</label>
                            <input type="text" name="button_text" value="{{ $banner->button_text }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Destination URL</label>
                            <input type="url" name="button_url" value="{{ $banner->button_url }}" placeholder="https://..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Position</label>
                            <select name="position" class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white">
                                <option value="hero" {{ $banner->position=='hero'?'selected':'' }}>Hero</option>
                                <option value="promo" {{ $banner->position=='promo'?'selected':'' }}>Promotional</option>
                                <option value="category" {{ $banner->position=='category'?'selected':'' }}>Category</option>
                            </select></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Replace Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white">
                                <option value="1" {{ $banner->status==1?'selected':'' }}>Active</option>
                                <option value="0" {{ $banner->status==0?'selected':'' }}>Inactive</option>
                            </select></div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="editOpen = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-bold text-gray-700">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#ff3f6c] text-white rounded-md text-sm font-bold">Save Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 py-16 text-center text-gray-500">
        <i class="fa-regular fa-images text-5xl text-gray-300 mb-3 block"></i>
        <p class="font-medium text-gray-700">No banners yet.</p>
        <p class="text-sm mt-1">Click "Add Banner" to create your first homepage banner.</p>
    </div>
    @endforelse
</div>

<!-- Add Banner Modal -->
<div id="add-banner-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/40" onclick="document.getElementById('add-banner-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Banner</h3>
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" required placeholder="e.g. End of Season Sale" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Subtitle</label>
                    <input type="text" name="subtitle" placeholder="e.g. Up to 70% off" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Button Text</label>
                    <input type="text" name="button_text" placeholder="e.g. Shop Now" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Destination URL</label>
                    <input type="url" name="button_url" placeholder="https://..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#ff3f6c] focus:border-[#ff3f6c]"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Position</label>
                    <select name="position" class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="hero">Hero Banner</option>
                        <option value="promo">Promotional</option>
                        <option value="category">Category</option>
                    </select></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Banner Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="1">Active (Live)</option>
                        <option value="0">Inactive</option>
                    </select></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('add-banner-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-bold text-gray-700">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#ff3f6c] text-white rounded-md text-sm font-bold">Create Banner</button>
            </div>
        </form>
    </div>
</div>
@endsection
