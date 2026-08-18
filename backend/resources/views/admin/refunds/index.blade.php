@extends('layouts.admin')
@section('title', 'Refunds Management')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900">Refunds</h1><p class="text-gray-500 text-sm mt-1">Manage and process customer refunds.</p></div>

<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 flex gap-3 flex-wrap">
    <form action="{{ route('admin.refunds.index') }}" method="GET" class="flex gap-3 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number or customer..." class="flex-1 min-w-[200px] px-4 py-2 border border-gray-200 rounded-md text-sm focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-md text-sm bg-white">
            <option value="">All Statuses</option>
            @foreach(['pending','processing','initiated','completed','failed'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
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
                    <th class="px-6 py-4">Refund ID</th>
                    <th class="px-6 py-4">Order</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Method</th>
                    <th class="px-6 py-4">Reference ID</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($refunds as $refund)
                <tr class="hover:bg-gray-50" x-data="{ actionOpen: false }">
                    <td class="px-6 py-4 font-mono text-xs text-gray-600">#{{ $refund->id }}</td>
                    <td class="px-6 py-4">
                        @if($refund->order)<a href="{{ route('admin.orders.show', $refund->order->id) }}" class="text-[#ff3f6c] hover:underline font-bold">{{ $refund->order->order_number }}</a>
                        @else <span class="text-gray-400">N/A</span> @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $refund->order?->user?->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $refund->order?->user?->email }}</p>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900">₹{{ number_format($refund->amount, 2) }}</td>
                    <td class="px-6 py-4 text-gray-600 capitalize">{{ $refund->refund_method ?? 'N/A' }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $refund->reference_id ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @php $color = match($refund->status) { 'completed'=>'green','failed'=>'red','pending'=>'yellow','processing'=>'blue','initiated'=>'purple', default=>'gray' }; @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $color }}-100 text-{{ $color }}-800">{{ ucfirst($refund->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $refund->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        @if(!in_array($refund->status, ['completed', 'failed']))
                        <button @click="actionOpen = true" class="text-[#ff3f6c] hover:underline font-bold text-sm">Update</button>
                        <div x-show="actionOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
                            <div class="fixed inset-0 bg-black/40" @click="actionOpen = false"></div>
                            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 p-6">
                                <h3 class="font-bold text-gray-900 mb-1">Update Refund #{{ $refund->id }}</h3>
                                <p class="text-sm text-gray-500 mb-4">Amount: <strong>₹{{ number_format($refund->amount, 2) }}</strong></p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-xs text-yellow-800 font-medium">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    Marking as "Completed" or "Initiated" indicates the refund has been processed via your payment gateway. Ensure the gateway refund is done before marking complete.
                                </div>
                                <form action="{{ route('admin.refunds.status', $refund->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="block text-sm font-bold text-gray-700 mb-1">New Status</label>
                                        <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white">
                                            @foreach(['pending','processing','initiated','completed','failed'] as $s)
                                                <option value="{{ $s }}" {{ $refund->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Gateway Reference ID</label>
                                        <input type="text" name="reference_id" value="{{ $refund->reference_id }}" placeholder="e.g. RZPRF-xxxxx" class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm">
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="actionOpen = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-bold text-gray-700">Cancel</button>
                                        <button type="submit" class="px-4 py-2 bg-[#ff3f6c] text-white rounded-md text-sm font-bold">Update Refund</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @else
                            <span class="text-gray-400 text-xs">{{ ucfirst($refund->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">
                    <i class="fa-solid fa-money-bill-transfer text-4xl text-gray-300 mb-3 block"></i>
                    <p class="font-medium">No refunds found.</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($refunds->hasPages())<div class="px-6 py-4 border-t border-gray-100">{{ $refunds->links() }}</div>@endif
</div>
@endsection
