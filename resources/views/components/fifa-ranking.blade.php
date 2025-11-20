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

<div class="bg-slate-800 shadow-sm border border-slate-700 overflow-hidden w-full">
    {{-- Header with green background --}}
    <div class="bg-slate-900 px-4 py-3">
        <h2 class="text-base font-bold text-white uppercase text-center">BXH FIFA (bóng đá nam Việt Nam)</h2>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-700">
                <tr>
                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-300 uppercase">XH</th>
                    <th class="px-3 py-2 text-left text-xs font-bold text-gray-300 uppercase">Tuyển QG</th>
                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-300 uppercase">+/-</th>
                    <th class="px-3 py-2 text-right text-xs font-bold text-gray-300 uppercase">Điểm</th>
                </tr>
            </thead>
            <tbody class="bg-slate-800 divide-y divide-slate-700">
                @foreach ($rankings as $index => $ranking)
                    <tr class="{{ ($ranking['highlight'] ?? false) ? 'bg-amber-900' : ($index % 2 === 0 ? 'bg-slate-800' : 'bg-slate-700') }}">
                        <td class="px-3 py-2 text-center text-xs text-gray-100">{{ $ranking['rank'] }}</td>
                        <td class="px-3 py-2 text-left text-xs text-gray-100">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">{{ $ranking['flag'] ?? '' }}</span>
                                <span>{{ $ranking['country'] }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-center text-xs {{ ($ranking['change'] ?? 0) >= 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ ($ranking['change'] ?? 0) > 0 ? '+' : '' }}{{ $ranking['change'] ?? 0 }}
                        </td>
                        <td class="px-3 py-2 text-right text-xs text-gray-100">{{ number_format($ranking['points'] ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

