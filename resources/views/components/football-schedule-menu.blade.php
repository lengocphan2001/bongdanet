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

<div class="bg-white shadow-sm border border-gray-200 overflow-hidden w-full">
    {{-- Header with green bar --}}
    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
        <div class="flex items-center space-x-2">
            <div class="w-1 h-5 bg-green-600"></div>
            <h2 class="text-sm font-bold text-black uppercase">LỊCH THI ĐẤU</h2>
        </div>
    </div>
    
    {{-- List Items --}}
    <div class="bg-gray-50">
        <nav>
            <ul>
                @foreach ($items as $index => $item)
                    @php
                        $isActive = ($activeLeagueId && isset($item['id']) && $item['id'] == $activeLeagueId);
                    @endphp
                    <li>
                        <a href="{{ $item['url'] }}" 
                           class="block px-4 py-2 {{ $index < count($items) - 1 ? 'border-b border-gray-200' : '' }} hover:bg-green-50 transition-colors duration-200 cursor-pointer group">
                            <p class="text-xs {{ $isActive ? 'text-red-600 font-bold' : 'text-gray-900 group-hover:text-green-600' }} leading-relaxed">
                                {{ $item['label'] }}
                            </p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>

