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

<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-xl border border-slate-700/50 rounded-xl overflow-hidden w-full backdrop-blur-sm">
    {{-- Header with gradient bar --}}
    <div class="bg-gradient-to-r from-purple-600/90 to-indigo-700/90 px-4 py-3.5 border-b border-purple-500/30">
        <div class="flex items-center space-x-3">
            <div class="w-1 h-6 bg-gradient-to-b from-white/80 to-white/40 rounded-full"></div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wide">NHẬN ĐỊNH BÓNG ĐÁ</h2>
        </div>
    </div>

    {{-- List Items --}}
    <div class="bg-slate-800/50">
        <nav>
            <ul>
                @foreach ($items as $index => $item)
                    <li>
                        <a href="{{ $item['url'] }}"
                           class="block py-2.5 px-4 text-xs transition-all duration-200 relative
                                  {{ $index < count($items) - 1 ? 'border-b border-slate-700/50' : '' }}
                                  {{ $item['isActive'] ? 'text-purple-400 font-bold bg-gradient-to-r from-purple-600/10 to-indigo-700/10' : 'text-gray-300 hover:bg-gradient-to-r hover:from-purple-600/10 hover:to-indigo-700/10 hover:text-purple-400' }}">
                            @if($item['isActive'])
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-indigo-600 rounded-r-full"></div>
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

