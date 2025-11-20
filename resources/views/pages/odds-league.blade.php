@extends('layouts.app')

@section('title', 'keobongda.co - Kèo ' . ($league['name'] ?? ''))

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Kèo bóng đá', 'url' => route('odds')],
        ['label' => $league['name'] ?? 'N/A', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0">
                {{-- Page Title --}}
                <h1 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">
                    Kèo {{ $league['name'] ?? 'N/A' }} {{ date('Y') }}, Tỷ lệ kèo bóng đá {{ $league['name'] ?? '' }} tối đêm nay
                </h1>

                {{-- League Selection Tabs --}}
                @php
                    $leagueFilters = [
                        ['name' => 'Cúp Châu Á', 'id' => 511],
                        ['name' => 'Ngoại Hạng Anh', 'id' => 583],
                        ['name' => 'Cúp C1 Châu Âu', 'id' => 539],
                        ['name' => 'Bundesliga', 'id' => 594],
                        ['name' => 'La Liga', 'id' => 637],
                        ['name' => 'Serie A', 'id' => 719],
                        ['name' => 'Ligue 1', 'id' => 764],
                        ['name' => 'VĐQG Australia', 'id' => 974],
                    ];
                @endphp
                <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="flex flex-wrap gap-2 overflow-x-auto">
                        @foreach($leagueFilters as $filter)
                            <a href="{{ route('odds.league', $filter['id']) }}" 
                               class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 whitespace-nowrap {{ ($filter['id'] == $leagueId) ? 'text-white bg-blue-600 hover:bg-blue-700 shadow-sm' : 'text-gray-300 bg-slate-700 border border-slate-600 hover:bg-slate-600' }}">
                                {{ $filter['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Odds Table --}}
                @if(empty($matches))
                    <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 p-8 text-center">
                        <p class="text-gray-400">Không có trận đấu nào</p>
                    </div>
                @else
                    <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs" style="font-size: 11px;">
                                <thead class="bg-slate-900">
                                    <tr>
                                        <th class="px-1.5 py-2 text-left text-xs font-medium text-white uppercase border-r border-gray-300">Lịch</th>
                                        <th class="px-1.5 py-2 text-left text-xs font-medium text-white uppercase border-r border-gray-300">Trận đấu</th>
                                        <th class="px-1.5 py-2 text-center text-xs font-medium text-white uppercase border-r border-gray-300">Chấp</th>
                                        <th class="px-1.5 py-2 text-center text-xs font-medium text-white uppercase border-r border-gray-300">Tài xỉu</th>
                                        <th class="px-1.5 py-2 text-center text-xs font-medium text-white uppercase">1X2</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-slate-800">
                                    @foreach($matches as $match)
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
                                        <tr class="{{ $loop->index % 2 === 0 ? 'bg-slate-800' : 'bg-slate-700' }} {{ !$loop->last ? 'border-b border-slate-600' : '' }}">
                                            {{-- Time Column --}}
                                            <td class="px-1.5 py-1.5 whitespace-nowrap text-gray-100 border-r border-slate-600">
                                                <div class="text-red-500 font-bold">{{ $timeDisplay }}</div>
                                                <div class="text-gray-400 text-xs">{{ $dateDisplay }}</div>
                                            </td>
                                            
                                            {{-- Teams Column --}}
                                            <td class="px-1.5 py-1.5 text-gray-100 border-r border-slate-600">
                                                <div class="font-medium">{{ $homeTeam }}</div>
                                                <div class="font-medium">{{ $awayTeam }}</div>
                                            </td>
                                            
                                            {{-- Asian Handicap Column --}}
                                            <td class="px-1.5 py-1.5 border-r border-slate-600">
                                                <div class="flex">
                                                    <div class="flex-1 text-center text-green-400 font-medium">
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
                                                    <div class="flex-1 text-center text-green-400 font-medium">
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
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4">
                <x-odds-menu :activeLeagueId="$leagueId" activeItem="{{ $league['name'] ?? 'Ngoại Hạng Anh' }}" />
                <x-football-results-menu activeItem="Ngoại Hạng Anh" />
                <x-match-schedule activeDate="H.nay" />
            </aside>
        </div>
    </div>
</div>
@endsection

