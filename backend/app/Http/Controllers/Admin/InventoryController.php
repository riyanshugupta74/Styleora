<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product.brand', 'color', 'size'])
            ->orderBy('stock', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filter === 'low_stock') {
            $query->where('stock', '>', 0)->where('stock', '<', 10);
        } elseif ($request->filter === 'out_of_stock') {
            $query->where('stock', 0);
        }

        $variants = $query->paginate(25)->withQueryString();
        return view('admin.inventory.index', compact('variants'));
    }

    public function adjust(Request $request, $id)
    {
        $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $variant = ProductVariant::findOrFail($id);
        $oldStock = $variant->stock;
        $newStock = max(0, $oldStock + $request->adjustment);
        $variant->update(['stock' => $newStock]);

        // Update parent product stock
        $variant->product->update(['stock' => $variant->product->variants()->sum('stock')]);

        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'ADJUST_INVENTORY',
            'entity' => 'ProductVariant',
            'entity_id' => $variant->id,
            'details' => json_encode([
                'sku' => $variant->sku,
                'previous_stock' => $oldStock,
                'new_stock' => $newStock,
                'difference' => $newStock - $oldStock,
                'reason' => $request->reason,
            ]),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', "Stock adjusted: {$oldStock} → {$newStock} for SKU {$variant->sku}.");
    }
}
