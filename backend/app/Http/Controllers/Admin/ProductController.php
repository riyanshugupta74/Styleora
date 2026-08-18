<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants'])->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'description' => $request->description,
            'status' => $request->status ?? 1,
            'stock' => $request->stock ?? 0,
        ]);

        // Audit Log
        if(class_exists('\App\Models\AdminAuditLog')) {
            \App\Models\AdminAuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'CREATE',
                'module' => 'PRODUCTS',
                'description' => "Created product {$product->name} (SKU: {$product->sku})"
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,'.$product->id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'description' => $request->description,
            'status' => $request->status ?? $product->status,
            'stock' => $request->stock ?? $product->stock,
        ]);

        if(class_exists('\App\Models\AdminAuditLog')) {
            \App\Models\AdminAuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'UPDATE',
                'module' => 'PRODUCTS',
                'description' => "Updated product {$product->name}"
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status == 1 ? 0 : 1;
        $product->save();

        return redirect()->back()->with('success', 'Product status updated.');
    }
}
