<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = ReturnRequest::with(['orderItem.order.user', 'orderItem.product'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('orderItem.order', fn($q) => $q->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('orderItem.order.user', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->paginate(20)->withQueryString();
        return view('admin.returns.index', compact('returns'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $return = ReturnRequest::findOrFail($id);
        $old = $return->status;
        $return->update(['status' => $request->status]);

        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_RETURN_STATUS',
            'entity' => 'ReturnRequest',
            'entity_id' => $return->id,
            'details' => json_encode(['old_status' => $old, 'new_status' => $request->status]),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', 'Return status updated to ' . $request->status . '.');
    }
}
