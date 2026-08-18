@extends('layouts.admin')
@section('title', 'Customer: '.$customer->name)
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.customers.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
        <p class="text-gray-500 text-sm mt-0.5">Customer Profile</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Profile Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-[#ff3f6c] text-white flex items-center justify-center font-bold text-2xl">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="font-bold text-gray-900 text-lg">{{ $customer->name }}</h2>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $customer->role == 'blocked' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ ucfirst($customer->role) }}
                </span>
            </div>
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-medium text-gray-900">{{ $customer->email }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="font-medium text-gray-900">{{ $customer->phone ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Joined</span><span class="font-medium text-gray-900">{{ $customer->created_at->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Total Orders</span><span class="font-bold text-gray-900">{{ $orders->total() }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Total Spent</span><span class="font-bold text-[#ff3f6c]">₹{{ number_format($totalSpend, 2) }}</span></div>
        </div>
        <div class="mt-6 pt-4 border-t border-gray-100">
            <form action="{{ route('admin.customers.status', $customer->id) }}" method="POST">
                @csrf
                @if($customer->role == 'blocked')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-bold"><i class="fa-solid fa-unlock mr-2"></i>Unblock Customer</button>
                @else
                    <input type="hidden" name="status" value="blocked">
                    <button type="submit" onclick="return confirm('Are you sure you want to block {{ $customer->name }}?')" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-bold"><i class="fa-solid fa-ban mr-2"></i>Block Customer</button>
                @endif
            </form>
        </div>
    </div>

    <!-- Orders Section -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Order History</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($orders as $order)
            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                <div>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-[#ff3f6c] hover:underline">{{ $order->order_number }}</a>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $order->items->count() }} item(s)</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900">₹{{ number_format($order->total, 2) }}</p>
                    @php $color = match($order->status) { 'delivered'=>'green','cancelled'=>'red','pending'=>'yellow', default=>'gray' }; @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $color }}-100 text-{{ $color }}-800">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-500 text-sm">No orders yet.</div>
            @endforelse
        </div>
        @if($orders->hasPages())<div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>@endif
    </div>
</div>
@endsection
