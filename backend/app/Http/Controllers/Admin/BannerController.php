<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:4096',
            'button_url' => 'nullable|url',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'image' => $imagePath,
            'position' => $request->position ?? 'hero',
            'sort_order' => Banner::max('sort_order') + 1,
            'status' => $request->status ?? 1,
        ]);

        $this->audit('CREATE_BANNER', $request->title);
        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $request->validate(['title' => 'required|string|max:255']);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'position' => $request->position ?? $banner->position,
            'status' => $request->status ?? $banner->status,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);
        $this->audit('UPDATE_BANNER', $banner->title);
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();
        $this->audit('DELETE_BANNER', $banner->title);
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted.');
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = $banner->status == 1 ? 0 : 1;
        $banner->save();
        return redirect()->back()->with('success', 'Banner status updated.');
    }

    private function audit($action, $detail)
    {
        \App\Models\AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity' => 'Banner',
            'entity_id' => null,
            'details' => json_encode(['detail' => $detail]),
            'ip_address' => request()->ip(),
        ]);
    }
}
