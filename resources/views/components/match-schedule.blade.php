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

<div class="bg-slate-800 shadow-sm border border-slate-700 overflow-hidden w-full">
    {{-- Header with green bar --}}
    <div class="bg-slate-700 px-4 py-3 border-b border-slate-600">
        <div class="flex items-center space-x-2 justify-between">
            <div class="flex gap-2">
                <div class="w-1 h-5 bg-blue-500"></div>
                <h2 class="text-sm font-bold text-white uppercase">LỊCH BÓNG ĐÁ</h2>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('results') }}" class="text-xs font-bold text-white hover:text-blue-400 transition-colors">KQBĐ</a>
                <a href="#" class="text-xs text-gray-400 hover:text-gray-300">Kèo bóng đá</a>
            </div>
        </div>
        
        {{-- Sub-navigation tabs --}}
        
    </div>

    {{-- Date Navigation Tabs --}}
    <div class="bg-slate-800 px-4 py-2 border-b border-slate-700">
        <div class="flex items-center space-x-2 overflow-x-auto">
            @foreach ($dates as $dateItem)
                <a href="{{ route('schedule', ['date' => $dateItem['value']]) }}" 
                   class="px-3 py-1 text-xs font-medium {{ $dateItem['isActive'] ? 'text-white bg-blue-600' : 'text-gray-300 hover:bg-slate-700' }} rounded whitespace-nowrap transition-colors">
                    {{ $dateItem['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Match List --}}
    <div class="bg-slate-800">
        @php
            // Use matches from View Composer (API data) or fallback to empty array
            $matches = $matchScheduleMatches ?? [];
        @endphp
        
        <div class="py-2">
            @forelse ($matches as $index => $match)
                <div class="flex items-center justify-between px-4 py-3 {{ $index < count($matches) - 1 ? 'border-b border-slate-700' : '' }}">
                    {{-- Home Team --}}
                    <div class="flex items-center justify-end space-x-2 flex-1">
                        <span class="text-xs text-gray-100 text-right">{{ $match['home_team'] }}</span>
                        <div class="w-6 h-6 bg-slate-700 rounded-full flex items-center justify-center">
                            @if ($match['home_logo'])
                                <img src="{{ $match['home_logo'] }}" alt="{{ $match['home_team'] }}" class="w-6 h-6 rounded-full">
                            @else
                                <span class="text-xs text-gray-500">🏴</span>
                            @endif
                        </div>
                        
                    </div>
                    
                    {{-- Time --}}
                    <div class="flex-shrink-0 px-4">
                        <span class="text-xs font-medium text-gray-100">{{ $match['time'] }}</span>
                    </div>
                    
                    {{-- Away Team --}}
                    <div class="flex items-center space-x-2 flex-1 justify-start">
                        <div class="w-6 h-6 bg-slate-700 rounded-full flex items-center justify-center">
                            @if ($match['away_logo'])
                                <img src="{{ $match['away_logo'] }}" alt="{{ $match['away_team'] }}" class="w-6 h-6 rounded-full">
                            @else
                                <span class="text-xs text-gray-500">🏴</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-100">{{ $match['away_team'] }}</span>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <p class="text-xs text-gray-400">Không có trận đấu nào hôm nay</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

