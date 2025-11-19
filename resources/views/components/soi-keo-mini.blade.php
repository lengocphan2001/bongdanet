@props([
    'activeItem' => null,
])

@php
    // Auto-detect active slug from current route
    $activeSlug = null;
    $currentRoute = request()->route();
    if ($currentRoute && $currentRoute->getName() === 'predictions.league') {
        $activeSlug = $currentRoute->parameter('leagueSlug');
    }

    // If activeItem is provided manually, find its slug
    if ($activeItem !== null && $activeSlug === null) {
        $leagueMap = [
            'Nhận định bóng đá Anh' => 'ngoai-hang-anh',
            'Nhận định bóng đá C1' => 'cup-c1',
            'Nhận định bóng đá Đức' => 'vdqg-duc',
            'Nhận định bóng đá TBN' => 'la-liga',
            'Nhận định bóng đá Ý' => 'vdqg-y',
            'Nhận định bóng đá Pháp' => 'vdqg-phap',
            'Nhận định bóng đá V-League' => 'v-league',
            'Nhận định bóng đá C2' => 'cup-c2',
            'Nhận định bóng đá C3' => 'cup-c3',
            'Nhận định bóng đá C2 Châu Á' => 'c2-chau-a',
            'Nhận định bóng đá C1 Châu Á' => 'cup-c1-chau-a',
        ];
        $activeSlug = $leagueMap[$activeItem] ?? null;
    }

    $items = [
        ['label' => 'Nhận định bóng đá Anh', 'slug' => 'ngoai-hang-anh'],
        ['label' => 'Nhận định bóng đá C1', 'slug' => 'cup-c1'],
        ['label' => 'Nhận định bóng đá Đức', 'slug' => 'vdqg-duc'],
        ['label' => 'Nhận định bóng đá TBN', 'slug' => 'la-liga'],
        ['label' => 'Nhận định bóng đá Ý', 'slug' => 'vdqg-y'],
        ['label' => 'Nhận định bóng đá Pháp', 'slug' => 'vdqg-phap'],
        ['label' => 'Nhận định bóng đá V-League', 'slug' => 'v-league'],
        ['label' => 'Nhận định bóng đá C2', 'slug' => 'cup-c2'],
        ['label' => 'Nhận định bóng đá C3', 'slug' => 'cup-c3'],
        ['label' => 'Nhận định bóng đá C2 Châu Á', 'slug' => 'c2-chau-a'],
        ['label' => 'Nhận định bóng đá C1 Châu Á', 'slug' => 'cup-c1-chau-a'],
    ];

    // Generate URLs and check if active
    foreach ($items as &$item) {
        $item['url'] = route('predictions.league', $item['slug']);
        $item['isActive'] = ($activeSlug !== null && $item['slug'] === $activeSlug);
    }
    unset($item);
@endphp

<div class="bg-white shadow-sm border border-gray-200 overflow-hidden w-full">
    {{-- Header with green bar --}}
    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
        <div class="flex items-center space-x-2">
            <div class="w-1 h-5 bg-green-600"></div>
            <h2 class="text-sm font-bold text-black uppercase">NHẬN ĐỊNH BÓNG ĐÁ</h2>
        </div>
    </div>

    {{-- List Items --}}
    <nav>
        <ul>
            @foreach ($items as $index => $item)
                <li class="hover:bg-gray-100">
                    <a href="{{ $item['url'] }}"
                       class="block py-2 px-4 text-xs transition-colors duration-150
                              {{ $index < count($items) - 1 ? 'border-b border-gray-200' : '' }}
                              {{ $item['isActive'] ? 'text-green-600 font-medium bg-green-50' : 'text-gray-900' }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>

