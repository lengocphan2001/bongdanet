@extends('layouts.app')

@section('title', 'keobongda.co - Kèo bóng đá - Tỷ lệ kèo nhà cái')

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Main Container --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">
                    {{-- Page Title --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-1 h-8 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Kèo Bóng Đá - Tỷ Lệ Kèo Nhà Cái</span>
                        </h1>
                    </div>
                </div>

                {{-- Bookmaker Slider --}}
                <x-bookmaker-slider />

                {{-- Main Container (continued) --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">

                    {{-- Date Selection Tabs --}}
                    <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-2.5 mb-4 backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide pb-1 -mx-1 px-1">
                            @foreach ($dateOptions ?? [] as $option)
                                @php
                                    $isActive = ($date ?? '') == $option['value'];
                                @endphp
                                <a href="{{ route('odds', ['date' => $option['value']]) }}" 
                                   class="px-2.5 py-1.5 text-xs font-semibold rounded-md whitespace-nowrap flex-shrink-0 transition-all duration-200 {{ $isActive ? 'text-white bg-gradient-to-r from-amber-600 to-orange-700 shadow-md shadow-amber-500/20' : 'text-gray-300 bg-slate-700/50 hover:bg-slate-700 hover:text-white' }}">
                                    {{ $option['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- League Navigation Tabs --}}
                    @php
                        $leagueFilters = [
                            ['name' => 'Cúp Châu Á', 'id' => 511, 'icon' => '🏆'],
                            ['name' => 'Ngoại Hạng Anh', 'id' => 583, 'icon' => '⚽'],
                            ['name' => 'Cúp C1', 'id' => 539, 'icon' => '🏆'],
                            ['name' => 'VĐQG Đức', 'id' => 594, 'icon' => '⚽'],
                            ['name' => 'La Liga', 'id' => 637, 'icon' => '⚽'],
                            ['name' => 'VĐQG Ý', 'id' => 719, 'icon' => '⚽'],
                            ['name' => 'VĐQG Pháp', 'id' => 764, 'icon' => '⚽'],
                            ['name' => 'VĐQG Úc', 'id' => 974, 'icon' => '⚽'],
                            ['name' => 'V League', 'id' => 3748, 'icon' => '⚽'],
                            ['name' => 'Cúp C2', 'id' => 541, 'icon' => '🏆'],
                            ['name' => 'Cúp C3', 'id' => 4569, 'icon' => '🏆'],
                            ['name' => 'C2 Châu Á', 'id' => 512, 'icon' => '🏆'],
                        ];
                    @endphp
                    <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-2.5 mb-4 backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide pb-1 -mx-1 px-1">
                            @foreach($leagueFilters as $filter)
                                <a href="{{ route('odds.league', $filter['id']) }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 whitespace-nowrap flex-shrink-0 hover:scale-105 active:scale-95
                                          {{ $loop->index % 4 === 0 ? 'bg-gradient-to-r from-amber-600 to-orange-700 hover:from-amber-500 hover:to-orange-600 text-white shadow-md shadow-amber-500/20' : 
                                             ($loop->index % 4 === 1 ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white shadow-md shadow-blue-500/20' :
                                             ($loop->index % 4 === 2 ? 'bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-white shadow-md shadow-emerald-500/20' :
                                             'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white shadow-md shadow-purple-500/20')) }}">
                                    <span class="text-[10px]">{{ $filter['icon'] }}</span>
                                    <span>{{ $filter['name'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Odds Table --}}
                    @if(empty($groupedByLeague ?? []))
                        <div class="text-center py-12 sm:py-16">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-400 text-sm sm:text-base font-medium">Không có trận đấu nào</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($groupedByLeague as $leagueGroup)
                                {{-- League Section --}}
                                <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm overflow-hidden">
                                    {{-- League Header --}}
                                    <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 px-4 py-3 border-b border-slate-700/50">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-amber-500/20 text-amber-400 text-xs font-semibold">CƯỢC</span>
                                                <div>
                                                    <h2 class="text-sm font-bold text-white">
                                                        {{ $leagueGroup['league_name'] }}
                                                        @if(!empty($leagueGroup['country_name']))
                                                            <span class="text-xs text-gray-400 font-normal">- {{ $leagueGroup['country_name'] }}</span>
                                                        @endif
                                                    </h2>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                @php
                                                    $leagueId = $leagueGroup['league_id'] ?? null;
                                                @endphp
                                                @if($leagueId && $leagueId !== 'unknown' && is_numeric($leagueId))
                                                    <a href="{{ route('schedule.league', $leagueId) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-105">
                                                        <span>Lịch</span>
                                                    </a>
                                                    <a href="{{ route('results.league', $leagueId) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                                        <span>KQ</span>
                                                    </a>
                                                    <a href="{{ route('standings.show', $leagueId) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 hover:scale-105">
                                                        <span>BXH</span>
                                                    </a>
                                                @else
                                                    <span class="text-xs text-gray-500 px-3 py-1.5">Lịch</span>
                                                    <span class="text-xs text-gray-500 px-3 py-1.5">KQ</span>
                                                    <span class="text-xs text-gray-500 px-3 py-1.5">BXH</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Table --}}
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs">
                                            <thead class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b border-slate-600/50 backdrop-blur-sm">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-bold text-gray-200 uppercase border-r border-slate-600/50">Lịch</th>
                                                    <th class="px-3 py-2 text-left text-xs font-bold text-gray-200 uppercase border-r border-slate-600/50">Trận đấu</th>
                                                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-200 uppercase border-r border-slate-600/50">Chấp</th>
                                                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-200 uppercase border-r border-slate-600/50">Tài xỉu</th>
                                                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-200 uppercase">1X2</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-700/50">
                                        
                                        @foreach($leagueGroup['matches'] as $match)
                                        @php
                                            $matchId = $match['match_id'] ?? null;
                                            $homeTeam = $match['home_team'] ?? '-';
                                            $awayTeam = $match['away_team'] ?? '-';
                                                
                                                // Format time and date
                                            $timeDisplay = $match['time'] ?? '-';
                                                $dateDisplay = '';
                                                if (isset($match['starting_datetime']) && $match['starting_datetime']) {
                                                    try {
                                                        $dt = Carbon\Carbon::parse($match['starting_datetime']);
                                                        $timeDisplay = $dt->format('H:i');
                                                        $dateDisplay = $dt->format('d/m');
                                                    } catch (\Exception $e) {
                                                        // Keep original timeDisplay
                                                    }
                                                }
                                            
                                            // Get odds data
                                            $oddsData = $match['odds_data'] ?? [];
                                            
                                            // Asian Handicap
                                            $asianHandicapValue = null;
                                                $asianHomeOdds = '-';
                                                $asianAwayOdds = '-';
                                            if (!empty($oddsData['Asian Handicap'])) {
                                                $firstBookmaker = array_key_first($oddsData['Asian Handicap']);
                                                if ($firstBookmaker) {
                                                    $ahData = $oddsData['Asian Handicap'][$firstBookmaker];
                                                    $asianHandicapValue = $ahData['handicap'] ?? null;
                                                        $asianHomeOdds = $ahData['home'] ?? '-';
                                                        $asianAwayOdds = $ahData['away'] ?? '-';
                                                }
                                            }
                                            
                                            // Over/Under
                                            $overUnderHandicap = null;
                                                $overOdds = '-';
                                                $underOdds = '-';
                                            if (!empty($oddsData['Over/Under'])) {
                                                $firstBookmaker = array_key_first($oddsData['Over/Under']);
                                                if ($firstBookmaker) {
                                                    $ouData = $oddsData['Over/Under'][$firstBookmaker];
                                                    $overUnderHandicap = $ouData['handicap'] ?? null;
                                                    $overOdds = $ouData['over'] ?? '-';
                                                    $underOdds = $ouData['under'] ?? '-';
                                                }
                                            }
                                            
                                            // 1X2 (European)
                                                $homeOdds = '-';
                                                $drawOdds = '-';
                                                $awayOdds = '-';
                                            if (!empty($oddsData['1X2'])) {
                                                $firstBookmaker = array_key_first($oddsData['1X2']);
                                                if ($firstBookmaker) {
                                                    $euroData = $oddsData['1X2'][$firstBookmaker];
                                                    $homeOdds = $euroData['home'] ?? '-';
                                                    $drawOdds = $euroData['draw'] ?? '-';
                                                    $awayOdds = $euroData['away'] ?? '-';
                                                }
                                            }
                                        @endphp
                                                {{-- Single Row per Match --}}
                                                <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 {{ $matchId ? 'cursor-pointer group' : '' }} {{ ($loop->index % 2 === 0) ? '' : 'bg-slate-800/30' }}"
                                                    @if($matchId) onclick="openMatchModal({{ $matchId }})" @endif>
                                                    {{-- Time Column --}}
                                                    <td class="px-3 py-2 whitespace-nowrap border-r border-slate-600/50">
                                                        <div class="text-xs font-bold text-amber-400 bg-amber-500/10 px-2 py-1 rounded inline-block">{{ $timeDisplay }}</div>
                                                        @if($dateDisplay)
                                                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $dateDisplay }}</div>
                                                        @endif
                                                    </td>
                                                    
                                                    {{-- Teams Column --}}
                                                    <td class="px-3 py-2 border-r border-slate-600/50">
                                                        <div class="text-xs text-white font-medium group-hover:text-amber-400 transition-colors">{{ $homeTeam }}</div>
                                                        <div class="text-xs text-white font-medium group-hover:text-amber-400 transition-colors">{{ $awayTeam }}</div>
                                                    </td>
                                                    
                                                    {{-- Asian Handicap Column --}}
                                                    <td class="px-3 py-2 border-r border-slate-600/50">
                                                        <div class="flex items-center gap-2">
                                                            <div class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded">
                                                                {{ $asianHandicapValue ?? '-' }}
                                                            </div>
                                                            <div class="flex-1 flex flex-col gap-0.5">
                                                                <div class="text-xs text-gray-300 text-center font-semibold">{{ $asianHomeOdds }}</div>
                                                                <div class="text-xs text-gray-300 text-center font-semibold">{{ $asianAwayOdds }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    
                                                    {{-- Over/Under Column --}}
                                                    <td class="px-3 py-2 border-r border-slate-600/50">
                                                        <div class="flex items-center gap-2">
                                                            <div class="text-xs font-bold text-blue-400 bg-blue-500/10 px-1.5 py-0.5 rounded">
                                                                {{ $overUnderHandicap ?? '-' }}
                                                            </div>
                                                            <div class="flex-1 flex flex-col gap-0.5">
                                                                <div class="text-xs text-gray-300 text-center font-semibold">{{ $overOdds }}</div>
                                                                <div class="text-xs text-gray-300 text-center font-semibold">{{ $underOdds }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    
                                                    {{-- 1X2 Column --}}
                                                    <td class="px-3 py-2 text-center">
                                                        <div class="flex flex-col gap-0.5">
                                                            <div class="text-xs text-gray-300 font-semibold">{{ $homeOdds }}</div>
                                                            <div class="text-xs text-gray-300 font-semibold">{{ $drawOdds }}</div>
                                                            <div class="text-xs text-gray-300 font-semibold">{{ $awayOdds }}</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4 order-2">
                <x-odds-menu activeItem="Ngoại Hạng Anh" />
                <x-football-results-menu activeItem="Ngoại Hạng Anh" />
                <x-match-schedule activeDate="H.nay" />
            </aside>
        </div>
    </div>
</div>
@endsection

