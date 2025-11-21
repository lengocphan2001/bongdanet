@extends('layouts.app')

@section('title', 'keobongda.co - Kèo bóng đá - Tỷ lệ kèo nhà cái')

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Kèo bóng đá', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Page Title --}}
                <h1 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">
                    Kèo bóng đá - Tỷ lệ kèo nhà cái 5 hôm nay, Keonhacai trực tuyến
                </h1>

                {{-- Date Selection Tabs --}}
                <div class="bg-slate-800 rounded-lg shadow-md border border-slate-700 p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="flex flex-wrap gap-2 overflow-x-auto">
                        @foreach ($dateOptions ?? [] as $option)
                            <a href="{{ route('odds', ['date' => $option['value']]) }}" 
                               class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 whitespace-nowrap {{ ($date ?? '') == $option['value'] ? 'text-white bg-blue-600 hover:bg-blue-700 shadow-sm' : 'text-gray-300 bg-slate-700 hover:bg-slate-600' }}">
                                {{ $option['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- League Navigation Tabs --}}
                @php
                    $leagueFilters = [
                        ['name' => 'Cúp Châu Á', 'id' => 511],
                        ['name' => 'Ngoại Hạng Anh', 'id' => 583],
                        ['name' => 'Cúp C1', 'id' => 539],
                        ['name' => 'VĐQG Đức', 'id' => 594],
                        ['name' => 'La Liga', 'id' => 637],
                        ['name' => 'VĐQG Ý', 'id' => 719],
                        ['name' => 'VĐQG Pháp', 'id' => 764],
                        ['name' => 'VĐQG Úc', 'id' => 974],
                        ['name' => 'V League', 'id' => 3748],
                        ['name' => 'Cúp C2', 'id' => 541],
                        ['name' => 'Cúp C3', 'id' => 4569],
                        ['name' => 'C2 Châu Á', 'id' => 512],
                    ];
                @endphp
                <div class="bg-slate-800 rounded-lg shadow-md border border-slate-700 p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="flex flex-wrap gap-2 overflow-x-auto">
                        @foreach($leagueFilters as $filter)
                            <a href="{{ route('odds.league', $filter['id']) }}" 
                               class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-gray-600 rounded hover:bg-gray-700 transition-colors whitespace-nowrap">
                                {{ $filter['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Odds Table --}}
                @if(empty($groupedByLeague ?? []))
                    <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 p-8 text-center">
                        <p class="text-gray-400">Không có trận đấu nào</p>
                    </div>
                @else
                    <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs" style="font-size: 11px;">
                                <tbody class="bg-slate-800">
                                    @foreach($groupedByLeague as $leagueGroup)
                                        {{-- League Header --}}
                                    <tr>
                                            <td colspan="5" class="px-4 py-2 bg-slate-900 text-white">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm font-bold">Kèo bóng đá {{ $leagueGroup['league_name'] }}</span>
                                                        @if(!empty($leagueGroup['country_name']))
                                                            <span class="text-xs text-gray-300">- {{ $leagueGroup['country_name'] }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center space-x-4 text-sm">
                                                        @php
                                                            $leagueId = $leagueGroup['league_id'] ?? null;
                                                        @endphp
                                                        @if($leagueId && $leagueId !== 'unknown' && is_numeric($leagueId))
                                                            <a href="{{ route('schedule.league', $leagueId) }}" class="hover:underline">Lịch</a>
                                                            <a href="{{ route('results.league', $leagueId) }}" class="hover:underline">KQ</a>
                                                            <a href="{{ route('standings.show', $leagueId) }}" class="hover:underline">BXH</a>
                                                        @else
                                                            <span class="text-gray-400">Lịch</span>
                                                            <span class="text-gray-400">KQ</span>
                                                            <span class="text-gray-400">BXH</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                    </tr>
                                        
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
                                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                                {{-- Time Column --}}
                                                <td class="px-1.5 py-1.5 whitespace-nowrap text-gray-900 border-r border-gray-200">
                                                    <div class="text-red-500 font-bold">{{ $timeDisplay }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $dateDisplay }}</div>
                                                </td>
                                                
                                                {{-- Teams Column --}}
                                                <td class="px-1.5 py-1.5 text-gray-100 border-r border-slate-600">
                                                    <div class="font-medium">{{ $homeTeam }}</div>
                                                    <div class="font-medium">{{ $awayTeam }}</div>
                                            </td>
                                                
                                                {{-- Asian Handicap Column --}}
                                                <td class="px-1.5 py-1.5 border-r border-slate-600">
                                                    <div class="flex">
                                                        <div class="flex-1 text-center text-green-600 font-medium">
                                                            {{ $asianHandicapValue ?? '-' }}
                                                        </div>
                                                        <div class="flex-1 flex flex-col text-center">
                                                            <div class="text-gray-100">{{ $asianHomeOdds }}</div>
                                                            <div class="text-gray-100">{{ $asianAwayOdds }}</div>
                                                        </div>
                                                </div>
                                            </td>
                                                
                                                {{-- Over/Under Column --}}
                                                <td class="px-1.5 py-1.5 border-r border-slate-600">
                                                    <div class="flex">
                                                        <div class="flex-1 text-center text-green-600 font-medium">
                                                            {{ $overUnderHandicap ?? '-' }}
                                                        </div>
                                                        <div class="flex-1 flex flex-col text-center">
                                                            <div class="text-gray-100">{{ $overOdds }}</div>
                                                            <div class="text-gray-100">{{ $underOdds }}</div>
                                                </div>
                                                </div>
                                            </td>
                                                
                                                {{-- 1X2 Column --}}
                                                <td class="px-1.5 py-1.5 text-center text-gray-100">
                                                    <div>{{ $homeOdds }}</div>
                                                    <div>{{ $drawOdds }}</div>
                                                    <div>{{ $awayOdds }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
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

