@extends('layouts.admin')
@section('title', 'Admin | Orders')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <h1 class="font-outfit text-2xl font-bold text-gray-900 tracking-tight">Orders</h1>
        
        <form method="GET" action="{{ route('admin.orders.index') }}" class="w-full space-y-3">
            <div class="relative w-full md:w-96">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Order ID, Phone, Email, Name..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#ff3f6c] focus:border-transparent transition-shadow">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            @if(request('search'))
                <a href="{{ route('admin.orders.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
            </div>
            <div class="flex flex-wrap gap-2">
                <select name="status" class="rounded border-gray-200 text-sm"><option value="">All statuses</option>@foreach(['placed','confirmed','packed','shipped','out_for_delivery','delivered','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', $status) }}</option>@endforeach</select>
                <select name="payment_status" class="rounded border-gray-200 text-sm"><option value="">All payments</option><option value="pending">Pending</option><option value="completed">Completed</option><option value="refund_pending">Refund pending</option></select>
                <input type="date" name="from" value="{{ request('from') }}" class="rounded border-gray-200 text-sm"><input type="date" name="to" value="{{ request('to') }}" class="rounded border-gray-200 text-sm">
                <select name="sort" class="rounded border-gray-200 text-sm"><option value="">Newest</option><option value="oldest">Oldest</option><option value="amount_high">Amount: high</option><option value="amount_low">Amount: low</option></select><button class="px-3 py-2 bg-gray-900 text-white rounded text-sm">Apply</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Order ID</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Payment / Status</th>
                        <th class="p-4">Total</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($orders as $order)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-mono font-bold text-[#ff3f6c]">#{{ $order->order_number }}</td>
                            <td class="p-4 text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="p-4 text-gray-900 font-medium">{{ $order->user->name ?? 'Guest' }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $order->status == 'delivered' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ !in_array($order->status, ['delivered','cancelled']) ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                                <p class="mt-1 text-xs text-gray-500 uppercase">{{ $order->payment_method }} · {{ $order->payment_status }}</p>
                            </td>
                            <td class="p-4 font-bold text-gray-900">₹{{ number_format($order->total, 0) }}</td>
                            <td class="p-4 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[#ff3f6c] hover:text-[#e02e5c] font-bold text-xs uppercase tracking-widest border border-[#ff3f6c] hover:bg-[#ff3f6c] hover:text-white px-3 py-1.5 rounded-sm transition-colors inline-block">Manage</a>
                            </td>
                        </tr>
                    @endforeach
                    @if($orders->count() == 0)
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">No orders found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if(method_exists($orders, 'links') && $orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
