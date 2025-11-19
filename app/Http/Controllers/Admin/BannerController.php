<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of banners
     */
    public function index(Request $request)
    {
        $query = Banner::latest();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Filter by position
        if ($request->has('position') && $request->position !== '') {
            $query->where('position', $request->position);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('alt', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $banners = $query->paginate(20);

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created banner
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:normal,modal,sticky',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'code' => 'nullable|string',
            'link' => 'nullable|url|max:255',
            'alt' => 'nullable|string|max:255',
            'size' => 'required|in:small,medium,large,full-width,sidebar,square,rectangle',
            'position' => 'required|in:top,sidebar,sidebar-left,sidebar-right,bottom,inline,sticky',
            'target' => 'required|in:_blank,_self',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        Banner::create($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được tạo thành công!');
    }

    /**
     * Display the specified banner
     */
    public function show(Banner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified banner
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified banner
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:normal,modal,sticky',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'code' => 'nullable|string',
            'link' => 'nullable|url|max:255',
            'alt' => 'nullable|string|max:255',
            'size' => 'required|in:small,medium,large,full-width,sidebar,square,rectangle',
            'position' => 'required|in:top,sidebar,sidebar-left,sidebar-right,bottom,inline,sticky',
            'target' => 'required|in:_blank,_self',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được cập nhật thành công!');
    }

    /**
     * Remove the specified banner
     */
    public function destroy(Banner $banner)
    {
        // Delete image if exists
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được xóa thành công!');
    }

    /**
     * Toggle banner active status
     */
    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return redirect()->back()
            ->with('success', 'Trạng thái banner đã được cập nhật!');
    }
}
