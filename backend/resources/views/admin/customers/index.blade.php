@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-900">Customers</h1><p class="text-gray-500 text-sm mt-1">Manage customer accounts and activity.</p></div>

<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('admin.customers.index') }}" method="GET" class="flex gap-3">
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-md text-sm focus:ring-[#ff3f6c] focus:border-[#ff3f6c]">
        </div>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-bold text-sm">Search</button>
        @if(request('search'))<a href="{{ route('admin.customers.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 flex items-center">Clear</a>@endif
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <tr>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Phone</th>
                    <th class="px-6 py-4">Total Orders</th>
                    <th class="px-6 py-4">Joined</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#ff3f6c] text-white flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <div>
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="font-bold text-gray-900 hover:text-[#ff3f6c]">{{ $customer->name }}</a>
                                <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $customer->phone ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">{{ $customer->orders_count }} orders</span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $customer->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        @if($customer->role == 'blocked')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Blocked</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">Active</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="p-2 text-gray-400 hover:text-[#ff3f6c]" title="View Profile"><i class="fa-solid fa-eye"></i></a>
                            <form action="{{ route('admin.customers.status', $customer->id) }}" method="POST" class="inline">
                                @csrf
                                @if($customer->role == 'blocked')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="p-2 text-gray-400 hover:text-green-600" title="Unblock"><i class="fa-solid fa-unlock"></i></button>
                                @else
                                    <input type="hidden" name="status" value="blocked">
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600" title="Block" onclick="return confirm('Block {{ $customer->name }}?')"><i class="fa-solid fa-ban"></i></button>
                                @endif
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <i class="fa-solid fa-users text-4xl text-gray-300 mb-3 block"></i>
                    <p class="font-medium">No customers found.</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())<div class="px-6 py-4 border-t border-gray-100">{{ $customers->links() }}</div>@endif
</div>
@endsection
