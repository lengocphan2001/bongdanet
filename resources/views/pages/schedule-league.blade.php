@extends('layouts.app')

@section('title', 'keobongda.co - Lịch thi đấu ' . ($league['name'] ?? ''))

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Lịch thi đấu', 'url' => route('schedule')],
        ['label' => $league['name'] ?? 'N/A', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0">

                {{-- Page Title --}}
                <h1 class="text-2xl font-bold text-white mb-4">
                    Lịch thi đấu {{ $league['name'] ?? 'N/A' }} {{ date('Y') }} - Lịch bóng đá {{ $league['country_name'] ?? '' }} mới nhất
                </h1>

                {{-- League Selection Tabs --}}
                @php
                    $leagueFilters = [
                        ['name' => 'Cúp C1', 'id' => 539, 'icon' => '🏆'],
                        ['name' => 'Ngoại Hạng Anh', 'id' => 583, 'icon' => '⚽'],
                        ['name' => 'La Liga', 'id' => 637, 'icon' => '⚽'],
                        ['name' => 'VĐQG Ý', 'id' => 719, 'icon' => '⚽'],
                        ['name' => 'VĐQG Pháp', 'id' => 764, 'icon' => '⚽'],
                        ['name' => 'Cúp C2', 'id' => 541, 'icon' => '🏆'],
                        ['name' => 'Cúp C3', 'id' => 4569, 'icon' => '🏆'],
                        ['name' => 'V League', 'id' => 3748, 'icon' => '⚽'],
                        ['name' => 'VĐQG Đức', 'id' => 594, 'icon' => '⚽'],
                        ['name' => 'VĐQG Úc', 'id' => 974, 'icon' => '⚽'],
                        ['name' => 'Cúp C1 Châu Á', 'id' => 511, 'icon' => '🏆'],
                    ];
                @endphp
                <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-2.5 mb-4 backdrop-blur-sm">
                    <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide pb-1 -mx-1 px-1">
                        @foreach($leagueFilters as $filter)
                            <a href="{{ route('schedule.league', $filter['id']) }}" 
                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 whitespace-nowrap flex-shrink-0 hover:scale-105 active:scale-95
                                      {{ ($filter['id'] == $leagueId) ? 'ring-2 ring-blue-400 ring-offset-1 ring-offset-slate-800' : '' }}
                                      {{ $loop->index % 4 === 0 ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white shadow-md shadow-blue-500/20' : 
                                         ($loop->index % 4 === 1 ? 'bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-white shadow-md shadow-emerald-500/20' :
                                         ($loop->index % 4 === 2 ? 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white shadow-md shadow-purple-500/20' :
                                         'bg-gradient-to-r from-amber-600 to-orange-700 hover:from-amber-500 hover:to-orange-600 text-white shadow-md shadow-amber-500/20')) }}">
                                <span class="text-[10px]">{{ $filter['icon'] }}</span>
                                <span>{{ $filter['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Matchday Selector (Vòng đấu) - Only show for non-CUP leagues --}}
                @if(!empty($roundIds) && !($isCup ?? false))
                    <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-2.5 mb-4 backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide pb-1 -mx-1 px-1">
                            @foreach($roundIds as $index => $roundId)
                                @php
                                    $roundNumber = $index + 1;
                                    $isActive = ($round == $roundId);
                                @endphp
                                <a href="{{ route('schedule.league', ['leagueId' => $leagueId, 'round' => $roundId]) }}" 
                                   class="px-2.5 py-1.5 text-xs font-bold rounded-md whitespace-nowrap flex-shrink-0 transition-all duration-200 hover:scale-105 active:scale-95
                                          {{ $isActive ? 'text-white bg-gradient-to-r from-amber-600 to-orange-700 shadow-md shadow-amber-500/20 ring-2 ring-amber-400 ring-offset-1 ring-offset-slate-800' : 'text-slate-900 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-300 hover:to-orange-400 shadow-md shadow-amber-500/15' }}">
                                    Vòng {{ $roundNumber }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                    {{-- Schedule Table --}}
                    <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b border-slate-600/50 backdrop-blur-sm">
                                    <tr>
                                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Thời gian</th>
                                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">{{ ($isCup ?? false) ? 'Bảng' : 'Vòng' }}</th>
                                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">Trận đấu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-700/50">
                                    @if(empty($matchesByDate))
                                        <tr>
                                            <td colspan="3" class="px-4 py-12 text-center">
                                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-800/50 border border-slate-700/50 mb-3">
                                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-gray-400 text-sm font-medium">Không có lịch thi đấu nào</p>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($matchesByDate as $matchDate => $matches)
                                            @php
                                                // Format date for display
                                                try {
                                                    $dateObj = Carbon\Carbon::parse($matchDate);
                                                    $dayName = $dateObj->locale('vi')->isoFormat('dddd');
                                                    $formattedDate = $dateObj->format('d/m/Y');
                                                } catch (\Exception $e) {
                                                    $dayName = '';
                                                    $formattedDate = $matchDate;
                                                }
                                            @endphp
                                            {{-- Date Header --}}
                                            <tr class="bg-gradient-to-r from-slate-800/60 to-slate-900/60">
                                                <td colspan="3" class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-1 h-5 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                                                        <span class="text-sm font-bold text-blue-400">
                                                            {{ $dayName }}, Ngày {{ $formattedDate }}
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            @foreach($matches as $match)
                                                @php
                                                    $matchId = $match['match_id'] ?? null;
                                                    // Format time display: "HH:mm"
                                                    $timeDisplay = $match['time'] ?? '-';
                                                    if (isset($match['starting_datetime']) && $match['starting_datetime']) {
                                                        try {
                                                            $dt = Carbon\Carbon::parse($match['starting_datetime']);
                                                            $timeDisplay = $dt->format('H:i');
                                                        } catch (\Exception $e) {
                                                            // Keep original timeDisplay
                                                        }
                                                    }
                                                    // For CUP: use round_name, for regular leagues: use round number
                                                    if ($isCup ?? false) {
                                                        $roundName = $match['round_name'] ?? $match['round'] ?? '-';
                                                        if (empty($roundName) || is_numeric($roundName)) {
                                                            $roundName = $match['round_name'] ?? '-';
                                                        }
                                                    } else {
                                                        $roundId = $match['round_id'] ?? null;
                                                        $roundNumber = '-';
                                                        if ($roundId && isset($roundIds)) {
                                                            $roundIndex = array_search($roundId, $roundIds);
                                                            if ($roundIndex !== false) {
                                                                $roundNumber = $roundIndex + 1;
                                                            }
                                                        }
                                                        $roundName = $roundNumber;
                                                    }
                                                @endphp
                                                <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 {{ $matchId ? 'cursor-pointer group' : '' }}" 
                                                    @if($matchId) onclick="openMatchModal({{ $matchId }})" @endif>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap">
                                                        <div class="text-xs sm:text-sm font-bold text-blue-400 bg-blue-500/10 px-2 py-1 rounded inline-block">
                                                            {{ $timeDisplay }}
                                                        </div>
                                                    </td>
                                                    <td class="px-3 sm:px-4 py-3">
                                                        <div class="text-xs font-semibold text-gray-300">{{ $roundName }}</div>
                                                    </td>
                                                    <td class="px-3 sm:px-4 py-3">
                                                        <div class="flex items-center justify-between gap-3">
                                                            <div class="flex items-center gap-2 flex-1 justify-end group-hover:text-emerald-400 transition-colors">
                                                                <span class="text-xs sm:text-sm text-white font-medium truncate">{{ $match['home_team'] ?? '-' }}</span>
                                                            </div>
                                                            @if($matchId)
                                                                <button onclick="event.stopPropagation(); openMatchModal({{ $matchId }})" 
                                                                        class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-lg min-w-[50px] text-center transition-all duration-200 flex-shrink-0 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                                                    ? - ?
                                                                </button>
                                                            @else
                                                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-lg min-w-[50px] text-center flex-shrink-0 shadow-lg shadow-emerald-500/25">
                                                                    ? - ?
                                                                </div>
                                                            @endif
                                                            <div class="flex items-center gap-2 flex-1 justify-start group-hover:text-emerald-400 transition-colors">
                                                                <span class="text-xs sm:text-sm text-white font-medium truncate">{{ $match['away_team'] ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-fit lg:w-80 flex-shrink-0 space-y-4">
                {{-- LỊCH THI ĐẤU Menu --}}
                <x-football-schedule-menu :activeLeagueId="$leagueId" />

                {{-- Condensed Schedule by Date --}}
                @if(!empty($matchesByDate))
                    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden w-full">
                        {{-- Header --}}
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-1 h-5 bg-green-600"></div>
                                <h2 class="text-sm font-bold text-black uppercase">Lịch thi đấu {{ $league['name'] ?? '' }}</h2>
                            </div>
                        </div>
                        
                        {{-- Schedule List --}}
                        <div class="bg-white">
                            <table class="w-full table-fixed">
                                <colgroup>
                                    <col style="width: auto;">
                                    <col style="width: 60px;">
                                    <col style="width: auto;">
                                </colgroup>
                                <tbody>
                                    @foreach(array_slice($matchesByDate, 0, 2) as $matchDate => $matches)
                                        @php
                                            try {
                                                $dateObj = Carbon\Carbon::parse($matchDate);
                                                $formattedDate = $dateObj->format('d/m/Y');
                                            } catch (\Exception $e) {
                                                $formattedDate = $matchDate;
                                            }
                                        @endphp
                                        
                                        {{-- Date Header Row --}}
                                        <tr>
                                            <td colspan="3" class="bg-gray-400 px-4 py-2">
                                                <h4 class="text-sm font-medium text-white">Ngày {{ $formattedDate }}</h4>
                                            </td>
                                        </tr>
                                        
                                        {{-- Match Schedule Rows --}}
                                        @foreach(array_slice($matches, 0, 5) as $match)
                                            @php
                                                $matchId = $match['match_id'] ?? null;
                                                $timeDisplay = $match['time'] ?? '-';
                                                if (isset($match['starting_datetime']) && $match['starting_datetime']) {
                                                    try {
                                                        $dt = Carbon\Carbon::parse($match['starting_datetime']);
                                                        $timeDisplay = $dt->format('H:i');
                                                    } catch (\Exception $e) {
                                                    }
                                                }
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                                <td class="text-xs text-gray-900 text-right">
                                                    @if($matchId)
                                                        <a href="{{ route('match.detail', $matchId) }}" class="hover:text-blue-600 transition-colors duration-200">
                                                            {{ $match['home_team'] ?? '-' }}
                                                        </a>
                                                    @else
                                                        {{ $match['home_team'] ?? '-' }}
                                                    @endif
                                                </td>
                                                <td class="px-2 py-2 text-xs font-medium text-green-600 text-center whitespace-nowrap">
                                                    {{ $timeDisplay }}
                                                </td>
                                                <td class="text-xs text-gray-900 text-left">
                                                    @if($matchId)
                                                        <a href="{{ route('match.detail', $matchId) }}" class="hover:text-blue-600 transition-colors duration-200">
                                                            {{ $match['away_team'] ?? '-' }}
                                                        </a>
                                                    @else
                                                        {{ $match['away_team'] ?? '-' }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>
@endsection

