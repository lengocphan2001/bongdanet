<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookmakerController extends Controller
{
    /**
     * Display a listing of bookmakers
     */
    public function index(Request $request)
    {
        $query = Bookmaker::latest();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $bookmakers = $query->paginate(20);

        return view('admin.bookmakers.index', compact('bookmakers'));
    }

    /**
     * Show the form for creating a new bookmaker
     */
    public function create()
    {
        return view('admin.bookmakers.create');
    }

    /**
     * Store a newly created bookmaker
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'required|url|max:255',
            'target' => 'required|in:_blank,_self',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('bookmakers', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        Bookmaker::create($validated);

        return redirect()->route('admin.bookmakers.index')
            ->with('success', 'Nhà cái đã được tạo thành công!');
    }

    /**
     * Display the specified bookmaker
     */
    public function show(Bookmaker $bookmaker)
    {
        return view('admin.bookmakers.show', compact('bookmaker'));
    }

    /**
     * Show the form for editing the specified bookmaker
     */
    public function edit(Bookmaker $bookmaker)
    {
        return view('admin.bookmakers.edit', compact('bookmaker'));
    }

    /**
     * Update the specified bookmaker
     */
    public function update(Request $request, Bookmaker $bookmaker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'required|url|max:255',
            'target' => 'required|in:_blank,_self',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($bookmaker->image && Storage::disk('public')->exists($bookmaker->image)) {
                Storage::disk('public')->delete($bookmaker->image);
            }
            $validated['image'] = $request->file('image')->store('bookmakers', 'public');
        } else {
            // Keep existing image if not uploading new one
            $validated['image'] = $bookmaker->image;
        }

        $validated['is_active'] = $request->has('is_active');

        $bookmaker->update($validated);

        return redirect()->route('admin.bookmakers.index')
            ->with('success', 'Nhà cái đã được cập nhật thành công!');
    }

    /**
     * Remove the specified bookmaker
     */
    public function destroy(Bookmaker $bookmaker)
    {
        // Delete image if exists
        if ($bookmaker->image && Storage::disk('public')->exists($bookmaker->image)) {
            Storage::disk('public')->delete($bookmaker->image);
        }

        $bookmaker->delete();

        return redirect()->route('admin.bookmakers.index')
            ->with('success', 'Nhà cái đã được xóa thành công!');
    }

    /**
     * Toggle bookmaker active status
     */
    public function toggleStatus(Bookmaker $bookmaker)
    {
        $bookmaker->update(['is_active' => !$bookmaker->is_active]);

        return redirect()->back()
            ->with('success', 'Trạng thái nhà cái đã được cập nhật!');
    }
}
