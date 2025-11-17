@props([
    'activeItem' => 'Soi kèo Anh',
])

@php
    $items = [
        ['label' => 'Soi kèo Anh', 'url' => '#'],
        ['label' => 'Soi kèo C1', 'url' => '#'],
        ['label' => 'Soi kèo Đức', 'url' => '#'],
        ['label' => 'Soi kèo TBN', 'url' => '#'],
        ['label' => 'Soi kèo Ý', 'url' => '#'],
        ['label' => 'Soi kèo Pháp', 'url' => '#'],
        ['label' => 'Soi kèo V-League', 'url' => '#'],
        ['label' => 'Soi kèo C2', 'url' => '#'],
        ['label' => 'Soi kèo Hàn Quốc', 'url' => '#'],
        ['label' => 'Soi kèo Nhật Bản', 'url' => '#'],
        ['label' => 'Soi kèo MLS', 'url' => '#'],
        ['label' => 'Soi kèo Mexico', 'url' => '#'],
    ];
@endphp

<div class="bg-white shadow-sm border border-gray-200 overflow-hidden w-full">
    {{-- Header with green bar --}}
    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
        <div class="flex items-center space-x-2">
            <div class="w-1 h-5 bg-green-600"></div>
            <h2 class="text-sm font-bold text-black uppercase">SOI KÈO BÓNG ĐÁ</h2>
        </div>
    </div>

    {{-- List Items --}}
    <nav>
        <ul>
            @foreach ($items as $index => $item)
                <li class="hover:bg-gray-100">
                    <a href="{{ $item['url'] }}"
                       class="block py-2 px-4 text-xs text-gray-900 transition-colors duration-150
                              {{ $index < count($items) - 1 ? 'border-b border-gray-200' : '' }}
                              {{ $item['label'] === $activeItem ? 'text-green-600 font-medium' : '' }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>

