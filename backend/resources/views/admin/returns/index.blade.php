@extends('layouts.admin')
@section('title', 'Returns Management')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900">Returns</h1><p class="text-gray-500 text-sm mt-1">Manage customer return requests.</p></div>

<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 flex gap-3 flex-wrap">
    <form action="{{ route('admin.returns.index') }}" method="GET" class="flex gap-3 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number or customer..." class="flex-1 min-w-[200px] px-4 py-2 border border-gray-200 rounded-md text-sm focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-md text-sm bg-white focus:ring-[#ff3f6c]">
            <option value="">All Statuses</option>
            @foreach(['pending','approved','rejected','pickup_scheduled','picked_up','received','inspection','approved_for_refund','refund_initiated','completed'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-bold text-sm">Filter</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <tr>
                    <th class="px-6 py-4">Return ID</th>
                    <th class="px-6 py-4">Order</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">Reason</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($returns as $return)
                @php $item = $return->orderItem; $order = $item?->order; $user = $order?->user; @endphp
                <tr class="hover:bg-gray-50" x-data="{ actionOpen: false }">
                    <td class="px-6 py-4 font-mono text-xs text-gray-600">#{{ $return->id }}</td>
                    <td class="px-6 py-4">
                        @if($order)
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[#ff3f6c] hover:underline font-bold">{{ $order->order_number }}</a>
                        @else <span class="text-gray-400">N/A</span> @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $user?->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $user?->phone ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-700 max-w-[150px] truncate" title="{{ $item?->product_name_snapshot }}">{{ $item?->product_name_snapshot ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-gray-600 max-w-[120px] truncate" title="{{ $return->reason }}">{{ Str::limit($return->reason, 40) }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = ['pending'=>'yellow','approved'=>'green','rejected'=>'red','completed'=>'blue','received'=>'purple','inspection'=>'indigo'];
                            $color = $statusColors[$return->status] ?? 'gray';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $color }}-100 text-{{ $color }}-800">
                            {{ ucwords(str_replace('_', ' ', $return->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $return->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <button @click="actionOpen = true" class="text-[#ff3f6c] hover:underline font-bold text-sm">Update</button>
                        <div x-show="actionOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
                            <div class="fixed inset-0 bg-black/40" @click="actionOpen = false"></div>
                            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 p-6">
                                <h3 class="font-bold text-gray-900 mb-4">Update Return #{{ $return->id }}</h3>
                                <form action="{{ route('admin.returns.status', $return->id) }}" method="POST">
                                    @csrf
                                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white mb-4">
                                        @foreach(['pending','approved','rejected','pickup_scheduled','picked_up','received','inspection','approved_for_refund','refund_initiated','completed'] as $s)
                                            <option value="{{ $s }}" {{ $return->status == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="actionOpen = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-bold text-gray-700">Cancel</button>
                                        <button type="submit" class="px-4 py-2 bg-[#ff3f6c] text-white rounded-md text-sm font-bold">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-gray-500">
                    <i class="fa-solid fa-rotate-left text-4xl text-gray-300 mb-3 block"></i>
                    <p class="font-medium">No return requests found.</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($returns->hasPages())<div class="px-6 py-4 border-t border-gray-100">{{ $returns->links() }}</div>@endif
</div>
@endsection
