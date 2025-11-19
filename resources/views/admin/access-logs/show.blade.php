@extends('admin.layouts.app')

@section('title', 'Chi tiết nhật ký truy cập')

@section('content')
<div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
    <div class="mb-4 sm:mb-6">
        <a href="{{ route('admin.access-logs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm sm:text-base mb-4 inline-block">
            ← Quay lại danh sách
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mt-2">Chi tiết nhật ký truy cập</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <!-- Basic Information -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Thời gian</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $log->ip_address }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Method</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $log->method === 'GET' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $log->method }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status Code</dt>
                    <dd class="mt-1">
                        @if($log->status_code >= 200 && $log->status_code < 300)
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">{{ $log->status_code }}</span>
                        @elseif($log->status_code >= 300 && $log->status_code < 400)
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">{{ $log->status_code }}</span>
                        @elseif($log->status_code >= 400)
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">{{ $log->status_code }}</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">{{ $log->status_code ?? 'N/A' }}</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Response Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($log->response_time)
                            {{ number_format($log->response_time) }}ms
                        @else
                            N/A
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Device Information -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin thiết bị</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Loại thiết bị</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                            {{ ucfirst($log->device_type ?? 'Unknown') }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Hệ điều hành</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->os ?? 'Unknown' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Trình duyệt</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->browser ?? 'Unknown' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Quốc gia</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->country ?? 'Unknown' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Thành phố</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->city ?? 'Unknown' }}</dd>
                </div>
            </dl>
        </div>

        <!-- URL Information -->
        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">URL & Referer</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">URL</dt>
                    <dd class="mt-1 text-sm text-gray-900 break-all font-mono bg-white p-2 rounded border">{{ $log->url }}</dd>
                </div>
                @if($log->referer)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Referer</dt>
                        <dd class="mt-1 text-sm text-gray-900 break-all font-mono bg-white p-2 rounded border">{{ $log->referer }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- User Agent -->
        @if($log->user_agent)
            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">User Agent</h2>
                <div class="bg-white p-3 rounded border">
                    <code class="text-xs text-gray-800 break-all">{{ $log->user_agent }}</code>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

