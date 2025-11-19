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

    <!-- Suspicious IPs Alert -->
    @if($suspiciousIPs->count() > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800">Cảnh báo: Phát hiện IP có hành vi bất thường</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Các IP sau đây có hơn 100 requests trong 1 giờ qua:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            @foreach($suspiciousIPs as $suspicious)
                                <li class="flex items-center justify-between">
                                    <div>
                                        <strong>{{ $suspicious->ip_address }}</strong> - 
                                        {{ number_format($suspicious->request_count) }} requests
                                        <a href="{{ route('admin.access-logs.index', ['ip' => $suspicious->ip_address]) }}" class="text-blue-600 hover:underline ml-2">Xem chi tiết</a>
                                    </div>
                                    <form method="POST" action="{{ route('admin.access-logs.block-ip') }}" class="inline ml-2" onsubmit="return confirm('Bạn có chắc chắn muốn chặn IP này?');">
                                        @csrf
                                        <input type="hidden" name="ip_address" value="{{ $suspicious->ip_address }}">
                                        <input type="hidden" name="reason" value="Suspicious activity: {{ $suspicious->request_count }} requests in 1 hour">
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Chặn IP</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Top IPs -->
    @if($topIPs->count() > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">Top 10 IP có nhiều requests nhất (24h qua)</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-yellow-200">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-yellow-800">IP Address</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-yellow-800">Số requests</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-yellow-800">Request đầu tiên</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-yellow-800">Request cuối</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-yellow-800">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-yellow-200">
                                    @foreach($topIPs as $ip)
                                        <tr>
                                            <td class="px-3 py-2 text-xs font-mono">{{ $ip->ip_address }}</td>
                                            <td class="px-3 py-2 text-xs">
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">
                                                    {{ number_format($ip->request_count) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-xs">{{ $ip->first_request ? \Carbon\Carbon::parse($ip->first_request)->format('H:i:s') : 'N/A' }}</td>
                                            <td class="px-3 py-2 text-xs">{{ $ip->last_request ? \Carbon\Carbon::parse($ip->last_request)->format('H:i:s') : 'N/A' }}</td>
                                            <td class="px-3 py-2 text-xs">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('admin.access-logs.index', ['ip' => $ip->ip_address]) }}" class="text-blue-600 hover:underline">Xem</a>
                                                    <form method="POST" action="{{ route('admin.access-logs.block-ip') }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn chặn IP này?');">
                                                        @csrf
                                                        <input type="hidden" name="ip_address" value="{{ $ip->ip_address }}">
                                                        <input type="hidden" name="reason" value="High request count: {{ $ip->request_count }} requests in 24h">
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs">Chặn</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Blocked IPs -->
    @if(isset($blockedIPs) && $blockedIPs->count() > 0)
        <div class="bg-gray-50 border border-gray-300 p-4 mb-6 rounded-lg">
            <h3 class="text-sm font-medium text-gray-800 mb-3">Danh sách IP bị chặn</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-700">IP Address</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-700">Lý do</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-700">Chặn đến</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-700">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($blockedIPs as $blocked)
                            <tr>
                                <td class="px-3 py-2 text-xs font-mono">{{ $blocked->ip_address }}</td>
                                <td class="px-3 py-2 text-xs text-gray-600">{{ $blocked->reason ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-xs text-gray-600">
                                    @if($blocked->blocked_until)
                                        {{ $blocked->blocked_until->format('d/m/Y H:i:s') }}
                                    @else
                                        <span class="text-red-600 font-medium">Vĩnh viễn</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-xs">
                                    <form method="POST" action="{{ route('admin.access-logs.unblock-ip', $blocked->ip_address) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn bỏ chặn IP này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-green-600 hover:text-green-800">Bỏ chặn</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Block IP Form -->
    <div class="bg-gray-50 border border-gray-300 p-4 mb-6 rounded-lg">
        <h3 class="text-sm font-medium text-gray-800 mb-3">Chặn IP thủ công</h3>
        <form method="POST" action="{{ route('admin.access-logs.block-ip') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">IP Address</label>
                <input type="text" name="ip_address" value="{{ request('ip') }}" required class="border rounded px-3 py-2 text-sm w-full" placeholder="192.168.1.1">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Lý do</label>
                <input type="text" name="reason" class="border rounded px-3 py-2 text-sm w-full" placeholder="Lý do chặn">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Chặn trong (giờ)</label>
                <input type="number" name="hours" min="1" max="8760" class="border rounded px-3 py-2 text-sm w-full" placeholder="Để trống = vĩnh viễn">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm w-full">Chặn IP</button>
            </div>
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

