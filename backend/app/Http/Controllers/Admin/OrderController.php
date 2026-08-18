<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'address'])->latest();

        // Search: order number, ID (with or without #), customer name/email/phone, product name
        if ($request->filled('search')) {
            $rawSearch = trim($request->search);
            $cleanSearch = ltrim($rawSearch, '#');

            $query->where(function($q) use ($rawSearch, $cleanSearch) {
                $q->where('order_number', 'like', "%{$rawSearch}%")
                  ->orWhere('order_number', 'like', "%{$cleanSearch}%")
                  ->orWhere('id', $rawSearch)
                  ->orWhere('id', $cleanSearch)
                  ->orWhereHas('user', function($userQuery) use ($rawSearch, $cleanSearch) {
                      $userQuery->where('name', 'like', "%{$rawSearch}%")
                                ->orWhere('email', 'like', "%{$rawSearch}%")
                                ->orWhere('phone', 'like', "%{$rawSearch}%")
                                ->orWhere('name', 'like', "%{$cleanSearch}%")
                                ->orWhere('email', 'like', "%{$cleanSearch}%");
                  })
                  ->orWhereHas('address', function($addrQuery) use ($rawSearch, $cleanSearch) {
                      $addrQuery->where('phone', 'like', "%{$rawSearch}%")
                                ->orWhere('full_name', 'like', "%{$rawSearch}%")
                                ->orWhere('full_name', 'like', "%{$cleanSearch}%");
                  })
                  ->orWhereHas('items.product', function($prodQuery) use ($rawSearch, $cleanSearch) {
                      $prodQuery->where('name', 'like', "%{$rawSearch}%")
                                ->orWhere('sku', 'like', "%{$rawSearch}%")
                                ->orWhere('name', 'like', "%{$cleanSearch}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Date range filter
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->reorder()->oldest();
                    break;
                case 'amount_high':
                    $query->reorder()->orderBy('total', 'desc');
                    break;
                case 'amount_low':
                    $query->reorder()->orderBy('total', 'asc');
                    break;
            }
        }

        $orders = $query->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items', 'address'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'item_id' => 'nullable|exists:order_items,id'
        ]);

        $order = Order::findOrFail($id);

        if($request->item_id) {
            $item = OrderItem::findOrFail($request->item_id);
            $item->update(['status' => $request->status]);
            
            \App\Models\AdminAuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'Updated Order Item Status',
                'entity' => 'OrderItem',
                'entity_id' => $item->id,
                'details' => json_encode(['order_id' => $id, 'status' => $request->status]),
                'ip_address' => request()->ip()
            ]);
        } else {
            $order->update(['status' => $request->status]);
            // cascade to items if it's not a terminal state already
            foreach($order->items as $item) {
                if(!in_array($item->status, ['cancelled', 'return_requested', 'returned', 'refunded'])) {
                    $item->update(['status' => $request->status]);
                }
            }
            
            \App\Models\AdminAuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'Updated Global Order Status',
                'entity' => 'Order',
                'entity_id' => $order->id,
                'details' => json_encode(['status' => $request->status]),
                'ip_address' => request()->ip()
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string',
            'cancellation_note' => 'nullable|string'
        ]);

        $order = Order::with('items.product')->findOrFail($id);

        if (in_array($order->status, ['delivered', 'cancelled', 'returned'])) {
            return redirect()->back()->with('error', 'This order cannot be cancelled.');
        }

        foreach($order->items as $item) {
            if ($item->variant) { $item->variant->increment('stock', $item->quantity); }
            $item->update(['status' => 'cancelled']);
        }
        $order->update(['status' => 'cancelled', 'cancellation_reason' => $request->cancellation_reason, 'cancellation_note' => $request->cancellation_note, 'cancelled_at' => now(), 'cancelled_by' => auth()->id()]);
        \App\Models\AdminAuditLog::create(['user_id' => auth()->id(), 'action' => 'Cancelled Order', 'entity' => 'Order', 'entity_id' => $order->id, 'details' => json_encode(['reason' => $request->cancellation_reason, 'note' => $request->cancellation_note]), 'ip_address' => request()->ip()]);
        return redirect()->back()->with('success', 'Order cancelled successfully and inventory restocked.');
    }
}
