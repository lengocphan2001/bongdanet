@extends('admin.layouts.app')

@section('title', 'Nhật ký truy cập')

@section('content')
<div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Nhật ký truy cập website</h1>
        <form method="POST" action="{{ route('admin.access-logs.clean') }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa các bản ghi cũ?');">
            @csrf
            <input type="number" name="days" value="30" min="1" class="border rounded px-2 py-1 text-sm w-20 mr-2" placeholder="Ngày">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 rounded text-sm">
                Xóa bản ghi cũ
            </button>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4 mb-6">
        <div class="bg-blue-50 p-3 rounded-lg">
            <div class="text-xs sm:text-sm text-blue-600 font-medium">Tổng số</div>
            <div class="text-lg sm:text-2xl font-bold text-blue-900">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-green-50 p-3 rounded-lg">
            <div class="text-xs sm:text-sm text-green-600 font-medium">Hôm nay</div>
            <div class="text-lg sm:text-2xl font-bold text-green-900">{{ number_format($stats['today']) }}</div>
        </div>
        <div class="bg-purple-50 p-3 rounded-lg">
            <div class="text-xs sm:text-sm text-purple-600 font-medium">Tuần này</div>
            <div class="text-lg sm:text-2xl font-bold text-purple-900">{{ number_format($stats['this_week']) }}</div>
        </div>
        <div class="bg-orange-50 p-3 rounded-lg">
            <div class="text-xs sm:text-sm text-orange-600 font-medium">Tháng này</div>
            <div class="text-lg sm:text-2xl font-bold text-orange-900">{{ number_format($stats['this_month']) }}</div>
        </div>
        <div class="bg-indigo-50 p-3 rounded-lg">
            <div class="text-xs sm:text-sm text-indigo-600 font-medium">IP duy nhất</div>
            <div class="text-lg sm:text-2xl font-bold text-indigo-900">{{ number_format($stats['unique_ips']) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-4 bg-gray-50 p-3 sm:p-4 rounded-lg">
        <form method="GET" action="{{ route('admin.access-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">IP Address</label>
                <input type="text" 
                       name="ip" 
                       value="{{ request('ip') }}" 
                       placeholder="192.168.1.1"
                       class="border rounded px-3 py-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">URL</label>
                <input type="text" 
                       name="url" 
                       value="{{ request('url') }}" 
                       placeholder="Tìm trong URL..."
                       class="border rounded px-3 py-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" 
                       name="date_from" 
                       value="{{ request('date_from') }}"
                       class="border rounded px-3 py-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" 
                       name="date_to" 
                       value="{{ request('date_to') }}"
                       class="border rounded px-3 py-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status Code</label>
                <select name="status_code" class="border rounded px-3 py-2 text-sm w-full">
                    <option value="">Tất cả</option>
                    <option value="200" {{ request('status_code') == '200' ? 'selected' : '' }}>200 OK</option>
                    <option value="404" {{ request('status_code') == '404' ? 'selected' : '' }}>404 Not Found</option>
                    <option value="500" {{ request('status_code') == '500' ? 'selected' : '' }}>500 Error</option>
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Thiết bị</label>
                <select name="device_type" class="border rounded px-3 py-2 text-sm w-full">
                    <option value="">Tất cả</option>
                    <option value="desktop" {{ request('device_type') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                    <option value="mobile" {{ request('device_type') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                    <option value="tablet" {{ request('device_type') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Trình duyệt</label>
                <select name="browser" class="border rounded px-3 py-2 text-sm w-full">
                    <option value="">Tất cả</option>
                    <option value="Chrome" {{ request('browser') == 'Chrome' ? 'selected' : '' }}>Chrome</option>
                    <option value="Firefox" {{ request('browser') == 'Firefox' ? 'selected' : '' }}>Firefox</option>
                    <option value="Safari" {{ request('browser') == 'Safari' ? 'selected' : '' }}>Safari</option>
                    <option value="Edge" {{ request('browser') == 'Edge' ? 'selected' : '' }}>Edge</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm w-full sm:w-auto">Tìm kiếm</button>
                @if(request()->anyFilled(['ip', 'url', 'date_from', 'date_to', 'status_code', 'device_type', 'browser']))
                    <a href="{{ route('admin.access-logs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm w-full sm:w-auto text-center">Xóa</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto -mx-2 sm:mx-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Method</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Thiết bị</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Trình duyệt</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Thời gian phản hồi</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-xs sm:text-sm text-gray-900">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $log->ip_address }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm text-gray-900 max-w-xs truncate" title="{{ $log->url }}">
                                {{ $log->url }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $log->method === 'GET' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ ucfirst($log->device_type) }}</span>
                                @if($log->os)
                                    <div class="text-xs text-gray-500 mt-1">{{ $log->os }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">{{ $log->browser }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            @if($log->status_code >= 200 && $log->status_code < 300)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">{{ $log->status_code }}</span>
                            @elseif($log->status_code >= 300 && $log->status_code < 400)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">{{ $log->status_code }}</span>
                            @elseif($log->status_code >= 400)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">{{ $log->status_code }}</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">{{ $log->status_code ?? 'N/A' }}</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">
                                @if($log->response_time)
                                    {{ number_format($log->response_time) }}ms
                                @else
                                    N/A
                                @endif
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                            <a href="{{ route('admin.access-logs.show', $log) }}" class="text-blue-600 hover:text-blue-900">Chi tiết</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 sm:px-6 py-4 text-center text-gray-500 text-sm">Không có bản ghi nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->appends(request()->query())->links() }}
    </div>
</div>
@endsection

