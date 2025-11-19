@props(['activeItem' => null])

<nav class="bg-[#1a5f2f] text-white shadow-lg">
    <div class="container mx-auto px-2 sm:px-4">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center h-auto lg:h-16 py-2 lg:py-0 space-y-2 lg:space-y-0">
            {{-- Logo and Main Menu --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full lg:w-auto">
                {{-- Sidebar Toggle Button (Mobile) --}}
                <button onclick="toggleSidebar()" 
                        class="lg:hidden p-2 rounded-md hover:bg-white/10 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                {{-- Logo/Brand --}}
                <a href="{{ route('admin.predictions.index') }}" class="text-lg sm:text-xl font-bold hover:text-gray-200 transition-colors">
                    <span class="flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Admin Panel</span>
                    </span>
                </a>

                {{-- Main Navigation Menu --}}
                <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                    {{-- Dashboard (placeholder for future) --}}
                    <a href="{{ route('admin.predictions.index') }}" 
                       class="text-sm sm:text-base px-2 py-1 rounded transition-colors {{ $activeItem === 'dashboard' || $activeItem === 'predictions' ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Nhận định</span>
                        </span>
                    </a>

                    {{-- Users Management (placeholder for future) --}}
                    {{-- 
                    <a href="{{ route('admin.users.index') }}" 
                       class="text-sm sm:text-base px-2 py-1 rounded transition-colors {{ $activeItem === 'users' ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>Người dùng</span>
                        </span>
                    </a>
                    --}}

                    {{-- Settings (placeholder for future) --}}
                    {{-- 
                    <a href="{{ route('admin.settings.index') }}" 
                       class="text-sm sm:text-base px-2 py-1 rounded transition-colors {{ $activeItem === 'settings' ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Cài đặt</span>
                        </span>
                    </a>
                    --}}

                    {{-- View Site Link --}}
                    <a href="{{ route('home') }}" 
                       target="_blank"
                       class="text-sm sm:text-base px-2 py-1 rounded transition-colors hover:bg-white/10 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <span>Xem trang web</span>
                    </a>
                </div>
            </div>

            {{-- User Info and Actions --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full lg:w-auto">
                {{-- User Info --}}
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs sm:text-sm font-medium">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-gray-300">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                {{-- Logout Button --}}
                <form method="POST" action="{{ route('admin.logout') }}" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 px-3 sm:px-4 py-1.5 sm:py-2 rounded text-xs sm:text-sm w-full sm:w-auto transition-colors flex items-center justify-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

