@props([
    'activeItem' => 'Soi kèo bóng đá TBN',
])

@php
    $items = [
        ['label' => 'Soi kèo bóng đá Anh', 'url' => '#'],
        ['label' => 'Soi kèo bóng đá TBN', 'url' => '#'],
        ['label' => 'Soi kèo bóng đá Ý', 'url' => '#'],
        ['label' => 'Soi kèo Cúp C1', 'url' => '#'],
        ['label' => 'Soi kèo Cúp C2', 'url' => '#'],
        ['label' => 'Soi kèo bóng đá Pháp', 'url' => '#'],
        ['label' => 'Soi kèo V League', 'url' => '#'],
        ['label' => 'Soi kèo Nhật Bản', 'url' => '#'],
        ['label' => 'Soi kèo Hàn Quốc', 'url' => '#'],
        ['label' => 'Soi kèo bóng đá Brazil', 'url' => '#'],
        ['label' => 'Soi kèo Argentina', 'url' => '#'],
        ['label' => 'Soi kèo bóng đá Mỹ', 'url' => '#'],
        ['label' => 'Soi kèo bóng đá Mexico', 'url' => '#'],
        ['label' => 'Soi kèo bóng đá Nga', 'url' => '#'],
        ['label' => 'Soi kèo bóng cỏ', 'url' => '#'],
        ['label' => 'Soi kèo Bồ Đào Nha', 'url' => '#'],
        ['label' => 'Soi kèo Thổ Nhĩ Kỳ', 'url' => '#'],
        ['label' => 'Soi kèo Hà Lan', 'url' => '#'],
        ['label' => 'Soi kèo Scotland', 'url' => '#'],
        ['label' => 'Soi kèo bóng đá Úc', 'url' => '#'],
    ];
@endphp

<div class="bg-white shadow-sm border border-gray-200 overflow-hidden w-fit">
    {{-- Header --}}
    <div class="bg-[#f59e0b] py-3 px-4">
        <h2 class="text-base font-bold text-white uppercase text-center">SOI KÈO BÓNG ĐÁ</h2>
    </div>
    
    {{-- List Items --}}
    <nav>
        <ul>
            @foreach ($items as $index => $item)
                <li class="hover:bg-gray-100">
                    <a href="{{ $item['url'] }}" 
                       class="block py-2 px-4 text-xs text-gray-900 font-medium transition-colors duration-150
                              {{ $index < count($items) - 1 ? 'border-b border-gray-200' : '' }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
