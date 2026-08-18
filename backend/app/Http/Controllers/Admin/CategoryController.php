<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products')->orderBy('name');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $query->paginate(20)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:categories,name']);
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ?? 1,
        ]);
        $this->audit('CREATE_CATEGORY', 'Category', null, null, $request->name);
        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate(['name' => 'required|string|max:100|unique:categories,name,' . $id]);
        $old = $category->name;
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ?? $category->status,
        ]);
        $this->audit('UPDATE_CATEGORY', 'Category', $id, $old, $request->name);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        if ($category->products_count > 0) {
            return redirect()->back()->with('error', 'Cannot delete: this category has ' . $category->products_count . ' products attached.');
        }
        $name = $category->name;
        $category->delete();
        $this->audit('DELETE_CATEGORY', 'Category', $id, $name, null);
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->status = $category->status == 1 ? 0 : 1;
        $category->save();
        return redirect()->back()->with('success', 'Category status updated.');
    }

    private function audit($action, $entity, $entityId, $old, $new)
    {
        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'details' => json_encode(['old' => $old, 'new' => $new]),
            'ip_address' => request()->ip(),
        ]);
    }
}
