@props([
    'activeItem' => 'NHẬN ĐỊNH BÓNG ĐÁ TBN',
])

@php
    $items = [
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ Anh', 'url' => '#'],
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ TBN', 'url' => '#'],
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ Ý', 'url' => '#'],
        ['label' => 'nhận định Cúp C1', 'url' => '#'],
        ['label' => 'nhận định Cúp C2', 'url' => '#'],
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ Pháp', 'url' => '#'],
        ['label' => 'nhận định V League', 'url' => '#'],
        ['label' => 'nhận định Nhật Bản', 'url' => '#'],
        ['label' => 'nhận định Hàn Quốc', 'url' => '#'],
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ Brazil', 'url' => '#'],
        ['label' => 'nhận định Argentina', 'url' => '#'],
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ Mỹ', 'url' => '#'],
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ Mexico', 'url' => '#'],
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ Nga', 'url' => '#'],
        ['label' => 'nhận định bóng cỏ', 'url' => '#'],
        ['label' => 'nhận định Bồ Đào Nha', 'url' => '#'],
        ['label' => 'nhận định Thổ Nhĩ Kỳ', 'url' => '#'],
        ['label' => 'nhận định Hà Lan', 'url' => '#'],
        ['label' => 'nhận định Scotland', 'url' => '#'],
        ['label' => 'NHẬN ĐỊNH BÓNG ĐÁ Úc', 'url' => '#'],
    ];
@endphp

<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-xl border border-slate-700/50 rounded-xl overflow-hidden w-fit backdrop-blur-sm">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-amber-600/90 to-orange-700/90 py-3.5 px-4 border-b border-amber-500/30">
        <div class="flex items-center justify-center space-x-2">
            <div class="w-1 h-5 bg-gradient-to-b from-white/80 to-white/40 rounded-full"></div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wide">NHẬN ĐỊNH BÓNG ĐÁ</h2>
        </div>
    </div>
    
    {{-- List Items --}}
    <div class="bg-slate-800/50">
        <nav>
            <ul>
                @foreach ($items as $index => $item)
                    @php
                        $isActive = ($activeItem && str_contains($item['label'], $activeItem));
                    @endphp
                    <li>
                        <a href="{{ $item['url'] }}" 
                           class="block py-2.5 px-4 text-xs {{ $isActive ? 'text-amber-400 font-bold' : 'text-gray-300' }} font-medium transition-all duration-200 hover:bg-gradient-to-r hover:from-amber-600/10 hover:to-orange-700/10 hover:text-amber-400
                                  {{ $index < count($items) - 1 ? 'border-b border-slate-700/50' : '' }} relative">
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
