@extends('layouts.admin')
@section('title', 'Reviews Management')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900">Reviews</h1><p class="text-gray-500 text-sm mt-1">Moderate customer product reviews.</p></div>

<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 flex gap-3 flex-wrap">
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex gap-3 flex-1 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product or customer..." class="flex-1 min-w-[200px] px-4 py-2 border border-gray-200 rounded-md text-sm focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
        <select name="rating" class="px-3 py-2 border border-gray-200 rounded-md text-sm bg-white">
            <option value="">All Ratings</option>
            @foreach([5,4,3,2,1] as $r)
                <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} ★</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-md text-sm bg-white">
            <option value="">All Statuses</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
            <option value="hidden" {{ request('status')=='hidden'?'selected':'' }}>Hidden</option>
            <option value="flagged" {{ request('status')=='flagged'?'selected':'' }}>Flagged</option>
        </select>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-bold text-sm">Filter</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <tr>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">Rating</th>
                    <th class="px-6 py-4">Review</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $review->user?->name ?? 'Deleted User' }}</td>
                    <td class="px-6 py-4 text-gray-700 max-w-[150px] truncate" title="{{ $review->product?->name }}">{{ $review->product?->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            @for($i=1; $i<=5; $i++)
                                <i class="fa-star {{ $i <= $review->rating ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }} text-xs"></i>
                            @endfor
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 max-w-[200px]">{{ Str::limit($review->comment, 60) }}</td>
                    <td class="px-6 py-4">
                        @php $color = match($review->status ?? 'approved') { 'approved'=>'green','hidden'=>'gray','flagged'=>'red', default=>'gray' }; @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $color }}-100 text-{{ $color }}-800">{{ ucfirst($review->status ?? 'approved') }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $review->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <form action="{{ route('admin.reviews.status', $review->id) }}" method="POST" class="inline">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-200 rounded px-2 py-1 bg-white focus:ring-[#ff3f6c]">
                                    <option value="approved" {{ ($review->status ?? 'approved') == 'approved' ? 'selected' : '' }}>Approve</option>
                                    <option value="hidden" {{ $review->status == 'hidden' ? 'selected' : '' }}>Hide</option>
                                    <option value="flagged" {{ $review->status == 'flagged' ? 'selected' : '' }}>Flag</option>
                                </select>
                            </form>
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this review? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600" title="Delete"><i class="fa-solid fa-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <i class="fa-solid fa-star text-4xl text-gray-300 mb-3 block"></i>
                    <p class="font-medium">No reviews found.</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())<div class="px-6 py-4 border-t border-gray-100">{{ $reviews->links() }}</div>@endif
</div>
@endsection
