<header class="fixed top-0 left-0 right-0 z-50 bg-slate-900 shadow-lg">
    {{-- Main Navigation Bar with Logo --}}
    <nav class="relative z-10">
        <div class="container mx-auto px-4">
            {{-- Desktop Navigation with Logo --}}
            <div class="hidden lg:flex items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center mr-8 flex-shrink-0 {{ request()->routeIs('home') ? 'opacity-100' : 'opacity-90 hover:opacity-100' }} transition-opacity">
                    <img src="{{ asset('assets/images/bongdanet-logo.png') }}" alt="Keobongda Logo" class="h-12 w-auto">  
                </a>
                
                {{-- Navigation Links --}}
                <div class="flex items-center space-x-1 flex-1">
                <a href="{{ route('home.matches') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap group {{ request()->routeIs('home.matches*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                    <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-300 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span class="text-sm font-semibold" style="color: white !important;">TRẬN ĐẤU</span>
                </a>

                <a href="{{ route('livescore') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap group relative {{ request()->routeIs('livescore*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                    <div class="w-5 h-5 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-500/50"></div>
                    <span class="text-sm font-semibold" style="color: white !important;">TRỰC TIẾP</span>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                </a>

                <a href="{{ route('schedule') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap group {{ request()->routeIs('schedule*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                    <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-300 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                    </svg>
                    <span class="text-sm font-semibold" style="color: white !important;">LỊCH THI ĐẤU</span>
                </a>

                <a href="{{ route('results') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap group {{ request()->routeIs('results*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                    <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-300 transition-colors" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="8" fill="currentColor"/>
                        <circle cx="12" cy="12" r="6" fill="none" stroke="white" stroke-width="0.5"/>
                        <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-sm font-semibold" style="color: white !important;">KẾT QUẢ</span>
                </a>

                <a href="{{ route('standings.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap group {{ request()->routeIs('standings*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                    <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-300 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span class="text-sm font-semibold" style="color: white !important;">BẢNG XẾP HẠNG</span>
                </a>

                <a href="{{ route('odds') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap group {{ request()->routeIs('odds*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                    <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-300 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span class="text-sm font-semibold" style="color: white !important;">KÈO BÓNG ĐÁ</span>
                </a>

                <a href="{{ route('predictions') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap group {{ request()->routeIs('predictions*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                    <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-300 transition-colors" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="11" cy="11" r="4" fill="currentColor" opacity="0.3"/>
                        <path d="M9 9l1 1m4-2l-1 1" stroke="white" stroke-width="1" stroke-linecap="round"/>
                    </svg>
                    <span class="text-sm font-semibold" style="color: white !important;">TIN TỨC BÓNG ĐÁ</span>
                </a>
                </div>
            </div>

            {{-- Mobile Navigation with Logo and Menu Button --}}
            <div class="lg:hidden flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('assets/images/bongdanet-logo.png') }}" alt="Keobongda Logo" class="h-10 w-auto">  
                </a>
                {{-- Mobile Menu Button --}}
                <button id="mobile-menu-button" class="text-white p-2 hover:bg-slate-800 rounded-lg transition-colors" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            {{-- Mobile Menu Dropdown --}}
            <div id="mobile-menu" class="lg:hidden hidden bg-slate-900">
                <div class="py-4 space-y-1">
                    <a href="{{ route('home.matches') }}" class="flex items-center space-x-2 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('home.matches*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="text-sm font-semibold" style="color: white !important;">TRẬN ĐẤU</span>
                    </a>

                    <a href="{{ route('livescore') }}" class="flex items-center space-x-2 px-4 py-3 rounded-lg transition-all duration-200 relative {{ request()->routeIs('livescore*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                        <div class="w-5 h-5 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-500/50"></div>
                        <span class="text-sm font-semibold" style="color: white !important;">TRỰC TIẾP</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                    </a>

                    <a href="{{ route('schedule') }}" class="flex items-center space-x-2 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('schedule*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                        </svg>
                        <span class="text-sm font-semibold" style="color: white !important;">LỊCH THI ĐẤU</span>
                    </a>

                    <a href="{{ route('results') }}" class="flex items-center space-x-2 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('results*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                        <svg class="w-5 h-5 text-blue-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="8" fill="currentColor"/>
                            <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="text-sm font-semibold" style="color: white !important;">KẾT QUẢ</span>
                    </a>

                    <a href="{{ route('standings.index') }}" class="flex items-center space-x-2 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('standings*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        <span class="text-sm font-semibold" style="color: white !important;">BẢNG XẾP HẠNG</span>
                    </a>

                    <a href="{{ route('odds') }}" class="flex items-center space-x-2 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('odds*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="text-sm font-semibold" style="color: white !important;">KÈO BÓNG ĐÁ</span>
                    </a>

                    <a href="{{ route('predictions') }}" class="flex items-center space-x-2 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('predictions*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}" style="color: white !important;">
                        <svg class="w-5 h-5 text-blue-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="text-sm font-semibold" style="color: white !important;">TIN TỨC BÓNG ĐÁ</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});
</script>
        </div>
    </nav>
</header>

