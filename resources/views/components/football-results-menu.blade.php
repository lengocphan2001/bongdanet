@props([
    'activeItem' => 'Ngoại Hạng Anh',
    'activeLeagueId' => null,
])

@php
    $items = [
        ['label' => 'Kết quả bóng đá Cúp C1', 'id' => 539, 'url' => route('results.league', 539)],
        ['label' => 'Kết quả bóng đá Ngoại Hạng Anh', 'id' => 583, 'url' => route('results.league', 583)],
        ['label' => 'Kết quả bóng đá La Liga', 'id' => 637, 'url' => route('results.league', 637)],
        ['label' => 'Kết quả bóng đá VĐQG Ý', 'id' => 719, 'url' => route('results.league', 719)],
        ['label' => 'Kết quả bóng đá VĐQG Pháp', 'id' => 764, 'url' => route('results.league', 764)],
        ['label' => 'Kết quả bóng đá Cúp C2', 'id' => 541, 'url' => route('results.league', 541)],
        ['label' => 'Kết quả bóng đá Cúp C3', 'id' => 4569, 'url' => route('results.league', 4569)],
        ['label' => 'Kết quả bóng đá V League', 'id' => 3748, 'url' => route('results.league', 3748)],
        ['label' => 'Kết quả bóng đá VĐQG Đức', 'id' => 594, 'url' => route('results.league', 594)],
        ['label' => 'Kết quả bóng đá VĐQG Úc', 'id' => 974, 'url' => route('results.league', 974)],
        ['label' => 'Kết quả bóng đá Cúp C1 Châu Á', 'id' => 511, 'url' => route('results.league', 511)],
    ];
@endphp

<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-xl border border-slate-700/50 rounded-xl overflow-hidden w-full backdrop-blur-sm">
    {{-- Header with gradient bar --}}
    <div class="bg-gradient-to-r from-emerald-600/90 to-green-700/90 px-4 py-3.5 border-b border-emerald-500/30">
        <div class="flex items-center space-x-3">
            <div class="w-1 h-6 bg-gradient-to-b from-white/80 to-white/40 rounded-full"></div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wide">KẾT QUẢ BÓNG ĐÁ</h2>
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
                           class="block px-4 py-3 {{ $index < count($items) - 1 ? 'border-b border-slate-700/50' : '' }} hover:bg-gradient-to-r hover:from-emerald-600/10 hover:to-green-700/10 transition-all duration-200 cursor-pointer group relative">
                            @if($isActive)
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-green-600 rounded-r-full"></div>
                            @endif
                            <p class="text-xs {{ $isActive ? 'text-emerald-400 font-bold' : 'text-gray-300 group-hover:text-emerald-400' }} leading-relaxed transition-colors duration-200 {{ $isActive ? 'ml-1' : '' }}">
                                {{ $item['label'] }}
                            </p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>
