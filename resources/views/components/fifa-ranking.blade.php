@props([
    'rankings' => null,
])

@php
    // Default rankings data if not provided
    if (!$rankings) {
        $rankings = [
            ['rank' => 1, 'country' => 'Tây Ban Nha', 'flag' => '🇪🇸', 'change' => 5, 'points' => 1880],
            ['rank' => 2, 'country' => 'Argentina', 'flag' => '🇦🇷', 'change' => 2, 'points' => 1872],
            ['rank' => 3, 'country' => 'Pháp', 'flag' => '🇫🇷', 'change' => -8, 'points' => 1862],
            ['rank' => 4, 'country' => 'Anh', 'flag' => '🏴', 'change' => 3, 'points' => 1824],
            ['rank' => 5, 'country' => 'Bồ Đào Nha', 'flag' => '🇵🇹', 'change' => -1, 'points' => 1778],
            ['rank' => 6, 'country' => 'Hà Lan', 'flag' => '🇳🇱', 'change' => 5, 'points' => 1759],
            ['rank' => 7, 'country' => 'Braxin', 'flag' => '🇧🇷', 'change' => -2, 'points' => 1758],
            ['rank' => 8, 'country' => 'Bỉ', 'flag' => '🇧🇪', 'change' => 0, 'points' => 1740],
            ['rank' => 9, 'country' => 'Ý', 'flag' => '🇮🇹', 'change' => 7, 'points' => 1717],
            ['rank' => 10, 'country' => 'Đức', 'flag' => '🇩🇪', 'change' => 9, 'points' => 1713],
            ['rank' => 111, 'country' => 'Việt Nam', 'flag' => '🇻🇳', 'change' => 13, 'points' => 1183, 'highlight' => true],
        ];
    }
@endphp

<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-xl border border-slate-700/50 rounded-xl overflow-hidden w-full backdrop-blur-sm">
    {{-- Header with gradient --}}
    <div class="bg-gradient-to-r from-purple-600/90 to-indigo-700/90 px-4 py-3.5 border-b border-purple-500/30">
        <div class="flex items-center space-x-3">
            <div class="w-1 h-6 bg-gradient-to-b from-white/80 to-white/40 rounded-full"></div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wide">BXH FIFA (bóng đá nam Việt Nam)</h2>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b border-slate-600/50 backdrop-blur-sm">
                <tr>
                    <th class="px-3 py-2.5 text-center text-xs font-bold text-gray-200 uppercase">XH</th>
                    <th class="px-3 py-2.5 text-left text-xs font-bold text-gray-200 uppercase">Tuyển QG</th>
                    <th class="px-3 py-2.5 text-center text-xs font-bold text-gray-200 uppercase">+/-</th>
                    <th class="px-3 py-2.5 text-right text-xs font-bold text-gray-200 uppercase">Điểm</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @foreach ($rankings as $index => $ranking)
                    <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 {{ ($ranking['highlight'] ?? false) ? 'bg-gradient-to-r from-amber-900/30 to-orange-900/30 border-l-2 border-amber-500' : '' }}">
                        <td class="px-3 py-2.5 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ ($ranking['highlight'] ?? false) ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold' : 'bg-gradient-to-r from-purple-600 to-indigo-700 text-white font-bold' }} text-xs">
                                {{ $ranking['rank'] }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 text-left">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">{{ $ranking['flag'] ?? '' }}</span>
                                <span class="text-xs sm:text-sm text-white font-medium {{ ($ranking['highlight'] ?? false) ? 'text-amber-300 font-bold' : '' }}">{{ $ranking['country'] }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            @php
                                $change = $ranking['change'] ?? 0;
                                $isPositive = $change > 0;
                                $isNegative = $change < 0;
                            @endphp
                            <span class="inline-flex items-center justify-center px-2 py-1 rounded-md text-xs font-semibold
                                {{ $isPositive ? 'bg-emerald-500/20 text-emerald-400' : ($isNegative ? 'bg-red-500/20 text-red-400' : 'bg-gray-500/20 text-gray-400') }}">
                                {{ $change > 0 ? '+' : '' }}{{ $change }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 text-right">
                            <span class="text-xs sm:text-sm text-white font-semibold {{ ($ranking['highlight'] ?? false) ? 'text-amber-300' : '' }}">{{ number_format($ranking['points'] ?? 0) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

