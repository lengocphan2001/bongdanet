@extends('admin.layouts.app')

@section('title', 'Quản lý Nhà Cái')

@section('content')
<div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Quản lý Nhà Cái</h1>
        <a href="{{ route('admin.bookmakers.create') }}" class="bg-[#1a5f2f] hover:bg-[#144a25] text-white px-3 sm:px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto text-center">
            + Thêm nhà cái mới
        </a>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-col sm:flex-row gap-3 sm:gap-4">
        <form method="GET" action="{{ route('admin.bookmakers.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
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
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded text-sm w-full sm:w-auto">Tìm</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.bookmakers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 sm:px-4 py-2 rounded text-sm w-full sm:w-auto text-center">Xóa</a>
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
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Link</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Thứ tự</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookmakers as $bookmaker)
                    <tr>
                        <td class="px-3 sm:px-6 py-4">
                            @if($bookmaker->image)
                                <img src="{{ Storage::url($bookmaker->image) }}" 
                                     alt="{{ $bookmaker->name }}" 
                                     class="w-16 h-16 object-contain rounded">
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">
                                    N/A
                                </div>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $bookmaker->name }}</div>
                            @if($bookmaker->notes)
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($bookmaker->notes, 30) }}</div>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                            <a href="{{ $bookmaker->link }}" target="_blank" class="text-xs sm:text-sm text-blue-600 hover:text-blue-900 truncate block max-w-xs">
                                {{ Str::limit($bookmaker->link, 40) }}
                            </a>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.bookmakers.toggle-status', $bookmaker) }}" class="inline">
                                @csrf
                                @if($bookmaker->is_active)
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
                            {{ $bookmaker->order }}
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                            <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                <a href="{{ route('admin.bookmakers.edit', $bookmaker) }}" class="text-blue-600 hover:text-blue-900 sm:mr-3">Sửa</a>
                                <form method="POST" action="{{ route('admin.bookmakers.destroy', $bookmaker) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cái này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 sm:px-6 py-4 text-center text-gray-500 text-sm">Chưa có nhà cái nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $bookmakers->links() }}
    </div>
</div>
@endsection

