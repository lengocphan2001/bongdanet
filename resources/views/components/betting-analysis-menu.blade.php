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

<div class="bg-slate-800 shadow-sm border border-slate-700 overflow-hidden w-fit">
    {{-- Header --}}
    <div class="bg-amber-600 py-3 px-4">
        <h2 class="text-base font-bold text-white uppercase text-center">NHẬN ĐỊNH BÓNG ĐÁ</h2>
    </div>
    
    {{-- List Items --}}
    <nav>
        <ul>
            @foreach ($items as $index => $item)
                <li class="hover:bg-slate-700">
                    <a href="{{ $item['url'] }}" 
                       class="block py-2 px-4 text-xs text-gray-300 font-medium transition-colors duration-150
                              {{ $index < count($items) - 1 ? 'border-b border-slate-700' : '' }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
