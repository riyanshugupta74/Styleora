<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRequest;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public function index(Request $request)
    {
        $query = ExchangeRequest::with(['orderItem.order.user', 'orderItem.product', 'newVariant.color', 'newVariant.size'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('orderItem.order.user', fn($q) =>
                $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")
            );
        }

        $exchanges = $query->paginate(20)->withQueryString();
        return view('admin.exchanges.index', compact('exchanges'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $exchange = ExchangeRequest::findOrFail($id);
        $old = $exchange->status;
        $exchange->update(['status' => $request->status]);

        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_EXCHANGE_STATUS',
            'entity' => 'ExchangeRequest',
            'entity_id' => $exchange->id,
            'details' => json_encode(['old_status' => $old, 'new_status' => $request->status]),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', 'Exchange status updated.');
    }
}
