<header class="bg-white">
    {{-- Logo Section - Centered --}}
    <div class="bg-white">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('assets/images/bongdanet-logo.png') }}" alt="Bongdanet Logo" class="w-32 sm:w-40 md:w-48 h-16 sm:h-18 md:h-20">  
                </a>
                {{-- Mobile Menu Button --}}
                <button id="mobile-menu-button" class="lg:hidden text-[#1a5f2f] p-2" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Main Navigation Bar --}}
    <nav class="bg-[#1a5f2f]">
        <div class="container mx-auto px-4">
            {{-- Desktop Navigation --}}
            <div class="hidden lg:flex items-center space-x-6 h-14">
                <a href="{{ route('predictions') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity whitespace-nowrap">
                    <svg class="w-5 h-5 text-[#22c55e]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="11" cy="11" r="4" fill="currentColor" opacity="0.3"/>
                        <path d="M9 9l1 1m4-2l-1 1" stroke="white" stroke-width="1" stroke-linecap="round"/>
                    </svg>
                    <span class="text-sm font-medium">NHẬN ĐỊNH BÓNG ĐÁ</span>
                </a>

                <a href="{{ route('livescore') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity whitespace-nowrap">
                    <div class="w-5 h-5 bg-red-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium">LIVESCORE</span>
                </a>

                <a href="{{ route('schedule') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity whitespace-nowrap">
                    <svg class="w-5 h-5 text-[#22c55e]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                    </svg>
                    <span class="text-sm font-medium">LỊCH THI ĐẤU</span>
                </a>

                <a href="{{ route('results') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity whitespace-nowrap">
                    <svg class="w-5 h-5 text-[#22c55e]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="8" fill="currentColor"/>
                        <circle cx="12" cy="12" r="6" fill="none" stroke="white" stroke-width="0.5"/>
                        <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-sm font-medium">KẾT QUẢ</span>
                </a>

                <a href="{{ route('standings.index') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity whitespace-nowrap">
                    <svg class="w-5 h-5 text-[#22c55e]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span class="text-sm font-medium">BXH BÓNG ĐÁ</span>
                </a>

                <a href="#" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity whitespace-nowrap">
                    <svg class="w-5 h-5 text-[#22c55e]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 17l3-3 4 4 6-6 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <circle cx="18" cy="8" r="2" fill="currentColor"/>
                        <path d="M17.5 7.5l1 1" stroke="white" stroke-width="0.5"/>
                    </svg>
                    <span class="text-sm font-medium">KÈO BÓNG ĐÁ</span>
                </a>
            </div>

            {{-- Mobile Navigation --}}
            <div id="mobile-menu" class="lg:hidden hidden">
                <div class="py-4 space-y-2">
                    <a href="{{ route('predictions') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity px-2 py-2">
                        <svg class="w-5 h-5 text-[#22c55e]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="text-sm font-medium">NHẬN ĐỊNH BÓNG ĐÁ</span>
                    </a>

                    <a href="{{ route('livescore') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity px-2 py-2">
                        <div class="w-5 h-5 bg-red-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium">LIVESCORE</span>
                    </a>

                    <a href="{{ route('schedule') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity px-2 py-2">
                        <svg class="w-5 h-5 text-[#22c55e]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                        </svg>
                        <span class="text-sm font-medium">LỊCH THI ĐẤU</span>
                    </a>

                    <a href="{{ route('results') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity px-2 py-2">
                        <svg class="w-5 h-5 text-[#22c55e]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="8" fill="currentColor"/>
                            <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="text-sm font-medium">KẾT QUẢ</span>
                    </a>

                    <a href="{{ route('standings.index') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity px-2 py-2">
                        <svg class="w-5 h-5 text-[#22c55e]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        <span class="text-sm font-medium">BXH BÓNG ĐÁ</span>
                    </a>

                    <a href="#" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity px-2 py-2">
                        <svg class="w-5 h-5 text-[#22c55e]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 17l3-3 4 4 6-6 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                        <span class="text-sm font-medium">KÈO BÓNG ĐÁ</span>
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
