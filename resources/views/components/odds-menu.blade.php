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

<div class="bg-slate-800 shadow-sm border border-slate-700 overflow-hidden w-full">
    {{-- Header with green bar --}}
    <div class="bg-slate-700 px-4 py-3 border-b border-slate-600">
        <div class="flex items-center space-x-2">
            <div class="w-1 h-5 bg-blue-500"></div>
            <h2 class="text-sm font-bold text-white uppercase">KÈO BÓNG ĐÁ</h2>
        </div>
    </div>
    
    {{-- List Items --}}
    <div class="bg-slate-800">
        <nav>
            <ul>
                @foreach ($items as $index => $item)
                    @php
                        $isActive = ($activeLeagueId && isset($item['id']) && $item['id'] == $activeLeagueId);
                    @endphp
                    <li>
                        <a href="{{ $item['url'] }}" 
                           class="block px-4 py-2 {{ $index < count($items) - 1 ? 'border-b border-slate-700' : '' }} hover:bg-slate-700 transition-colors duration-200 cursor-pointer group">
                            <p class="text-xs {{ $isActive ? 'text-blue-400 font-bold' : 'text-gray-300 group-hover:text-blue-400' }} leading-relaxed">
                                {{ $item['label'] }}
                            </p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>

