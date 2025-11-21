@props([
    'activeItem' => 'Ngoại Hạng Anh',
    'activeLeagueId' => null,
])

@php
    $items = [
        ['label' => 'BXH Cúp C1', 'id' => 539, 'url' => route('standings.show', 539)],
        ['label' => 'BXH Ngoại Hạng Anh', 'id' => 583, 'url' => route('standings.show', 583)],
        ['label' => 'BXH La Liga', 'id' => 637, 'url' => route('standings.show', 637)],
        ['label' => 'BXH VĐQG Ý', 'id' => 719, 'url' => route('standings.show', 719)],
        ['label' => 'BXH VĐQG Pháp', 'id' => 764, 'url' => route('standings.show', 764)],
        ['label' => 'BXH Cúp C2', 'id' => 541, 'url' => route('standings.show', 541)],
        ['label' => 'BXH Cúp C3', 'id' => 4569, 'url' => route('standings.show', 4569)],
        ['label' => 'BXH V League', 'id' => 3748, 'url' => route('standings.show', 3748)],
        ['label' => 'BXH VĐQG Đức', 'id' => 594, 'url' => route('standings.show', 594)],
        ['label' => 'BXH VĐQG Úc', 'id' => 974, 'url' => route('standings.show', 974)],
        ['label' => 'BXH Cúp C1 Châu Á', 'id' => 511, 'url' => route('standings.show', 511)],
    ];
@endphp

<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 rounded-xl shadow-xl border border-slate-700/50 overflow-hidden backdrop-blur-sm w-full">
    {{-- Header with gradient bar --}}
    <div class="bg-gradient-to-r from-purple-800/80 to-purple-900/80 px-4 py-3 border-b border-slate-700/50 backdrop-blur-sm">
        <div class="flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div>
            <h2 class="text-sm font-bold text-white uppercase tracking-tight">BẢNG XẾP HẠNG BÓNG ĐÁ</h2>
        </div>
    </div>
    
    {{-- List Items --}}
    <div class="bg-slate-900/50">
        <nav>
            <ul>
                @foreach ($items as $index => $item)
                    @php
                        $isActive = ($activeLeagueId && isset($item['id']) && $item['id'] == $activeLeagueId);
                    @endphp
                    <li>
                        <a href="{{ $item['url'] }}" 
                           class="block px-4 py-3 {{ $index < count($items) - 1 ? 'border-b border-slate-700/50' : '' }} hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 cursor-pointer group relative">
                            @if($isActive)
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-purple-600 rounded-r-full"></div>
                                <p class="text-xs sm:text-sm text-purple-400 font-bold leading-relaxed transition-colors duration-200 pl-3">
                                    {{ $item['label'] }}
                                </p>
                            @else
                                <p class="text-xs sm:text-sm text-gray-300 group-hover:text-purple-400 leading-relaxed transition-colors duration-200">
                                    {{ $item['label'] }}
                                </p>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>

