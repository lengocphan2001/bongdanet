@props(['activeItem' => null])

<div id="admin-sidebar" class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out z-40 lg:translate-x-0 -translate-x-full">
    <div class="h-full overflow-y-auto">
        {{-- Sidebar Header --}}
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Menu</h2>
        </div>

        {{-- Menu Items --}}
        <nav class="p-2 space-y-1">
            {{-- Dashboard (placeholder) --}}
            {{-- 
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $activeItem === 'dashboard' ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
            </a>
            --}}

            {{-- Predictions --}}
            <a href="{{ route('admin.predictions.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $activeItem === 'predictions' ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Nhận định</span>
                @if($activeItem === 'predictions')
                    <span class="ml-auto w-2 h-2 bg-white rounded-full"></span>
                @endif
            </a>

            {{-- Create Prediction --}}
            <a href="{{ route('admin.predictions.create') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.predictions.create') ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tạo nhận định mới</span>
            </a>

            {{-- Divider --}}
            <div class="border-t border-gray-200 my-2"></div>

            {{-- Banners --}}
            <a href="{{ route('admin.banners.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $activeItem === 'banners' ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                </svg>
                <span>Banner Quảng Cáo</span>
                @if($activeItem === 'banners')
                    <span class="ml-auto w-2 h-2 bg-white rounded-full"></span>
                @endif
            </a>

            {{-- Divider --}}
            <div class="border-t border-gray-200 my-2"></div>

            {{-- Content Management Section --}}
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Quản lý nội dung</p>
            </div>

            {{-- Users (placeholder) --}}
            {{-- 
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $activeItem === 'users' ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Người dùng</span>
            </a>
            --}}

            {{-- Categories/Tags (placeholder) --}}
            {{-- 
            <a href="{{ route('admin.categories.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $activeItem === 'categories' ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span>Danh mục</span>
            </a>
            --}}

            {{-- Divider --}}
            <div class="border-t border-gray-200 my-2"></div>

            {{-- System Section --}}
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Hệ thống</p>
            </div>

            {{-- Access Logs --}}
            <a href="{{ route('admin.access-logs.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $activeItem === 'access-logs' ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Nhật ký truy cập</span>
                @if($activeItem === 'access-logs')
                    <span class="ml-auto w-2 h-2 bg-white rounded-full"></span>
                @endif
            </a>

            {{-- Settings (placeholder) --}}
            {{-- 
            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $activeItem === 'settings' ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Cài đặt</span>
            </a>
            --}}

            {{-- Logs (placeholder) --}}
            {{-- 
            <a href="{{ route('admin.logs.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $activeItem === 'logs' ? 'bg-[#1a5f2f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Nhật ký hệ thống</span>
            </a>
            --}}

            {{-- Divider --}}
            <div class="border-t border-gray-200 my-2"></div>

            {{-- Quick Actions --}}
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Thao tác nhanh</p>
            </div>

            {{-- View Site --}}
            <a href="{{ route('home') }}" 
               target="_blank"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-gray-100">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                <span>Xem trang web</span>
                <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
        </nav>
    </div>
</div>

{{-- Sidebar Overlay (for mobile) --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>


<script>
    // Make toggleSidebar available globally
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    };

    // Close sidebar when clicking outside on mobile
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('sidebar-overlay');
        if (overlay) {
            overlay.addEventListener('click', window.toggleSidebar);
        }

        // Close sidebar when clicking a link on mobile
        document.querySelectorAll('#admin-sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    window.toggleSidebar();
                }
            });
        });

        // Close sidebar on window resize if switching to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('admin-sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                if (sidebar && overlay) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                }
            }
        });
    });
</script>

