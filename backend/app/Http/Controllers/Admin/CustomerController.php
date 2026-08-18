<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $customers = $query->paginate(20)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::findOrFail($id);
        if ($customer->isAdmin()) {
            abort(403, 'Cannot view admin accounts here.');
        }

        $orders = Order::with('items.product')
            ->where('user_id', $id)
            ->latest()
            ->paginate(10);

        $totalSpend = Order::where('user_id', $id)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        return view('admin.customers.show', compact('customer', 'orders', 'totalSpend'));
    }

    public function toggleStatus(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        if ($customer->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot modify admin accounts.');
        }

        $newStatus = $request->status; // 'active', 'inactive', 'blocked'
        $old = $customer->role;
        if ($newStatus === 'blocked') {
            $customer->update(['role' => 'blocked']);
        } else {
            $customer->update(['role' => 'customer']);
        }

        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => strtoupper("CUSTOMER_{$newStatus}"),
            'entity' => 'User',
            'entity_id' => $customer->id,
            'details' => json_encode(['customer_email' => $customer->email, 'new_status' => $newStatus]),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', "Customer status updated to {$newStatus}.");
    }
}
