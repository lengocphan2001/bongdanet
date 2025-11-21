{{-- Bottom Navigation Menu (Mobile Only) --}}
<nav class="fixed bottom-0 left-0 right-0 z-50 bg-slate-900 border-t border-slate-700 shadow-lg lg:hidden">
    <div class="overflow-x-auto scrollbar-hide">
        <div class="flex items-center gap-0 min-w-max px-2 py-2">
            @php
                $menuItems = [
                    [
                        'route' => 'home.matches',
                        'routePattern' => 'home.matches*',
                        'label' => 'TRẬN ĐẤU',
                        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
                        'shortLabel' => 'TĐ'
                    ],
                    [
                        'route' => 'livescore',
                        'routePattern' => 'livescore*',
                        'label' => 'TRỰC TIẾP',
                        'icon' => '<div class="w-5 h-5 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-500/50"></div>',
                        'shortLabel' => 'TT',
                        'badge' => true
                    ],
                    [
                        'route' => 'schedule',
                        'routePattern' => 'schedule*',
                        'label' => 'LỊCH THI ĐẤU',
                        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>',
                        'shortLabel' => 'LTD'
                    ],
                    [
                        'route' => 'results',
                        'routePattern' => 'results*',
                        'label' => 'KẾT QUẢ',
                        'icon' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8" fill="currentColor"/><circle cx="12" cy="12" r="6" fill="none" stroke="white" stroke-width="0.5"/><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                        'shortLabel' => 'KQ'
                    ],
                    [
                        'route' => 'standings.index',
                        'routePattern' => 'standings*',
                        'label' => 'BẢNG XẾP HẠNG',
                        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>',
                        'shortLabel' => 'BXH'
                    ],
                    [
                        'route' => 'odds',
                        'routePattern' => 'odds*',
                        'label' => 'KÈO BÓNG ĐÁ',
                        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
                        'shortLabel' => 'KBD'
                    ],
                    [
                        'route' => 'predictions',
                        'routePattern' => 'predictions*',
                        'label' => 'TIN TỨC BÓNG ĐÁ',
                        'icon' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="11" cy="11" r="4" fill="currentColor" opacity="0.3"/><path d="M9 9l1 1m4-2l-1 1" stroke="white" stroke-width="1" stroke-linecap="round"/></svg>',
                        'shortLabel' => 'TTBD'
                    ],
                ];
            @endphp

            @foreach($menuItems as $item)
                @php
                    $isActive = request()->routeIs($item['routePattern']);
                    // Always show full label
                    $displayLabel = $item['label'];
                @endphp
                <a href="{{ route($item['route']) }}" 
                   class="flex flex-col items-center justify-center gap-1 px-2 py-2 min-w-[70px] rounded-lg transition-all duration-200 relative group {{ $isActive ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-slate-800' }}">
                    <div class="flex items-center justify-center {{ $isActive ? 'text-white' : 'text-blue-400 group-hover:text-blue-300' }}">
                        {!! $item['icon'] !!}
                    </div>
                    @if(isset($item['badge']) && $item['badge'])
                        <span class="absolute top-1 right-1 w-1.5 h-1.5 bg-red-500 rounded-full animate-ping"></span>
                    @endif
                    <span class="text-[9px] font-semibold text-center leading-tight {{ $isActive ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" style="line-height: 1.1;">
                        {{ $displayLabel }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</nav>

{{-- Add padding to main content to prevent overlap with bottom menu --}}
<style>
    @media (max-width: 1023px) {
        main {
            padding-bottom: 70px;
        }
    }
</style>

