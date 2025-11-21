@props([
    'activeItem' => 'Ngoại Hạng Anh',
    'activeLeagueId' => null,
])

@php
    $items = [
        ['label' => 'Kèo Asian Cup', 'id' => 511, 'url' => route('odds.league', 511)],
        ['label' => 'Kèo Ngoại Hạng Anh', 'id' => 583, 'url' => route('odds.league', 583)],
        ['label' => 'Kèo Cúp C1 Châu Âu', 'id' => 539, 'url' => route('odds.league', 539)],
        ['label' => 'Kèo Bundesliga', 'id' => 594, 'url' => route('odds.league', 594)],
        ['label' => 'Kèo La Liga', 'id' => 637, 'url' => route('odds.league', 637)],
        ['label' => 'Kèo Serie A', 'id' => 719, 'url' => route('odds.league', 719)],
        ['label' => 'Kèo Ligue 1', 'id' => 764, 'url' => route('odds.league', 764)],
        ['label' => 'Kèo VĐQG Australia', 'id' => 974, 'url' => route('odds.league', 974)],
    ];
@endphp

<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-xl border border-slate-700/50 rounded-xl overflow-hidden w-full backdrop-blur-sm">
    {{-- Header with gradient bar --}}
    <div class="bg-gradient-to-r from-amber-600/90 to-orange-700/90 px-4 py-3.5 border-b border-amber-500/30">
        <div class="flex items-center space-x-3">
            <div class="w-1 h-6 bg-gradient-to-b from-white/80 to-white/40 rounded-full"></div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wide">KÈO BÓNG ĐÁ</h2>
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
                           class="block py-2.5 px-4 text-xs transition-all duration-200 relative
                                  {{ $index < count($items) - 1 ? 'border-b border-slate-700/50' : '' }}
                                  {{ $isActive ? 'text-amber-400 font-bold bg-gradient-to-r from-amber-600/10 to-orange-700/10' : 'text-gray-300 hover:bg-gradient-to-r hover:from-amber-600/10 hover:to-orange-700/10 hover:text-amber-400' }}">
                            @if($isActive)
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-500 to-orange-600 rounded-r-full"></div>
                                <span class="ml-1">{{ $item['label'] }}</span>
                            @else
                                {{ $item['label'] }}
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>

