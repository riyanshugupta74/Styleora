<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = Refund::with(['order.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', fn($q) => $q->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('order.user', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $refunds = $query->paginate(20)->withQueryString();
        return view('admin.refunds.index', compact('refunds'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,initiated,completed,failed',
            'reference_id' => 'nullable|string',
        ]);

        $refund = Refund::with('order.user')->findOrFail($id);
        $old = $refund->status;

        $refund->update([
            'status' => $request->status,
            'reference_id' => $request->reference_id ?? $refund->reference_id,
        ]);

        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_REFUND_STATUS',
            'entity' => 'Refund',
            'entity_id' => $refund->id,
            'details' => json_encode([
                'order_id' => $refund->order_id,
                'amount' => $refund->amount,
                'old_status' => $old,
                'new_status' => $request->status,
            ]),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', 'Refund status updated to ' . $request->status . '.');
    }
}
