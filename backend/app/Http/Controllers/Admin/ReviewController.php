<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn($q) => $q
                ->where('comment', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%"))
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
            );
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->paginate(20)->withQueryString();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,hidden,flagged']);
        $review = Review::findOrFail($id);
        $old = $review->status;
        $review->update(['status' => $request->status]);

        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_REVIEW_STATUS',
            'entity' => 'Review',
            'entity_id' => $review->id,
            'details' => json_encode(['old_status' => $old, 'new_status' => $request->status]),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', 'Review status updated.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_REVIEW',
            'entity' => 'Review',
            'entity_id' => $id,
            'details' => json_encode(['product_id' => $review->product_id]),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()->with('success', 'Review deleted.');
    }
}
