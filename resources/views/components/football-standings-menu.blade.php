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

<div class="bg-slate-800 shadow-sm border border-slate-700 overflow-hidden w-full">
    {{-- Header with green bar --}}
    <div class="bg-slate-700 px-4 py-3 border-b border-slate-600">
        <div class="flex items-center space-x-2">
            <div class="w-1 h-5 bg-blue-500"></div>
            <h2 class="text-sm font-bold text-white uppercase">BẢNG XẾP HẠNG BÓNG ĐÁ</h2>
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

