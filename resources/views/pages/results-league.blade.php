@extends('layouts.app')

@section('title', 'keobong88 - Kết quả bóng đá ' . ($league['name'] ?? ''))

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobong88', 'url' => route('home')],
        ['label' => 'Kết quả bóng đá', 'url' => route('results')],
        ['label' => $league['name'] ?? 'N/A', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0">
                {{-- Main Container --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">
                {{-- Page Title --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-1 h-8 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Kết Quả {{ $league['name'] ?? 'N/A' }} {{ date('Y') }}</span>
                </h1>
                    </div>

                    {{-- Current Round Info --}}
                    @if(!empty($currentRoundId) && !($isCup ?? false))
                        @php
                            $currentRoundIndex = array_search($currentRoundId, $roundIds);
                            $currentRoundNumber = $currentRoundIndex !== false ? $currentRoundIndex + 1 : null;
                        @endphp
                        @if($currentRoundNumber)
                            <div class="mb-4 bg-gradient-to-r from-emerald-600/20 to-green-600/20 border border-emerald-500/30 rounded-lg p-3 backdrop-blur-sm">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-emerald-400">Vòng đấu hiện tại:</span>
                                    <span class="text-sm font-bold text-white">Vòng {{ $currentRoundNumber }}</span>
                                </div>
                            </div>
                        @endif
                    @endif

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
                            <a href="{{ route('results.league', $filter['id']) }}" 
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
                                <a href="{{ route('results.league', ['leagueId' => $leagueId, 'round' => $roundId]) }}" 
                                       class="px-2.5 py-1.5 text-xs font-bold rounded-md whitespace-nowrap flex-shrink-0 transition-all duration-200 hover:scale-105 active:scale-95
                                              {{ $isActive ? 'text-white bg-gradient-to-r from-amber-600 to-orange-700 shadow-md shadow-amber-500/20 ring-2 ring-amber-400 ring-offset-1 ring-offset-slate-800' : 'text-slate-900 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-300 hover:to-orange-400 shadow-md shadow-amber-500/15' }}">
                                        Vòng {{ $roundNumber }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Results Table --}}
                    <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                                <thead class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b border-slate-600/50 backdrop-blur-sm">
                                <tr>
                                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Thời gian</th>
                                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">{{ ($isCup ?? false) ? 'Bảng' : 'Vòng' }}</th>
                                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">Trận đấu</th>
                                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">HT</th>
                                </tr>
                            </thead>
                                <tbody class="divide-y divide-slate-700/50">
                                @if(empty($matchesByDate))
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center">
                                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-800/50 border border-slate-700/50 mb-3">
                                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-400 text-sm font-medium">Không có kết quả trận đấu nào</p>
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
                                        @php
                                            $dateKey = 'date-' . str_replace(['-', ' '], ['', ''], $matchDate);
                                        @endphp
                                        <tr class="bg-gradient-to-r from-slate-800/60 to-slate-900/60">
                                            <td colspan="4" class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="toggleDateSection('{{ $dateKey }}')" 
                                                            class="flex-shrink-0 p-1.5 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 rounded-lg transition-all duration-200 group"
                                                            aria-label="Toggle date section">
                                                        <svg id="toggle-icon-{{ $dateKey }}" class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </button>
                                                    <div class="w-1 h-5 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                                                    <span class="text-sm font-bold text-emerald-400">
                                                    {{ $dayName }}, Ngày {{ $formattedDate }}
                                                </span>
                                                    <span class="text-xs text-emerald-400/70 font-medium ml-1">({{ count($matches) }})</span>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        @foreach($matches as $match)
                                            @php
                                                $matchId = $match['match_id'] ?? null;
                                                
                                                // For finished matches, prioritize full_time (ft_score) over score
                                                // because score might contain HT score instead of FT score
                                                $fullTime = $match['full_time'] ?? null;
                                                if (!empty($fullTime)) {
                                                    // full_time is in format "2-1" or "1-2"
                                                    $score = $fullTime;
                                                } else {
                                                    // Fallback to score if full_time not available
                                                    $score = $match['score'] ?? '0-0';
                                                }
                                                
                                                $halfTime = $match['half_time'] ?? '-';
                                                // Format time display: "dd/mm HH:mm" or live minute
                                                $timeDisplay = $match['time'] ?? '-';
                                                if ($match['is_live'] ?? false) {
                                                    $timeDisplay = ($match['time'] ?? "0'") . "'";
                                                } else {
                                                    // Format as "dd/mm HH:mm" if we have date
                                                    if (isset($match['starting_datetime']) && $match['starting_datetime']) {
                                                        try {
                                                            $dt = Carbon\Carbon::parse($match['starting_datetime']);
                                                            $timeDisplay = $dt->format('d/m H:i');
                                                        } catch (\Exception $e) {
                                                            // Keep original timeDisplay
                                                        }
                                                    }
                                                }
                                                // For CUP: use round_name, for regular leagues: use round number
                                                if ($isCup ?? false) {
                                                    // For CUP: prioritize round_name from match data
                                                    $roundName = $match['round_name'] ?? $match['round'] ?? '-';
                                                    // If still empty or just a number, try to get from round data
                                                    if (empty($roundName) || is_numeric($roundName)) {
                                                        $roundName = $match['round_name'] ?? '-';
                                                    }
                                                } else {
                                                    // For regular leagues, find the round number from roundIds
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
                                                $isLive = $match['is_live'] ?? false;
                                            @endphp
                                            <tr class="date-section-row hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 {{ $matchId ? 'cursor-pointer group' : '' }}" 
                                                data-date-section="{{ $dateKey }}"
                                                @if($matchId) onclick="window.location.href='{{ route('match.detail', $matchId) }}'" @endif>
                                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap">
                                                    @if($isLive)
                                                        <div class="text-xs sm:text-sm font-bold text-red-400 bg-red-500/10 px-2 py-1 rounded inline-block">
                                                            {{ $timeDisplay }}
                                                        </div>
                                                    @else
                                                        <div class="text-xs sm:text-sm font-bold text-blue-400 bg-blue-500/10 px-2 py-1 rounded inline-block">
                                                        {{ $timeDisplay }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-3 sm:px-4 py-3">
                                                    @php
                                                        $isCurrentRound = !($isCup ?? false) && isset($currentRoundId) && isset($match['round_id']) && $match['round_id'] == $currentRoundId;
                                                    @endphp
                                                    <div class="text-xs font-semibold {{ $isCurrentRound ? 'text-emerald-400' : 'text-gray-300' }}">
                                                        @if($isCurrentRound)
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-500/20 border border-emerald-500/30">
                                                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                                                {{ $roundName }}
                                                            </span>
                                                        @else
                                                            {{ $roundName }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-3 sm:px-4 py-3">
                                                    <div class="flex items-center justify-between gap-3">
                                                        <div class="flex items-center gap-2 flex-1 justify-end min-w-0 group-hover:text-emerald-400 transition-colors">
                                                            <span class="text-xs sm:text-sm text-white font-medium truncate">{{ $match['home_team'] ?? '-' }}</span>
                                                            @if (!empty($match['home_team_info']['img'] ?? null))
                                                                <div class="w-5 h-5 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                                    <img src="{{ $match['home_team_info']['img'] }}" 
                                                                         alt="{{ $match['home_team'] }}" 
                                                                         class="w-full h-full object-contain"
                                                                         onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[8px] text-white font-bold\'>{{ substr($match['home_team'] ?? 'H', 0, 1) }}</div>';">
                                                                </div>
                                                            @else
                                                                <div class="w-5 h-5 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[8px] text-white font-bold flex-shrink-0">{{ substr($match['home_team'] ?? 'H', 0, 1) }}</div>
                                                            @endif
                                                        </div>
                                                            @if($matchId)
                                                                <a href="{{ route('match.detail', $matchId) }}" 
                                                               onclick="event.stopPropagation();"
                                                               class="inline-block bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-lg min-w-[50px] text-center transition-all duration-200 flex-shrink-0 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                                                    {{ $score }}
                                                                </a>
                                                        @else
                                                            <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-lg min-w-[50px] text-center flex-shrink-0 shadow-lg shadow-emerald-500/25">
                                                                {{ $score }}
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center gap-2 flex-1 justify-start min-w-0 group-hover:text-emerald-400 transition-colors">
                                                            @if (!empty($match['away_team_info']['img'] ?? null))
                                                                <div class="w-5 h-5 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                                    <img src="{{ $match['away_team_info']['img'] }}" 
                                                                         alt="{{ $match['away_team'] }}" 
                                                                         class="w-full h-full object-contain"
                                                                         onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[8px] text-white font-bold\'>{{ substr($match['away_team'] ?? 'A', 0, 1) }}</div>';">
                                                                </div>
                                                            @else
                                                                <div class="w-5 h-5 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[8px] text-white font-bold flex-shrink-0">{{ substr($match['away_team'] ?? 'A', 0, 1) }}</div>
                                                            @endif
                                                            <span class="text-xs sm:text-sm text-white font-medium truncate">{{ $match['away_team'] ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-center">
                                                    <span class="bg-slate-700 text-white text-xs font-bold px-2 py-1 rounded-lg border border-slate-600/50 inline-block">
                                                        {{ $halfTime }}
                                                    </span>
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
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-fit lg:w-80 flex-shrink-0 space-y-4">
                {{-- KẾT QUẢ BÓNG ĐÁ Menu --}}
                <x-football-results-menu :activeLeagueId="$leagueId" />

                {{-- Condensed Results by Date --}}
                @if(!empty($matchesByDate))
                    <div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 rounded-xl shadow-xl border border-slate-700/50 overflow-hidden backdrop-blur-sm w-full">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-emerald-800/80 to-green-900/80 px-4 py-3 border-b border-slate-700/50 backdrop-blur-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                                <h2 class="text-sm font-bold text-white uppercase tracking-tight">Kết quả {{ $league['name'] ?? '' }}</h2>
                            </div>
                        </div>
                        
                        {{-- Results List --}}
                        <div class="bg-slate-900/50">
                            <div class="overflow-y-auto max-h-[600px]">
                                    @foreach(array_slice($matchesByDate, 0, 2) as $matchDate => $matches)
                                        @php
                                            try {
                                                $dateObj = Carbon\Carbon::parse($matchDate);
                                                $formattedDate = $dateObj->format('d/m/Y');
                                            $dayName = $dateObj->locale('vi')->isoFormat('dddd');
                                            } catch (\Exception $e) {
                                                $formattedDate = $matchDate;
                                            $dayName = '';
                                            }
                                        @endphp
                                        
                                    {{-- Date Header --}}
                                    <div class="bg-gradient-to-r from-slate-800/60 to-slate-900/60 px-4 py-2.5 border-b border-slate-700/50">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1 h-4 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                                            <span class="text-xs font-semibold text-emerald-400">
                                                {{ $dayName ? $dayName . ', ' : '' }}Ngày {{ $formattedDate }}
                                            </span>
                                        </div>
                                    </div>
                                        
                                    {{-- Match Results --}}
                                    <div class="divide-y divide-slate-700/50">
                                        @foreach(array_slice($matches, 0, 5) as $match)
                                            @php
                                                $matchId = $match['match_id'] ?? null;
                                                
                                                // For finished matches, prioritize full_time (ft_score) over score
                                                $fullTime = $match['full_time'] ?? null;
                                                if (!empty($fullTime)) {
                                                    $score = $fullTime;
                                                } else {
                                                    $score = $match['score'] ?? '0-0';
                                                }
                                            @endphp
                                            <a href="{{ $matchId ? route('match.detail', $matchId) : '#' }}" 
                                               class="block px-4 py-3 hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 {{ $matchId ? 'cursor-pointer group' : '' }}">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-1.5 flex-1 justify-end min-w-0">
                                                        <span class="text-xs text-gray-300 font-medium truncate group-hover:text-emerald-400 transition-colors">
                                                            {{ $match['home_team'] ?? '-' }}
                                                        </span>
                                                        @if (!empty($match['home_team_info']['img'] ?? null))
                                                            <div class="w-4 h-4 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                                <img src="{{ $match['home_team_info']['img'] }}" 
                                                                     alt="{{ $match['home_team'] }}" 
                                                                     class="w-full h-full object-contain"
                                                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[7px] text-white font-bold\'>{{ substr($match['home_team'] ?? 'H', 0, 1) }}</div>';">
                                                            </div>
                                                    @else
                                                            <div class="w-4 h-4 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[7px] text-white font-bold flex-shrink-0">{{ substr($match['home_team'] ?? 'H', 0, 1) }}</div>
                                                    @endif
                                                    </div>
                                                    <div class="flex-shrink-0 mx-1">
                                                        <span class="inline-block bg-gradient-to-r from-emerald-500 to-green-600 text-white text-xs font-black px-2 py-1 rounded-lg min-w-[40px] text-center shadow-md shadow-emerald-500/25">
                                                            {{ $score }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 flex-1 justify-start min-w-0">
                                                        @if (!empty($match['away_team_info']['img'] ?? null))
                                                            <div class="w-4 h-4 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                                <img src="{{ $match['away_team_info']['img'] }}" 
                                                                     alt="{{ $match['away_team'] }}" 
                                                                     class="w-full h-full object-contain"
                                                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[7px] text-white font-bold\'>{{ substr($match['away_team'] ?? 'A', 0, 1) }}</div>';">
                                                            </div>
                                                    @else
                                                            <div class="w-4 h-4 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[7px] text-white font-bold flex-shrink-0">{{ substr($match['away_team'] ?? 'A', 0, 1) }}</div>
                                                    @endif
                                                        <span class="text-xs text-gray-300 font-medium truncate group-hover:text-emerald-400 transition-colors">
                                                            {{ $match['away_team'] ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                        </a>
                                        @endforeach
                                    </div>
                                    @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>

<script>
function toggleDateSection(dateKey) {
    const rows = document.querySelectorAll(`tr[data-date-section="${dateKey}"]`);
    const icon = document.getElementById('toggle-icon-' + dateKey);
    
    if (!rows.length || !icon) return;
    
    const isHidden = rows[0].classList.contains('hidden');
    
    rows.forEach(row => {
        if (isHidden) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
    
    if (isHidden) {
        icon.style.transform = 'rotate(0deg)';
    } else {
        icon.style.transform = 'rotate(-90deg)';
    }
}
</script>
@endsection

