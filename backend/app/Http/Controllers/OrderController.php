<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        // Eager load product images and variants to ensure exact images are displayed
        $orders = Order::with(['items.product.images', 'items.variant'])->where('user_id', Auth::id())->latest()->get();
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'items.variant'])
            ->latest()
            ->paginate(10);
            
        return view('user.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product.images', 'items.variant', 'address', 'payments'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('user.order-details', compact('order'));
    }

    public function cancelItem(Request $request, $itemId)
    {
        $request->validate([
            'cancellation_reason' => 'required|string',
            'cancellation_note' => 'nullable|string'
        ]);

        $item = OrderItem::with(['order', 'product'])->findOrFail($itemId);

        if ($item->order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($item->order->status, ['placed', 'confirmed']) || !in_array($item->status, ['placed', 'confirmed'])) {
            return back()->with('error', 'This item cannot be cancelled at this stage.');
        }

        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'cancelled',
            ]);

            if (empty($item->order->cancellation_reason)) {
                $item->order->update([
                    'cancellation_reason' => $request->cancellation_reason,
                    'cancellation_note' => $request->cancellation_note
                ]);
            }

            if($item->product_variant_id) {
                ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
            }
            $order = $item->order;
            if($order->payment_status == 'completed' || $order->payment_method != 'cod') {
                Refund::create(['order_id' => $order->id, 'amount' => $item->total, 'status' => 'initiated', 'refund_method' => 'original_source', 'reference_id' => 'REF-' . strtoupper(Str::random(8))]);
            }
            $allCancelled = true;
            foreach($order->items as $oItem) {
                if($oItem->status != 'cancelled') { $allCancelled = false; break; }
            }
            if($allCancelled) { $order->update(['status' => 'cancelled']); }
            DB::commit();
            return redirect()->back()->with('success', 'Item has been cancelled and refund initiated.');
        } catch(\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error cancelling item: ' . $e->getMessage());
        }
    }
}
