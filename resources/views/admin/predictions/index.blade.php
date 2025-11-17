@extends('admin.layouts.app')

@section('title', 'Quản lý Nhận định')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Quản lý Nhận định Bóng đá</h1>
        <a href="{{ route('admin.predictions.create') }}" class="bg-[#1a5f2f] hover:bg-[#144a25] text-white px-4 py-2 rounded">
            + Thêm nhận định mới
        </a>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex gap-4">
        <form method="GET" action="{{ route('admin.predictions.index') }}" class="flex gap-2">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Tìm kiếm..." 
                   class="border rounded px-3 py-2">
            <select name="status" class="border rounded px-3 py-2">
                <option value="">Tất cả trạng thái</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Tìm</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.predictions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Xóa</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trận đấu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tác giả</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($predictions as $prediction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $prediction->home_team ?? 'N/A' }} vs {{ $prediction->away_team ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $prediction->league_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $prediction->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $prediction->author->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $prediction->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.predictions.edit', $prediction) }}" class="text-blue-600 hover:text-blue-900 mr-3">Sửa</a>
                            <form method="POST" action="{{ route('admin.predictions.destroy', $prediction) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Không có nhận định nào.</td>
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

