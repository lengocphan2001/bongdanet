@extends('admin.layouts.app')

@section('title', 'Quản lý Nhận định')

@section('content')
<div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Quản lý Nhận định Bóng đá</h1>
        <a href="{{ route('admin.predictions.create') }}" class="bg-[#1a5f2f] hover:bg-[#144a25] text-white px-3 sm:px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto text-center">
            + Thêm nhận định mới
        </a>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-col sm:flex-row gap-3 sm:gap-4">
        <form method="GET" action="{{ route('admin.predictions.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Tìm kiếm..." 
                   class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
            <select name="status" class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
                <option value="">Tất cả trạng thái</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded text-sm w-full sm:w-auto">Tìm</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.predictions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 sm:px-4 py-2 rounded text-sm w-full sm:w-auto text-center">Xóa</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto -mx-2 sm:mx-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trận đấu</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Tác giả</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Ngày tạo</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($predictions as $prediction)
                    <tr>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm text-gray-900 break-words">
                                {{ $prediction->home_team ?? 'N/A' }} vs {{ $prediction->away_team ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $prediction->league_name }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm font-medium text-gray-900 break-words">{{ Str::limit($prediction->title, 50) }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">{{ $prediction->author->name }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            @if($prediction->status === 'published')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Đã xuất bản
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Bản nháp
                                </span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden md:table-cell">
                            {{ $prediction->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                            <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                <a href="{{ route('admin.predictions.edit', $prediction) }}" class="text-blue-600 hover:text-blue-900 sm:mr-3">Sửa</a>
                                <form method="POST" action="{{ route('admin.predictions.destroy', $prediction) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 sm:px-6 py-4 text-center text-gray-500 text-sm">Không có nhận định nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $predictions->links() }}
    </div>
</div>
@endsection

