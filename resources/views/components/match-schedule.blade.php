@props([
    'activeDate' => 'H.nay',
])

@php
    use Carbon\Carbon;
    
    $timezone = config('app.timezone', 'UTC');
    $today = Carbon::now($timezone);
    $tomorrow = $today->copy()->addDay();
    
    // Tính toán các ngày trong tuần (7 ngày từ hôm nay)
    $dates = [];
    for ($i = 0; $i < 7; $i++) {
        $date = $today->copy()->addDays($i);
        $dates[] = [
            'label' => $i === 0 ? 'H.nay' : ($i === 1 ? 'N.mai' : $date->format('d/m')),
            'value' => $date->format('Y-m-d'),
            'isActive' => false,
        ];
    }
    
    // Xác định active date
    $currentDate = request()->get('date', $today->format('Y-m-d'));
    foreach ($dates as &$dateItem) {
        if ($dateItem['value'] === $currentDate) {
            $dateItem['isActive'] = true;
        }
    }
@endphp

<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-xl border border-slate-700/50 rounded-xl overflow-hidden w-full backdrop-blur-sm">
    {{-- Header with gradient bar --}}
    <div class="bg-gradient-to-r from-blue-600/90 to-blue-700/90 px-4 py-3.5 border-b border-blue-500/30">
        <div class="flex items-center space-x-3 justify-between">
            <div class="flex items-center gap-3">
                <div class="w-1 h-6 bg-gradient-to-b from-white/80 to-white/40 rounded-full"></div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wide">LỊCH BÓNG ĐÁ</h2>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('results') }}" class="text-xs font-bold text-white hover:text-blue-200 transition-colors duration-200 px-2 py-1 rounded hover:bg-white/10">KQBĐ</a>
                <a href="#" class="text-xs text-blue-200 hover:text-white transition-colors duration-200 px-2 py-1 rounded hover:bg-white/10">Kèo bóng đá</a>
            </div>
        </div>
    </div>

    {{-- Date Navigation Tabs --}}
    <div class="bg-slate-800/50 px-4 py-3 border-b border-slate-700/50">
        <div class="flex items-center space-x-2 overflow-x-auto scrollbar-hide">
            @foreach ($dates as $dateItem)
                <a href="{{ route('schedule', ['date' => $dateItem['value']]) }}" 
                   class="px-3 py-1.5 text-xs font-semibold {{ $dateItem['isActive'] ? 'text-white bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg shadow-blue-500/25' : 'text-gray-300 hover:bg-slate-700/50 hover:text-white' }} rounded-lg whitespace-nowrap transition-all duration-200">
                    {{ $dateItem['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Match List --}}
    <div class="bg-slate-800/50">
        @php
            // Use matches from View Composer (API data) or fallback to empty array
            $matches = $matchScheduleMatches ?? [];
        @endphp
        
        <div class="py-2">
            @forelse ($matches as $index => $match)
                <div class="flex items-center justify-between px-4 py-3.5 {{ $index < count($matches) - 1 ? 'border-b border-slate-700/50' : '' }} hover:bg-gradient-to-r hover:from-blue-600/5 hover:to-blue-700/5 transition-all duration-200 group">
                    {{-- Home Team --}}
                    <div class="flex items-center justify-end space-x-2 flex-1">
                        <span class="text-xs text-gray-100 text-right font-medium group-hover:text-blue-400 transition-colors">{{ $match['home_team'] }}</span>
                        <div class="w-7 h-7 bg-gradient-to-br from-slate-700 to-slate-800 rounded-lg border border-slate-600/50 flex items-center justify-center p-0.5 shadow-sm group-hover:border-blue-500/50 transition-colors">
                            @if ($match['home_logo'])
                                <img src="{{ $match['home_logo'] }}" alt="{{ $match['home_team'] }}" class="w-full h-full rounded object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'text-xs text-gray-500\'>🏴</span>';">
                            @else
                                <span class="text-xs text-gray-500">🏴</span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Time --}}
                    <div class="flex-shrink-0 px-4">
                        <span class="text-xs font-bold text-blue-400 bg-blue-500/10 px-2 py-1 rounded">{{ $match['time'] }}</span>
                    </div>
                    
                    {{-- Away Team --}}
                    <div class="flex items-center space-x-2 flex-1 justify-start">
                        <div class="w-7 h-7 bg-gradient-to-br from-slate-700 to-slate-800 rounded-lg border border-slate-600/50 flex items-center justify-center p-0.5 shadow-sm group-hover:border-blue-500/50 transition-colors">
                            @if ($match['away_logo'])
                                <img src="{{ $match['away_logo'] }}" alt="{{ $match['away_team'] }}" class="w-full h-full rounded object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'text-xs text-gray-500\'>🏴</span>';">
                            @else
                                <span class="text-xs text-gray-500">🏴</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-100 font-medium group-hover:text-blue-400 transition-colors">{{ $match['away_team'] }}</span>
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-800/50 border border-slate-700/50 mb-3">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 font-medium">Không có trận đấu nào hôm nay</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

