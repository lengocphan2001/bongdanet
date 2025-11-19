@extends('admin.layouts.app')

@section('title', 'Quản lý Banner')

@section('content')
<div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Quản lý Banner Quảng Cáo</h1>
        <a href="{{ route('admin.banners.create') }}" class="bg-[#1a5f2f] hover:bg-[#144a25] text-white px-3 sm:px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto text-center">
            + Thêm banner mới
        </a>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-col sm:flex-row gap-3 sm:gap-4">
        <form method="GET" action="{{ route('admin.banners.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Tìm kiếm..." 
                   class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
            <select name="status" class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
            </select>
            <select name="position" class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
                <option value="">Tất cả vị trí</option>
                <option value="top" {{ request('position') === 'top' ? 'selected' : '' }}>Top</option>
                <option value="sidebar" {{ request('position') === 'sidebar' ? 'selected' : '' }}>Sidebar (Chung)</option>
                <option value="sidebar-left" {{ request('position') === 'sidebar-left' ? 'selected' : '' }}>Sidebar Left</option>
                <option value="sidebar-right" {{ request('position') === 'sidebar-right' ? 'selected' : '' }}>Sidebar Right</option>
                <option value="bottom" {{ request('position') === 'bottom' ? 'selected' : '' }}>Bottom</option>
                <option value="inline" {{ request('position') === 'inline' ? 'selected' : '' }}>Inline</option>
                <option value="sticky" {{ request('position') === 'sticky' ? 'selected' : '' }}>Sticky</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded text-sm w-full sm:w-auto">Tìm</button>
            @if(request('search') || request('status') || request('position'))
                <a href="{{ route('admin.banners.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 sm:px-4 py-2 rounded text-sm w-full sm:w-auto text-center">Xóa</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto -mx-2 sm:mx-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Loại</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Kích thước</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Vị trí</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Thứ tự</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($banners as $banner)
                    <tr>
                        <td class="px-3 sm:px-6 py-4">
                            @if($banner->image)
                                <img src="{{ Storage::url($banner->image) }}" 
                                     alt="{{ $banner->alt }}" 
                                     class="w-16 h-16 object-cover rounded">
                            @elseif($banner->code)
                                <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">
                                    Code
                                </div>
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">
                                    N/A
                                </div>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $banner->name }}</div>
                            @if($banner->notes)
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($banner->notes, 30) }}</div>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">
                                @if($banner->type === 'modal')
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Modal</span>
                                @elseif($banner->type === 'sticky')
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">Sticky</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Thường</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ $banner->size }}</span>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">{{ $banner->position }}</span>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.banners.toggle-status', $banner) }}" class="inline">
                                @csrf
                                @if($banner->is_active)
                                    <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 hover:bg-green-200">
                                        Đang hoạt động
                                    </button>
                                @else
                                    <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200">
                                        Tạm dừng
                                    </button>
                                @endif
                            </form>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden lg:table-cell">
                            {{ $banner->order }}
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                            <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="text-blue-600 hover:text-blue-900 sm:mr-3">Sửa</a>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 sm:px-6 py-4 text-center text-gray-500 text-sm">Chưa có banner nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $banners->links() }}
    </div>
</div>
@endsection

