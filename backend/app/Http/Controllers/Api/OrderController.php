<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Refund;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product.images', 'items.variant'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function show($id): JsonResponse
    {
        $order = Order::with(['items.product.images', 'items.product.brand', 'items.variant', 'address', 'payments'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'order' => $order,
            'timeline' => $order->timeline_status,
        ]);
    }

    public function cancelItem(Request $request, $itemId): JsonResponse
    {
        $request->validate([
            'cancellation_reason' => 'required|string',
            'cancellation_note' => 'nullable|string'
        ]);

        $item = OrderItem::with(['order', 'product'])->findOrFail($itemId);

        if ($item->order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($item->order->status, ['placed', 'confirmed']) || !in_array($item->status, ['placed', 'confirmed'])) {
            return response()->json(['error' => 'This item cannot be cancelled at this stage.'], 400);
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

            if ($item->product_variant_id) {
                ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
            }

            $order = $item->order;
            if ($order->payment_status == 'completed' || $order->payment_method != 'cod') {
                Refund::create([
                    'order_id' => $order->id,
                    'amount' => $item->total,
                    'status' => 'initiated',
                    'refund_method' => 'original_source',
                    'reference_id' => 'REF-' . strtoupper(Str::random(8))
                ]);
            }

            $allCancelled = true;
            foreach ($order->items as $oItem) {
                if ($oItem->status != 'cancelled') {
                    $allCancelled = false;
                    break;
                }
            }
            if ($allCancelled) {
                $order->update(['status' => 'cancelled']);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Item has been cancelled and refund initiated.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error cancelling item: ' . $e->getMessage()], 500);
        }
    }
}
