@props([
    'activeItem' => 'Ngoại Hạng Anh',
    'activeLeagueId' => null,
])

@php
    $items = [
        ['label' => 'Lịch thi đấu Cúp C1', 'id' => 539, 'url' => route('schedule.league', 539)],
        ['label' => 'Lịch thi đấu Ngoại Hạng Anh', 'id' => 583, 'url' => route('schedule.league', 583)],
        ['label' => 'Lịch thi đấu La Liga', 'id' => 637, 'url' => route('schedule.league', 637)],
        ['label' => 'Lịch thi đấu VĐQG Ý', 'id' => 719, 'url' => route('schedule.league', 719)],
        ['label' => 'Lịch thi đấu VĐQG Pháp', 'id' => 764, 'url' => route('schedule.league', 764)],
        ['label' => 'Lịch thi đấu Cúp C2', 'id' => 541, 'url' => route('schedule.league', 541)],
        ['label' => 'Lịch thi đấu Cúp C3', 'id' => 4569, 'url' => route('schedule.league', 4569)],
        ['label' => 'Lịch thi đấu V League', 'id' => 3748, 'url' => route('schedule.league', 3748)],
        ['label' => 'Lịch thi đấu VĐQG Đức', 'id' => 594, 'url' => route('schedule.league', 594)],
        ['label' => 'Lịch thi đấu VĐQG Úc', 'id' => 974, 'url' => route('schedule.league', 974)],
        ['label' => 'Lịch thi đấu Cúp C1 Châu Á', 'id' => 511, 'url' => route('schedule.league', 511)],
    ];
@endphp

<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-xl border border-slate-700/50 rounded-xl overflow-hidden w-full backdrop-blur-sm">
    {{-- Header with gradient bar --}}
    <div class="bg-gradient-to-r from-blue-600/90 to-blue-700/90 px-4 py-3.5 border-b border-blue-500/30">
        <div class="flex items-center space-x-3">
            <div class="w-1 h-6 bg-gradient-to-b from-white/80 to-white/40 rounded-full"></div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wide">LỊCH THI ĐẤU</h2>
        </div>
    </div>
    
    {{-- List Items --}}
    <div class="bg-slate-800/50">
        <nav>
            <ul>
                @foreach ($items as $index => $item)
                    @php
                        $isActive = ($activeLeagueId && isset($item['id']) && $item['id'] == $activeLeagueId);
                    @endphp
                    <li>
                        <a href="{{ $item['url'] }}" 
                           class="block px-4 py-3 {{ $index < count($items) - 1 ? 'border-b border-slate-700/50' : '' }} hover:bg-gradient-to-r hover:from-blue-600/10 hover:to-blue-700/10 transition-all duration-200 cursor-pointer group relative">
                            @if($isActive)
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-blue-600 rounded-r-full"></div>
                            @endif
                            <p class="text-xs {{ $isActive ? 'text-blue-400 font-bold' : 'text-gray-300 group-hover:text-blue-400' }} leading-relaxed transition-colors duration-200 {{ $isActive ? 'ml-1' : '' }}">
                                {{ $item['label'] }}
                            </p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>

