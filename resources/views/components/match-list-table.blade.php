@props([
    'liveMatches' => [],
    'upcomingMatches' => [],
    'bookmakers' => [],
    'currentDate' => null,
])

<style>
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    .live-minute-blink {
        animation: blink 1.5s ease-in-out infinite;
        font-weight: 600;
        color: #dc2626; /* red-600 */
    }
</style>

@php
    // Fallback data if not provided
    if (empty($liveMatches) && empty($upcomingMatches)) {
        $liveMatches = [
            [
                'league' => 'HK U22L',
                'status' => '64\'',
                'time' => '64\'',
                'home_team' => 'North District U22',
                'home_team_info' => null,
                'score' => '2-2',
                'away_team' => 'Kowloon City U22',
                'away_team_info' => null,
                'half_time' => '3-0',
                'full_time' => '2-2',
                'stats' => ['flag', 'ball', 'jersey', 'red'],
                'odds' => '0.87 / 0.80 / +0 0.85',
                'is_live' => true,
            ],
            [
                'league' => 'IND SPL',
                'status' => '59\'',
                'time' => '59\'',
                'home_team' => 'Pohkseh SC',
                'home_team_info' => null,
                'score' => '1-1',
                'away_team' => 'Mawtawar SC',
                'away_team_info' => null,
                'half_time' => '0-1',
                'full_time' => '1-1',
                'stats' => ['flag', 'ball'],
                'odds' => '2.10 / 3.20 / 2.85',
                'is_live' => true,
            ],
            [
                'league' => 'UEFA U17',
                'status' => 'HT',
                'time' => 'HT',
                'home_team' => 'Germany U17',
                'home_team_info' => null,
                'score' => '0-4',
                'away_team' => 'Spain U17',
                'away_team_info' => null,
                'half_time' => '0-2',
                'full_time' => '0-4',
                'stats' => ['flag', 'ball', 'jersey'],
                'odds' => '1.50 / 4.00 / 5.50',
                'is_live' => false,
            ],
        ];
        $upcomingMatches = [];
    }
@endphp

<div class="bg-slate-800 shadow-sm border border-slate-700 overflow-visible rounded-lg mb-2">
    {{-- Filter Bar --}}
    <div class=" mt-1">
        <div class="container mx-auto px-4 mb-2">
            <div class="border-b border-blue-500 flex items-center justify-between">
                <div class="flex items-center gap-1">
                    {{-- Tất cả - Active với background xanh đậm, bo góc trái --}}
                    <a href="#" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-tl-lg rounded-tr-lg transition-all duration-200 shadow-sm">
                        Tất cả
                    </a>
                    {{-- Trực tuyến - Inactive --}}
                    <a href="{{ route('livescore') }}" class="px-4 py-2 text-sm font-medium bg-slate-700 rounded-tl-lg rounded-tr-lg text-gray-300 transition-colors hover:bg-slate-600">
                        Trực tuyến
                    </a>
                    {{-- XEM BÓNG - Background đỏ/hồng nhạt với icon radar --}}
                    <a href="#" class="px-4 py-2 text-sm font-medium bg-slate-700 rounded-tl-lg rounded-tr-lg text-white transition-colors flex items-center space-x-1">
                       <div class="bg-red-600 px-2 rounded-sm animate-pulse">
                            <span class="text-xs">((•))</span>
                            <span>XEM BÓNG</span>
                       </div>
                    </a>
                    {{-- Giải đấu - Inactive --}}
                    <button type="button" id="tournament-filter-btn" class="px-4 py-2 text-sm font-medium bg-slate-700 rounded-tl-lg rounded-tr-lg text-gray-300 transition-colors flex items-center space-x-1 cursor-pointer hover:bg-slate-600">
                        <span>Giải đấu</span>
                    </button>
                </div>
                {{-- Icon loa muted ở góc phải với dấu X --}}
                <div class="flex items-center relative">
                    <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.783L4.636 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.636l3.747-3.783a1 1 0 011.617.783zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/>
                    </svg>
                    {{-- Dấu X chéo qua icon loa --}}
                    <svg class="w-4 h-4 text-black absolute inset-0" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2.5">
                        <line x1="2" y1="2" x2="18" y2="18" stroke-linecap="round"/>
                        <line x1="18" y1="2" x2="2" y2="18" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto overflow-y-visible max-w-full -mx-2 sm:mx-0">
        <table class="w-full divide-y divide-gray-200 relative table-auto min-w-[600px]" style="table-layout: auto; width: 100%;">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 sm:px-2 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider w-24 max-w-24">Giải</th>
                    <th class="px-2 sm:px-2 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider whitespace-nowrap w-16">Giờ</th>
                    <th class="px-2 sm:px-2 py-3 text-left text-xs font-bold text-gray-300 text-right uppercase tracking-wider w-32 max-w-32">Chủ</th>
                    <th class="px-2 sm:px-2 py-3 text-center text-xs font-bold text-gray-300 uppercase tracking-wider whitespace-nowrap w-16">Tỷ số</th>
                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider w-32 max-w-32">Khách</th>
                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider whitespace-nowrap w-20">C/H-T</th>
                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider whitespace-nowrap w-20">Số liệu</th>
                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider whitespace-nowrap w-32">
                        <x-betting-provider-dropdown :selected="!empty($bookmakers) ? $bookmakers[0] : 'Bet365'" :bookmakers="$bookmakers" />
                    </th>
                </tr>
            </thead>
            {{-- Live Matches Section --}}
            @if (!empty($liveMatches))
                <tbody id="live-matches-tbody" class="bg-slate-800 divide-y divide-slate-700">
                    @foreach ($liveMatches as $index => $match)
                    @php
                        $leagueName = $match['league'] ?? '';
                        $countryName = $match['country_name'] ?? '';
                        $leagueDisplay = $countryName ? ($countryName . ' - ' . $leagueName) : $leagueName;
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-slate-800' : 'bg-slate-700' }} hover:bg-slate-600 transition-colors" 
                        data-match-id="{{ $match['match_id'] ?? $loop->index }}" 
                        data-odds-data="{{ htmlspecialchars(json_encode($match['odds_data'] ?? []), ENT_QUOTES, 'UTF-8') }}"
                        data-league-name="{{ $leagueName }}"
                        data-league-display="{{ $leagueDisplay }}">
                        {{-- League --}}
                        <td class="px-2 py-3 max-w-24">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-medium text-gray-300 break-words">
                                    {{ $leagueDisplay }}
                                </span>
                            </div>
                        </td>
                        
                        {{-- Time --}}
                        @php
                            // Check if should blink (live match with minute, not HT)
                            $shouldBlink = false;
                            $timeDisplay = $match['time'] ?? '-';
                            if ($match['is_live'] ?? false) {
                                // Check if time contains a minute (e.g., "45'", "90+1'") and not "HT"
                                if (preg_match('/\d+\'/', $timeDisplay) && $timeDisplay !== 'HT') {
                                    $shouldBlink = true;
                                }
                            }
                        @endphp
                        <td class="px-2 py-3 whitespace-nowrap w-16">
                            <span class="text-sm font-medium {{ $shouldBlink ? 'live-minute-blink' : ($match['is_live'] ? 'text-red-500' : 'text-gray-400') }}">
                                {{ $timeDisplay }}
                            </span>
                        </td>
                        
                        {{-- Home Team --}}
                        <td class="px-2 py-3 text-right max-w-32">
                            <div class="flex items-center justify-end space-x-1 flex-wrap">
                                @if (isset($match['home_yellow_cards']) && $match['home_yellow_cards'] > 0)
                                    <div class="bg-yellow-400 text-black text-xs font-bold px-1.5 py-0.5 rounded flex items-center justify-center min-w-[20px]">
                                        {{ $match['home_yellow_cards'] }}
                                    </div>
                                @endif
                                @if (isset($match['home_position']) && $match['home_position'] > 0)
                                    <span class="text-xs text-purple-400 whitespace-nowrap">[{{ $match['home_position'] }}]</span>
                                @endif
                                <span class="text-xs text-gray-100 text-right break-words">{{ $match['home_team'] }}</span>
                            </div>
                        </td>
                        
                        {{-- Score with hover modal --}}
                        <td class="px-2 py-3 whitespace-nowrap text-center relative w-16">
                            <span class="text-sm font-bold text-gray-100 cursor-pointer hover:text-blue-400 relative group" 
                                  data-match-id="{{ $match['match_id'] ?? $loop->index }}" 
                                  data-home-team-id="{{ $match['home_team_id'] ?? '' }}"
                                  data-away-team-id="{{ $match['away_team_id'] ?? '' }}"
                                  data-match-events="{{ htmlspecialchars(json_encode($match['match_events'] ?? []), ENT_QUOTES, 'UTF-8') }}"
                                  data-home-stats="{{ htmlspecialchars(json_encode($match['home_stats'] ?? []), ENT_QUOTES, 'UTF-8') }}"
                                  data-away-stats="{{ htmlspecialchars(json_encode($match['away_stats'] ?? []), ENT_QUOTES, 'UTF-8') }}"
                                  data-score-trigger>
                                {{ $match['score'] }}
                                
                                {{-- Match Details Modal --}}
                                <div class="fixed left-0 top-0 w-96 bg-slate-800 border border-slate-600 rounded-lg shadow-xl z-[99999] hidden group-hover:block overflow-hidden" style="transform: translate(calc(var(--match-modal-x, 0px)), calc(var(--match-modal-y, 0px)));" data-match-modal>
                                    <div class="p-4 max-h-[600px] overflow-y-auto overflow-x-hidden">
                                        {{-- Title --}}
                                        <h3 class="text-lg font-bold text-white mb-4 text-center">Dữ liệu trận đấu</h3>
                                        
                                        {{-- Loading State --}}
                                        <div class="text-center text-gray-400 py-4" data-match-modal-loading>
                                            Đang tải...
                                        </div>
                                        
                                        {{-- Content --}}
                                        <div class="hidden overflow-x-hidden" data-match-modal-content>
                                            {{-- Match Events Section --}}
                                            <div class="mb-4">
                                                <h4 class="text-sm font-bold text-gray-300 mb-2">Match Events</h4>
                                                <div class="border border-slate-600 rounded overflow-hidden">
                                                    <table class="w-full text-xs" style="table-layout: fixed; width: 100%;">
                                                        <thead class="bg-slate-700">
                                                            <tr>
                                                                <th class="px-2 py-2 text-left" style="width: 35%; max-width: 35%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <div class="flex items-start space-x-1" style="flex-wrap: wrap;">
                                                                        @if (!empty($match['home_team_info']['img']))
                                                                            <img src="{{ $match['home_team_info']['img'] }}" alt="{{ $match['home_team'] }}" class="w-4 h-4 flex-shrink-0 mt-0.5" data-home-team-img>
                                                                        @endif
                                                                        <span class="break-words leading-tight" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; display: inline-block; max-width: 100%;" data-home-team-name>{{ $match['home_team'] }}</span>
                                                                    </div>
                                                                </th>
                                                                <th class="px-2 py-2 text-center" style="width: 30%; max-width: 30%;">Phút</th>
                                                                <th class="px-2 py-2 text-right" style="width: 35%; max-width: 35%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <div class="flex items-start justify-end space-x-1" style="flex-wrap: wrap;">
                                                                        <span class="break-words leading-tight text-right" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; display: inline-block; max-width: 100%;" data-away-team-name>{{ $match['away_team'] }}</span>
                                                                        @if (!empty($match['away_team_info']['img']))
                                                                            <img src="{{ $match['away_team_info']['img'] }}" alt="{{ $match['away_team'] }}" class="w-4 h-4 flex-shrink-0 mt-0.5" data-away-team-img>
                                                                        @endif
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200" data-match-events-body>
                                                            {{-- Events will be populated by JavaScript --}}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            {{-- Match Statistics Section --}}
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-700 mb-2">Thống kê trận đấu</h4>
                                                <div class="border border-slate-600 rounded overflow-hidden">
                                                    <table class="w-full text-xs" style="table-layout: fixed; width: 100%;">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-2 py-2 text-left" style="width: 35%; max-width: 35%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <span class="break-words leading-tight" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; display: inline-block; max-width: 100%;" data-home-team-name-stat>{{ $match['home_team'] }}</span>
                                                                </th>
                                                                <th class="px-2 py-2 text-center" style="width: 30%; max-width: 30%;">Chỉ số</th>
                                                                <th class="px-2 py-2 text-right" style="width: 35%; max-width: 35%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <span class="break-words leading-tight" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; display: inline-block; max-width: 100%;" data-away-team-name-stat>{{ $match['away_team'] }}</span>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200" data-match-stats-body>
                                                            {{-- Statistics will be populated by JavaScript --}}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </span>
                        </td>
                        
                        {{-- Away Team --}}
                        <td class="px-2 py-3 text-left max-w-32">
                            <div class="flex items-center space-x-1 text-left flex-wrap">
                                <span class="text-xs text-gray-100 text-left break-words">{{ $match['away_team'] }}</span>
                                @if (isset($match['away_position']) && $match['away_position'] > 0)
                                    <span class="text-xs text-purple-400 whitespace-nowrap">[{{ $match['away_position'] }}]</span>
                                @endif
                                @if (isset($match['away_red_cards']) && $match['away_red_cards'] > 0)
                                    <div class="bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded flex items-center justify-center min-w-[20px] relative">
                                        {{ $match['away_red_cards'] }}
                                        <svg class="w-2 h-2 absolute -top-0.5 -right-0.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                                @if (isset($match['away_yellow_cards']) && $match['away_yellow_cards'] > 0)
                                    <div class="bg-yellow-400 text-black text-xs font-bold px-1.5 py-0.5 rounded flex items-center justify-center min-w-[20px]">
                                        {{ $match['away_yellow_cards'] }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        
                        {{-- Corners (C/H-T) with hover popup --}}
                        <td class="px-2 py-3 whitespace-nowrap text-left relative w-20">
                            @php
                                $homeCorners = $match['home_total_corners'] ?? 0;
                                $awayCorners = $match['away_total_corners'] ?? 0;
                                $homeHtCorners = $match['home_ht_corners'] ?? 0;
                                $awayHtCorners = $match['away_ht_corners'] ?? 0;
                                $cornerEvents = $match['corner_events'] ?? [];
                                $homeTeamId = $match['home_team_id'] ?? null;
                                $awayTeamId = $match['away_team_id'] ?? null;
                                // Get halftime score (not fulltime)
                                $htScore = $match['half_time'] ?? $match['scores']['ht_score'] ?? null;
                                if ($htScore) {
                                    $htScores = explode('-', $htScore);
                                    $homeHtScore = $htScores[0] ?? '0';
                                    $awayHtScore = $htScores[1] ?? '0';
                                } else {
                                    $homeHtScore = '0';
                                    $awayHtScore = '0';
                                }
                            @endphp
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400 cursor-pointer hover:text-blue-400 relative group">
                                    {{ $homeCorners }}-{{ $awayCorners }}
                                    
                                    {{-- Corner Popup --}}
                                    <div class="fixed left-0 top-0 w-80 bg-slate-800 border border-slate-600 rounded-lg shadow-xl z-[99999] hidden group-hover:block" style="transform: translate(calc(var(--popup-x, 0px)), calc(var(--popup-y, 0px)))); max-height: 500px; overflow-y-auto; overflow-x-hidden;" data-corner-popup>
                                <div class="p-4">
                                    {{-- Title --}}
                                    <h3 class="text-lg font-bold text-white mb-3 text-center flex items-center justify-center space-x-2">
                                        <img src="{{ asset('assets/images/stast/corner.svg') }}" alt="Corner" class="w-4 h-4">
                                        <span>PHẠT GÓC</span>
                                    </h3>
                                    
                                    {{-- Team Header --}}
                                    <div class="bg-gray-700 text-white px-3 py-2 rounded-t flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            @if (!empty($match['home_team_info']['img']))
                                                <img src="{{ $match['home_team_info']['img'] }}" alt="{{ $match['home_team'] }}" class="w-6 h-6">
                                            @endif
                                            <span class="text-sm font-medium">{{ $match['home_team'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium">{{ $match['away_team'] }}</span>
                                            @if (!empty($match['away_team_info']['img']))
                                                <img src="{{ $match['away_team_info']['img'] }}" alt="{{ $match['away_team'] }}" class="w-6 h-6">
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- Summary Rows --}}
                                    <div class="space-y-1 mb-3">
                                        {{-- Total Match --}}
                                        <div class="flex items-center text-sm">
                                            <div class="w-12 text-center font-medium text-gray-300">{{ $homeCorners }}</div>
                                            <div class="flex-1 text-center text-gray-400">Cả trận</div>
                                            <div class="w-12 text-center font-medium text-gray-300">{{ $awayCorners }}</div>
                                        </div>
                                        
                                        {{-- First Half --}}
                                        <div class="flex items-center text-sm">
                                            <div class="w-12 text-center font-medium text-gray-300">{{ $homeHtCorners }}</div>
                                            <div class="flex-1 text-center text-gray-400">Hiệp 1</div>
                                            <div class="w-12 text-center font-medium text-gray-300">{{ $awayHtCorners }}</div>
                                        </div>
                                    </div>
                                    
                                    {{-- Individual Corner Events --}}
                                    @if (!empty($cornerEvents))
                                        <div class="border-t border-slate-600 pt-2">
                                            @foreach ($cornerEvents as $event)
                                                <div class="flex items-center text-xs py-1">
                                                    @if (($event['team_id'] ?? null) == $homeTeamId)
                                                        <img src="{{ asset('assets/images/stast/corner.svg') }}" alt="Corner" class="w-4 h-4 mr-2">
                                                        <span class="text-gray-300 font-medium mr-2">{{ $event['minute'] }}</span>
                                                        <span class="flex-1"></span>
                                                    @elseif (($event['team_id'] ?? null) == $awayTeamId)
                                                        <span class="flex-1"></span>
                                                        <span class="text-gray-300 font-medium ml-2">{{ $event['minute'] }}</span>
                                                        <img src="{{ asset('assets/images/stast/corner.svg') }}" alt="Corner" class="w-4 h-4 ml-2">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                                </span>
                                <span class="text-xs text-red-600 font-medium">
                                    {{ $homeHtScore }}-{{ $awayHtScore }}
                                </span>
                            </div>
                        </td>
                        
                        {{-- Stats Icons --}}
                        <td class="px-2 py-3 whitespace-nowrap w-20">
                            <div class="flex items-center space-x-1">
                                @php
                                    $matchId = $match['match_id'] ?? $loop->index;
                                @endphp
                                <a href="{{ route('match.detail', $matchId) }}" class="hover:opacity-80 transition-opacity" aria-label="Flag">
                                    <img src="{{ asset('assets/images/stast/flag.svg') }}" alt="Flag" class="w-4 h-4">
                                </a>
                                
                                <a href="{{ route('match.detail', $matchId) }}" class="hover:opacity-80 transition-opacity" aria-label="Ball">
                                    <img src="{{ asset('assets/images/stast/ball.svg') }}" alt="Ball" class="w-4 h-4">
                                </a>
                                
                                <a href="{{ route('match.detail', $matchId) }}" class="hover:opacity-80 transition-opacity" aria-label="Jersey">
                                    <img src="{{ asset('assets/images/stast/shirt.svg') }}" alt="Jersey" class="w-4 h-4">
                                </a>
                                
                                <a href="{{ route('match.detail', $matchId) }}" class="hover:opacity-80 transition-opacity" aria-label="Lineup">
                                    <img src="{{ asset('assets/images/stast/lineup.svg') }}" alt="Lineup" class="w-4 h-4">
                                </a>
                            </div>
                        </td>
                        
                        {{-- Bet365 Odds --}}
                        <td class="px-2 py-3 whitespace-nowrap text-left odds-cell w-32" data-match-id="{{ $match['match_id'] ?? $loop->index }}">
                            <div class="flex flex-col">
                                <span class="text-xs text-red-600 odds-1x2">{{ $match['odds_1x2'] ?? '- / - / -' }}</span>
                                <span class="text-xs text-blue-600 odds-over-under">{{ $match['odds_over_under'] ?? '- / - / -' }}</span>
                                <span class="text-xs text-yellow-600 odds-asian-handicap">{{ $match['odds_asian_handicap'] ?? '- / - / -' }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            @endif

            {{-- Section Header: Upcoming Matches --}}
            @if (!empty($upcomingMatches))
                <thead class="bg-gray-100" data-upcoming-matches-header>
                    <tr>
                        <td colspan="9" class="px-4 py-3 bg-green-600">
                            <h3 class="text-sm font-bold text-white uppercase">NHỮNG TRẬN SẮP BẮT ĐẦU</h3>
                        </td>
                    </tr>
                </thead>
            @endif

            {{-- Upcoming Matches Section --}}
            @if (!empty($upcomingMatches))
                <tbody id="upcoming-matches-tbody" class="bg-slate-800 divide-y divide-slate-700">
                    @foreach ($upcomingMatches as $index => $match)
                    @php
                        $leagueName = $match['league'] ?? '';
                        $countryName = $match['country_name'] ?? '';
                        $leagueDisplay = $countryName ? ($countryName . ' - ' . $leagueName) : $leagueName;
                    @endphp
                        <tr class="{{ $index % 2 === 0 ? 'bg-slate-800' : 'bg-slate-700' }} hover:bg-slate-600 transition-colors" 
                            data-match-id="{{ $match['match_id'] ?? $loop->index }}" 
                            data-odds-data="{{ htmlspecialchars(json_encode($match['odds_data'] ?? []), ENT_QUOTES, 'UTF-8') }}"
                            data-league-name="{{ $leagueName }}"
                            data-league-display="{{ $leagueDisplay }}">
                            {{-- League --}}
                            <td class="px-2 py-3 max-w-24">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-medium text-gray-300 break-words">
                                        {{ $leagueDisplay }}
                                    </span>
                                </div>
                            </td>
                            
                            {{-- Time --}}
                            <td class="px-2 py-3 whitespace-nowrap w-16">
                                <span class="text-sm font-medium {{ $match['is_live'] ? 'text-red-500' : 'text-gray-400' }}">
                                    {{ $match['time'] }}
                                </span>
                            </td>
                            
                            {{-- Home Team --}}
                            <td class="px-2 py-3 text-right max-w-32">
                                <div class="flex items-center justify-end space-x-1 flex-wrap">
                                    @if (isset($match['home_position']) && $match['home_position'] > 0)
                                        <span class="text-xs text-purple-400 whitespace-nowrap">[{{ $match['home_position'] }}]</span>
                                    @endif
                                    <span class="text-xs text-gray-100 text-right break-words">{{ $match['home_team'] }}</span>
                                </div>
                            </td>
                            
                            {{-- Score (no modal for upcoming matches) --}}
                            <td class="px-2 py-3 whitespace-nowrap text-center relative w-16">
                                <span class="text-sm font-bold text-gray-100">
                                    {{ $match['score'] ?: '-' }}
                                </span>
                            </td>
                        
                        {{-- Away Team --}}
                        <td class="px-2 py-3 text-left max-w-32">
                            <div class="flex items-center space-x-1 text-left flex-wrap">
                                <span class="text-xs text-gray-100 text-left break-words">{{ $match['away_team'] }}</span>
                                @if (isset($match['away_position']) && $match['away_position'] > 0)
                                    <span class="text-xs text-purple-400 whitespace-nowrap">[{{ $match['away_position'] }}]</span>
                                @endif
                            </div>
                        </td>
                        
                            {{-- Corners (C/H-T) - no popup for upcoming matches --}}
                            <td class="px-2 py-3 whitespace-nowrap text-left relative w-20">
                                @php
                                    // Get halftime score (not fulltime)
                                    $htScore = $match['half_time'] ?? $match['scores']['ht_score'] ?? null;
                                    if ($htScore) {
                                        $htScores = explode('-', $htScore);
                                        $homeHtScore = $htScores[0] ?? '0';
                                        $awayHtScore = $htScores[1] ?? '0';
                                        $htScoreDisplay = $homeHtScore . '-' . $awayHtScore;
                                    } else {
                                        $htScoreDisplay = '-';
                                    }
                                @endphp
                                <div class="flex flex-col">
                                    <span class="text-xs text-gray-400">
                                        {{ ($match['home_total_corners'] ?? 0) }}-{{ ($match['away_total_corners'] ?? 0) }}
                                    </span>
                                    <span class="text-xs text-red-600 font-medium">
                                        {{ $htScoreDisplay }}
                                    </span>
                                </div>
                            </td>
                        
                        {{-- Stats Icons --}}
                        <td class="px-2 py-3 whitespace-nowrap w-20">
                            <div class="flex items-center space-x-1">
                                @php
                                    $matchId = $match['match_id'] ?? $loop->index;
                                @endphp
                                <a href="{{ route('match.detail', $matchId) }}" class="hover:opacity-80 transition-opacity" aria-label="Flag">
                                    <img src="{{ asset('assets/images/stast/flag.svg') }}" alt="Flag" class="w-4 h-4">
                                </a>
                                
                                <a href="{{ route('match.detail', $matchId) }}" class="hover:opacity-80 transition-opacity" aria-label="Ball">
                                    <img src="{{ asset('assets/images/stast/ball.svg') }}" alt="Ball" class="w-4 h-4">
                                </a>
                                
                                <a href="{{ route('match.detail', $matchId) }}" class="hover:opacity-80 transition-opacity" aria-label="Jersey">
                                    <img src="{{ asset('assets/images/stast/shirt.svg') }}" alt="Jersey" class="w-4 h-4">
                                </a>
                                
                                <a href="{{ route('match.detail', $matchId) }}" class="hover:opacity-80 transition-opacity" aria-label="Lineup">
                                    <img src="{{ asset('assets/images/stast/lineup.svg') }}" alt="Lineup" class="w-4 h-4">
                                </a>
                            </div>
                        </td>
                        
                        {{-- Bet365 Odds --}}
                            <td class="px-2 py-3 whitespace-nowrap text-left odds-cell w-32" data-match-id="{{ $match['match_id'] ?? $loop->index }}">
                                <div class="flex flex-col">
                                    <span class="text-xs text-red-600 odds-1x2">{{ $match['odds_1x2'] ?? '- / - / -' }}</span>
                                    <span class="text-xs text-blue-600 odds-over-under">{{ $match['odds_over_under'] ?? '- / - / -' }}</span>
                                    <span class="text-xs text-yellow-600 odds-asian-handicap">{{ $match['odds_asian_handicap'] ?? '- / - / -' }}</span>
                                </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            @endif
        </table>
    </div>
</div>


{{-- Tournament Selection Modal --}}
<div id="tournament-modal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-[100000] hidden items-center justify-center">
    <div class="bg-slate-800 rounded-lg shadow-xs max-w-2xl w-full mx-4 max-h-[90vh] flex flex-col">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
            <h3 class="text-lg font-bold text-white">Chọn giải đấu</h3>
            <button type="button" id="tournament-modal-close" class="text-gray-400 hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Modal Body - Tournament List --}}
        <div class="flex-1 overflow-y-auto px-6 py-4">
            @php
                // Collect all unique leagues from liveMatches only
                $allLeagues = [];
                $leagueMap = []; // Map league name to display name (with country)
                
                // Process live matches only
                foreach ($liveMatches as $match) {
                    $leagueName = $match['league'] ?? null;
                    $countryName = $match['country_name'] ?? null;
                    $leagueId = $match['league_id'] ?? null;
                    
                    if ($leagueName) {
                        // Create unique key
                        $leagueKey = $leagueId ? (string)$leagueId : ($leagueName . '_' . ($countryName ?? ''));
                        
                        if (!isset($allLeagues[$leagueKey])) {
                            // Create display name
                            $displayName = $countryName ? ($countryName . ' - ' . $leagueName) : $leagueName;
                            
                            $allLeagues[$leagueKey] = [
                                'name' => $leagueName,
                                'display_name' => $displayName,
                                'country' => $countryName,
                                'league_id' => $leagueId,
                            ];
                            $leagueMap[$leagueName] = $leagueKey;
                        }
                    }
                }
                
                // Sort by display name
                uasort($allLeagues, function($a, $b) {
                    return strcmp($a['display_name'], $b['display_name']);
                });
            @endphp
            
            @if(empty($allLeagues))
                <div class="text-center text-gray-400 py-8">
                    <p>Không có giải đấu nào</p>
                </div>
            @else
                <div class="grid grid-cols-2 gap-2" id="tournament-list">
                    @foreach ($allLeagues as $leagueKey => $league)
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-slate-700 p-2 rounded transition-colors">
                            <input type="checkbox" 
                                   name="tournament[]" 
                                   value="{{ $league['name'] }}" 
                                   data-league-key="{{ $leagueKey }}"
                                   class="tournament-checkbox w-4 h-4 text-blue-600 border-slate-600 rounded focus:ring-blue-500" 
                                   checked>
                            <span class="text-sm text-gray-300">{{ $league['display_name'] }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-slate-700">
            <button type="button" id="select-all-tournaments" class="px-4 py-2 text-sm font-medium text-gray-300 bg-slate-700 border border-slate-600 rounded hover:bg-slate-600 transition-colors">
                Tất cả
            </button>
            <button type="button" id="deselect-all-tournaments" class="px-4 py-2 text-sm font-medium text-gray-300 bg-slate-700 border border-slate-600 rounded hover:bg-slate-600 transition-colors">
                Bỏ chọn
            </button>
            <button type="button" id="confirm-tournaments" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 transition-colors">
                Xác nhận
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('tournament-modal');
    const openBtn = document.getElementById('tournament-filter-btn');
    const closeBtn = document.getElementById('tournament-modal-close');
    const selectAllBtn = document.getElementById('select-all-tournaments');
    const deselectAllBtn = document.getElementById('deselect-all-tournaments');
    const confirmBtn = document.getElementById('confirm-tournaments');
    
    // Function to get all checkboxes (will be updated when modal content changes)
    function getTournamentCheckboxes() {
        return document.querySelectorAll('.tournament-checkbox');
    }

    // Open modal
    openBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        // Close any open dropdowns before opening modal
        const bettingMenu = document.getElementById('betting-provider-menu');
        if (bettingMenu && !bettingMenu.classList.contains('hidden')) {
            bettingMenu.classList.add('hidden');
            const arrow = document.getElementById('betting-provider-arrow');
            if (arrow) arrow.classList.remove('rotate-180');
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    // Close modal
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    closeBtn.addEventListener('click', closeModal);
    
    // Close when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Select all
    selectAllBtn.addEventListener('click', function() {
        getTournamentCheckboxes().forEach(checkbox => {
            checkbox.checked = true;
        });
        filterMatchesByTournaments();
    });

    // Deselect all
    deselectAllBtn.addEventListener('click', function() {
        getTournamentCheckboxes().forEach(checkbox => {
            checkbox.checked = false;
        });
        filterMatchesByTournaments();
    });

    // Filter matches based on selected tournaments (only filter live matches, keep upcoming matches always visible)
    function filterMatchesByTournaments() {
        const checkboxes = getTournamentCheckboxes();
        const selectedLeagues = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value.toLowerCase().trim());
        
        // Get all match rows
        const liveTbody = document.getElementById('live-matches-tbody');
        const upcomingTbody = document.getElementById('upcoming-matches-tbody');
        const upcomingHeader = document.querySelector('[data-upcoming-matches-header]');
        
        let visibleLiveCount = 0;
        
        // Filter live matches only
        if (liveTbody) {
            const liveRows = liveTbody.querySelectorAll('tr');
            liveRows.forEach(row => {
                // Use data attribute for accurate matching
                const leagueName = (row.getAttribute('data-league-name') || '').toLowerCase().trim();
                
                // Check if this league is selected
                const isSelected = selectedLeagues.some(selected => {
                    return leagueName === selected || leagueName.includes(selected) || selected.includes(leagueName);
                });
                
                if (isSelected) {
                    row.style.display = '';
                    visibleLiveCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Keep upcoming matches always visible (don't filter them)
        // Just ensure they are displayed
        if (upcomingTbody) {
            const upcomingRows = upcomingTbody.querySelectorAll('tr');
            upcomingRows.forEach(row => {
                row.style.display = '';
            });
        }
        
        // Show/hide headers based on visible matches
        // Upcoming header should always be visible if there are upcoming matches
        if (upcomingHeader && upcomingTbody) {
            const upcomingRows = upcomingTbody.querySelectorAll('tr');
            upcomingHeader.style.display = upcomingRows.length > 0 ? '' : 'none';
        }
        
        // Show/hide live tbody if empty
        if (liveTbody) {
            liveTbody.style.display = visibleLiveCount > 0 ? '' : 'none';
        }
        // Upcoming tbody should always be visible if it exists
        if (upcomingTbody) {
            const upcomingRows = upcomingTbody.querySelectorAll('tr');
            upcomingTbody.style.display = upcomingRows.length > 0 ? '' : 'none';
        }
    }
    
    // Filter matches when checkbox changes (real-time filtering)
    // Use event delegation to handle dynamically added checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('tournament-checkbox')) {
            filterMatchesByTournaments();
        }
    });
    
    // Initial filter - apply on page load (all selected by default)
    setTimeout(() => {
        filterMatchesByTournaments();
    }, 100);
    
    // Confirm selection (just close modal, filtering already done)
    confirmBtn.addEventListener('click', function() {
        closeModal();
    });

    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>

<script>
// Handle corner popup positioning
(function() {
    const processedPopups = new WeakSet();
    
    function setupCornerPopups() {
        document.querySelectorAll('[data-corner-popup]').forEach(popup => {
            if (processedPopups.has(popup)) return;
            
            const trigger = popup.closest('.group');
            if (!trigger) return;
            
            processedPopups.add(popup);
            
            let hideTimeout = null;
            
            trigger.addEventListener('mouseenter', function(e) {
                // Clear any pending hide timeout
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                    hideTimeout = null;
                }
                
                const rect = trigger.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                const popupWidth = 320; // w-80 = 320px
                const popupHeight = 300; // estimated height
                
                let left = rect.left;
                let top = rect.bottom + 4; // 4px gap
                
                // Adjust if popup would go off right edge
                if (left + popupWidth > viewportWidth) {
                    left = viewportWidth - popupWidth - 10;
                }
                
                // Adjust if popup would go off bottom edge
                if (top + popupHeight > viewportHeight) {
                    top = rect.top - popupHeight - 4; // Show above instead
                }
                
                // Ensure popup doesn't go off left edge
                if (left < 10) {
                    left = 10;
                }
                
                // Ensure popup doesn't go off top edge
                if (top < 10) {
                    top = 10;
                }
                
                popup.style.left = left + 'px';
                popup.style.top = top + 'px';
                popup.classList.remove('hidden');
            });
            
            // Keep popup visible when mouse enters popup
            popup.addEventListener('mouseenter', function() {
                // Clear any pending hide timeout
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                    hideTimeout = null;
                }
                // Ensure popup is visible
                popup.classList.remove('hidden');
            });
            
            trigger.addEventListener('mouseleave', function(e) {
                // Check if mouse is moving to popup
                const relatedTarget = e.relatedTarget;
                if (popup.contains(relatedTarget) || relatedTarget === popup) {
                    // Mouse is moving to popup, keep it visible
                    return;
                }
                
                // Add small delay before hiding to allow smooth transition
                hideTimeout = setTimeout(() => {
                    // Double check mouse is not over trigger or popup
                    const activeElement = document.elementFromPoint(e.clientX, e.clientY);
                    if (!trigger.contains(activeElement) && !popup.contains(activeElement) && activeElement !== popup) {
                        popup.classList.add('hidden');
                    }
                }, 100);
            });
            
            popup.addEventListener('mouseleave', function(e) {
                // Check if mouse is moving to trigger
                const relatedTarget = e.relatedTarget;
                if (trigger.contains(relatedTarget) || relatedTarget === trigger) {
                    // Mouse is moving to trigger, keep popup visible
                    return;
                }
                
                // Add small delay before hiding to allow smooth transition
                hideTimeout = setTimeout(() => {
                    // Double check mouse is not over trigger or popup
                    const activeElement = document.elementFromPoint(e.clientX, e.clientY);
                    if (!trigger.contains(activeElement) && !popup.contains(activeElement) && activeElement !== popup) {
                        popup.classList.add('hidden');
                    }
                }, 100);
            });
        });
    }
    
    document.addEventListener('DOMContentLoaded', setupCornerPopups);
    
    // Re-setup after dynamic content updates
    const observer = new MutationObserver(function() {
        setupCornerPopups();
    });
    
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    // Update bookmakers dropdown
    function updateBookmakersDropdown(bookmakers, selectedBookmaker = null) {
        const menu = document.getElementById('betting-provider-menu');
        if (!menu) return;
        
        const ul = menu.querySelector('ul');
        if (!ul) return;
        
        // Clear existing options
        ul.innerHTML = '';
        
        // Add new bookmakers
        bookmakers.forEach(bookmaker => {
            const li = document.createElement('li');
            const isSelected = selectedBookmaker && bookmaker === selectedBookmaker;
            li.innerHTML = `
                <button
                    type="button"
                    data-value="${bookmaker.replace(/"/g, '&quot;')}"
                    class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-slate-700 hover:text-blue-400 transition-colors betting-provider-option ${isSelected ? 'bg-slate-700 text-blue-400' : ''}"
                >
                    ${bookmaker}
                </button>
            `;
            ul.appendChild(li);
        });
        
        // Re-attach event listeners
        ul.querySelectorAll('.betting-provider-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();
                let value = this.getAttribute('data-value');
                // Decode HTML entities
                value = value.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&');
                
                const selected = document.getElementById('betting-provider-selected');
                if (selected) {
                    selected.textContent = value;
                }
                
                // Update active state
                ul.querySelectorAll('.betting-provider-option').forEach(opt => {
                    opt.classList.remove('bg-blue-50', 'text-blue-600');
                    let optValue = opt.getAttribute('data-value');
                    optValue = optValue.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&');
                    if (optValue === value) {
                        opt.classList.add('bg-blue-50', 'text-blue-600');
                    }
                });
                
                menu.classList.add('hidden');
                const arrow = document.getElementById('betting-provider-arrow');
                if (arrow) arrow.classList.remove('rotate-180');
                
                // Trigger custom event for odds update
                const event = new CustomEvent('bookmakerChanged', { detail: { bookmaker: value } });
                document.dispatchEvent(event);
            });
        });
    }

    // Helper function to get first available bookmaker from odds data
    function getFirstAvailableBookmaker(oddsData) {
        if (!oddsData || typeof oddsData !== 'object') {
            return null;
        }
        
        // Try to find first available bookmaker from any odds type
        const oddsTypes = ['1X2', 'Over/Under', 'Asian Handicap'];
        for (const oddsType of oddsTypes) {
            if (oddsData[oddsType] && typeof oddsData[oddsType] === 'object') {
                const bookmakers = Object.keys(oddsData[oddsType]);
                if (bookmakers.length > 0) {
                    return bookmakers[0];
                }
            }
        }
        
        return null;
    }
    
    // Helper function to get odds for a bookmaker
    function getOddsForBookmaker(oddsData, bookmakerName) {
        if (!oddsData) {
            return {
                '1X2': '- / - / -',
                'Over/Under': '- / - / -',
                'Asian Handicap': '- / - / -'
            };
        }
        
        try {
            let odds;
            if (typeof oddsData === 'string') {
                // Try to parse JSON
                try {
                    odds = JSON.parse(oddsData);
                } catch (e) {
                    // If already an object (from JavaScript), use directly
                    odds = oddsData;
                }
            } else {
                odds = oddsData;
            }
            
            if (!odds || typeof odds !== 'object') {
                return {
                    '1X2': '- / - / -',
                    'Over/Under': '- / - / -',
                    'Asian Handicap': '- / - / -'
                };
            }
            
            // If bookmakerName is not provided or not found, use first available bookmaker
            let actualBookmaker = bookmakerName;
            if (!actualBookmaker) {
                actualBookmaker = getFirstAvailableBookmaker(odds);
            }
            
            let odds1X2 = '- / - / -';
            let oddsOverUnder = '- / - / -';
            let oddsAsianHandicap = '- / - / -';
            
            // Get 1X2 odds - try selected bookmaker first, then fallback to first available
            if (odds['1X2'] && typeof odds['1X2'] === 'object') {
                let bookmaker1X2 = actualBookmaker ? findBookmakerInOdds(odds['1X2'], actualBookmaker) : null;
                if (!bookmaker1X2) {
                    // Fallback to first available bookmaker in 1X2
                    const availableBookmakers = Object.keys(odds['1X2']);
                    if (availableBookmakers.length > 0) {
                        bookmaker1X2 = availableBookmakers[0];
                    }
                }
                if (bookmaker1X2 && odds['1X2'][bookmaker1X2]) {
                    const odds1X2Data = odds['1X2'][bookmaker1X2];
                    if (odds1X2Data && typeof odds1X2Data === 'object') {
                        odds1X2 = `${odds1X2Data.home || '-'} / ${odds1X2Data.draw || '-'} / ${odds1X2Data.away || '-'}`;
                    }
                }
            }
            
            // Get Over/Under odds (with handicap in middle)
            if (odds['Over/Under'] && typeof odds['Over/Under'] === 'object') {
                let bookmakerOU = actualBookmaker ? findBookmakerInOdds(odds['Over/Under'], actualBookmaker) : null;
                if (!bookmakerOU) {
                    // Fallback to first available bookmaker in Over/Under
                    const availableBookmakers = Object.keys(odds['Over/Under']);
                    if (availableBookmakers.length > 0) {
                        bookmakerOU = availableBookmakers[0];
                    }
                }
                if (bookmakerOU && odds['Over/Under'][bookmakerOU]) {
                    const oddsOUData = odds['Over/Under'][bookmakerOU];
                    if (oddsOUData && typeof oddsOUData === 'object') {
                        const handicap = oddsOUData.handicap !== null && oddsOUData.handicap !== undefined ? oddsOUData.handicap : '-';
                        oddsOverUnder = `${oddsOUData.over || '-'} / ${handicap} / ${oddsOUData.under || '-'}`;
                    }
                }
            }
            
            // Get Asian Handicap odds (with handicap in middle)
            if (odds['Asian Handicap'] && typeof odds['Asian Handicap'] === 'object') {
                let bookmakerAH = actualBookmaker ? findBookmakerInOdds(odds['Asian Handicap'], actualBookmaker) : null;
                if (!bookmakerAH) {
                    // Fallback to first available bookmaker in Asian Handicap
                    const availableBookmakers = Object.keys(odds['Asian Handicap']);
                    if (availableBookmakers.length > 0) {
                        bookmakerAH = availableBookmakers[0];
                    }
                }
                if (bookmakerAH && odds['Asian Handicap'][bookmakerAH]) {
                    const oddsAHData = odds['Asian Handicap'][bookmakerAH];
                    if (oddsAHData && typeof oddsAHData === 'object') {
                        const handicap = oddsAHData.handicap !== null && oddsAHData.handicap !== undefined ? oddsAHData.handicap : '-';
                        oddsAsianHandicap = `${oddsAHData.home || '-'} / ${handicap} / ${oddsAHData.away || '-'}`;
                    }
                }
            }
            
            return {
                '1X2': odds1X2,
                'Over/Under': oddsOverUnder,
                'Asian Handicap': oddsAsianHandicap
            };
        } catch (e) {
            console.error('Error parsing odds data:', e, { oddsData, bookmakerName });
            return {
                '1X2': '- / - / -',
                'Over/Under': '- / - / -',
                'Asian Handicap': '- / - / -'
            };
        }
    }

    // Helper to find bookmaker name (case-insensitive, trim spaces, handle variations)
    function findBookmakerInOdds(bookmakersOdds, bookmakerName) {
        if (!bookmakersOdds || !bookmakerName || typeof bookmakersOdds !== 'object') {
            return null;
        }
        
        const normalized = bookmakerName.trim();
        const normalizedLower = normalized.toLowerCase();
        
        // Normalize: remove spaces, special characters for comparison
        const normalizeForComparison = (str) => {
            return str.toLowerCase().replace(/[^a-z0-9]/g, '');
        };
        
        const normalizedCompare = normalizeForComparison(normalized);
        
        // Try exact match first
        if (bookmakersOdds.hasOwnProperty(normalized)) {
            return normalized;
        }
        
        // Try case-insensitive match
        for (const key in bookmakersOdds) {
            if (bookmakersOdds.hasOwnProperty(key)) {
                const keyLower = key.toLowerCase();
                // Exact case-insensitive match
                if (keyLower === normalizedLower) {
                    return key;
                }
                // Normalized comparison (ignore spaces, special chars)
                if (normalizeForComparison(key) === normalizedCompare) {
                    return key;
                }
            }
        }
        
        // Try partial match (contains)
        for (const key in bookmakersOdds) {
            if (bookmakersOdds.hasOwnProperty(key)) {
                const keyNormalized = normalizeForComparison(key);
                if (keyNormalized.includes(normalizedCompare) || normalizedCompare.includes(keyNormalized)) {
                    return key;
                }
            }
        }
        
        // Debug: log available bookmakers if no match found (only once per bookmaker search)
        const logKey = `_logged_${normalized}`;
        if (!findBookmakerInOdds[logKey]) {
            const availableBookmakers = Object.keys(bookmakersOdds);
            if (availableBookmakers.length > 0) {
                console.warn(`Bookmaker "${normalized}" not found in odds data. Available bookmakers:`, availableBookmakers);
            } else {
                console.warn(`Bookmaker "${normalized}" not found. No bookmakers available in this odds type.`);
            }
            findBookmakerInOdds[logKey] = true;
        }
        
        return null;
    }

    // Helper function to build match row HTML
    function buildMatchRow(match, index, selectedBookmaker = null) {
        const matchId = match.match_id || index;
        const rowClass = index % 2 === 0 ? 'bg-slate-800' : 'bg-slate-700';
        const isLive = match.is_live || false;
        
        // Get odds for selected bookmaker
        let oddsData = match.odds_data || null;
        // Ensure oddsData is an object (not string)
        if (oddsData && typeof oddsData === 'string') {
            try {
                oddsData = JSON.parse(oddsData);
            } catch (e) {
                console.warn('Failed to parse odds_data as JSON:', e);
                oddsData = null;
            }
        }
        
        let displayOdds1X2 = match.odds_1x2 || '- / - / -';
        let displayOddsOverUnder = match.odds_over_under || '- / - / -';
        let displayOddsAsianHandicap = match.odds_asian_handicap || '- / - / -';
        
        if (selectedBookmaker && oddsData) {
            const odds = getOddsForBookmaker(oddsData, selectedBookmaker);
            displayOdds1X2 = odds['1X2'];
            displayOddsOverUnder = odds['Over/Under'];
            displayOddsAsianHandicap = odds['Asian Handicap'];
        }
        
        // Build stats icons - always show all 4 icons with links to match detail page
        const matchDetailUrl = `/ket-qua/${matchId}`;
        let statsIcons = '';
        statsIcons += `<a href="${matchDetailUrl}" class="hover:opacity-80 transition-opacity" aria-label="Flag"><img src="{{ asset("assets/images/stast/flag.svg") }}" alt="Flag" class="w-4 h-4"></a>`;
        statsIcons += `<a href="${matchDetailUrl}" class="hover:opacity-80 transition-opacity" aria-label="Ball"><img src="{{ asset("assets/images/stast/ball.svg") }}" alt="Ball" class="w-4 h-4"></a>`;
        statsIcons += `<a href="${matchDetailUrl}" class="hover:opacity-80 transition-opacity" aria-label="Jersey"><img src="{{ asset("assets/images/stast/shirt.svg") }}" alt="Jersey" class="w-4 h-4"></a>`;
        statsIcons += `<a href="${matchDetailUrl}" class="hover:opacity-80 transition-opacity" aria-label="Lineup"><img src="{{ asset("assets/images/stast/lineup.svg") }}" alt="Lineup" class="w-4 h-4"></a>`;
        
        // Build corners display with popup
        const homeCorners = match.home_total_corners || 0;
        const awayCorners = match.away_total_corners || 0;
        const homeHtCorners = match.home_ht_corners || 0;
        const awayHtCorners = match.away_ht_corners || 0;
        const cornerEvents = match.corner_events || [];
        const homeTeamId = match.home_team_id;
        const awayTeamId = match.away_team_id;
        
        // Get halftime score (not fulltime)
        let homeHtScore = '0';
        let awayHtScore = '0';
        const htScore = match.half_time || match.scores?.ht_score || null;
        if (htScore) {
            const htScoreParts = htScore.split('-');
            if (htScoreParts.length === 2) {
                homeHtScore = htScoreParts[0].trim();
                awayHtScore = htScoreParts[1].trim();
            }
        }
        const htScoreDisplay = htScore ? `${homeHtScore}-${awayHtScore}` : '-';
        
        // Build corner events HTML
        const cornerIconPath = '{{ asset("assets/images/stast/corner.svg") }}';
        let cornerEventsHtml = '';
        if (cornerEvents.length > 0) {
            cornerEventsHtml = '<div class="border-t border-slate-600 pt-2">';
            cornerEvents.forEach(event => {
                if (event.team_id == homeTeamId) {
                    cornerEventsHtml += `
                        <div class="flex items-center text-xs py-1">
                            <img src="${cornerIconPath}" alt="Corner" class="w-4 h-4 mr-2">
                            <span class="text-gray-300 font-medium mr-2">${event.minute}</span>
                            <span class="flex-1"></span>
                        </div>
                    `;
                } else if (event.team_id == awayTeamId) {
                    cornerEventsHtml += `
                        <div class="flex items-center text-xs py-1">
                            <span class="flex-1"></span>
                            <span class="text-gray-300 font-medium ml-2">${event.minute}</span>
                            <img src="${cornerIconPath}" alt="Corner" class="w-4 h-4 ml-2">
                        </div>
                    `;
                }
            });
            cornerEventsHtml += '</div>';
        }
        
        const homeTeamImg = match.home_team_info?.img || '';
        const awayTeamImg = match.away_team_info?.img || '';
        const homeTeamName = match.home_team || '';
        const awayTeamName = match.away_team || '';
        
        // Check if match is upcoming (not live, status = 0)
        const isUpcomingMatch = !isLive && (match.status === 0 || match.status === '0');
        
        const cornersDisplay = isUpcomingMatch ? `
            <td class="px-2 py-3 whitespace-nowrap text-left relative w-20">
                <div class="flex flex-col">
                    <span class="text-xs text-gray-400">
                        ${homeCorners}-${awayCorners}
                    </span>
                    <span class="text-xs text-red-600 font-medium">
                        ${htScoreDisplay}
                    </span>
                </div>
            </td>
        ` : `
            <td class="px-2 py-3 whitespace-nowrap text-left relative w-20">
                <div class="flex flex-col">
                    <span class="text-xs text-gray-400 cursor-pointer hover:text-blue-400 relative group">
                        ${homeCorners}-${awayCorners}
                        
                        <div class="fixed left-0 top-0 w-80 bg-slate-800 border border-slate-600 rounded-lg shadow-xl z-[99999] hidden" style="max-height: 500px; overflow-y-auto; overflow-x-hidden;" data-corner-popup>
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-white mb-3 text-center flex items-center justify-center space-x-2">
                            <img src="${cornerIconPath}" alt="Corner" class="w-4 h-4">
                            <span>PHẠT GÓC</span>
                        </h3>
                        
                        <div class="bg-gray-700 text-white px-3 py-2 rounded-t flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                ${homeTeamImg ? `<img src="${homeTeamImg}" alt="${homeTeamName}" class="w-6 h-6">` : ''}
                                <span class="text-sm font-medium">${homeTeamName}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium">${awayTeamName}</span>
                                ${awayTeamImg ? `<img src="${awayTeamImg}" alt="${awayTeamName}" class="w-6 h-6">` : ''}
                            </div>
                        </div>
                        
                        <div class="space-y-1 mb-3">
                            <div class="flex items-center text-sm">
                                <div class="w-12 text-center font-medium text-gray-300">${homeCorners}</div>
                                <div class="flex-1 text-center text-gray-400">Cả trận</div>
                                <div class="w-12 text-center font-medium text-gray-300">${awayCorners}</div>
                            </div>
                            
                            <div class="flex items-center text-sm">
                                <div class="w-12 text-center font-medium text-gray-300">${homeHtCorners}</div>
                                <div class="flex-1 text-center text-gray-400">Hiệp 1</div>
                                <div class="w-12 text-center font-medium text-gray-300">${awayHtCorners}</div>
                            </div>
                        </div>
                        
                        ${cornerEventsHtml}
                    </div>
                </div>
                    </span>
                    <span class="text-xs text-red-600 font-medium">
                        ${htScoreDisplay}
                    </span>
                </div>
            </td>
        `;
        
        // Prepare oddsData for attribute (must be string, escape HTML entities)
        let oddsDataAttr = '';
        if (oddsData) {
            try {
                const oddsDataStr = typeof oddsData === 'string' ? oddsData : JSON.stringify(oddsData);
                // Escape HTML entities for attribute
                oddsDataAttr = oddsDataStr
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            } catch (e) {
                console.error('Error preparing oddsData for attribute:', e);
                oddsDataAttr = '';
            }
        }
        
        const leagueName = match.league || '';
        const countryName = match.country_name || '';
        const leagueDisplay = countryName ? (countryName + ' - ' + leagueName) : leagueName;
        
        return `
            <tr class="${rowClass} hover:bg-gray-100 transition-colors" 
                data-match-id="${matchId}" 
                data-odds-data="${oddsDataAttr}"
                data-league-name="${leagueName}"
                data-league-display="${leagueDisplay}">
                <td class="px-2 py-3 max-w-24">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-medium text-gray-300 break-words">${leagueDisplay}</span>
                    </div>
                </td>
                <td class="px-2 py-3 whitespace-nowrap w-16">
                    ${(() => {
                        const timeDisplay = match.time || '-';
                        // Check if should blink (live match with minute, not HT)
                        const shouldBlink = isLive && /\d+'/.test(timeDisplay) && timeDisplay !== 'HT';
                        const timeClass = shouldBlink ? 'live-minute-blink' : (isLive ? 'text-red-500' : 'text-gray-400');
                        return `<span class="text-sm font-medium ${timeClass}">${timeDisplay}</span>`;
                    })()}
                </td>
                <td class="px-2 py-3 text-right max-w-32">
                    <div class="flex items-center justify-end space-x-1 flex-wrap">
                        ${(match.home_yellow_cards || 0) > 0 ? `<div class="bg-yellow-400 text-black text-xs font-bold px-1.5 py-0.5 rounded flex items-center justify-center min-w-[20px]">${match.home_yellow_cards}</div>` : ''}
                        ${(match.home_position || 0) > 0 ? `<span class="text-xs text-purple-400 whitespace-nowrap">[${match.home_position}]</span>` : ''}
                        <span class="text-xs text-gray-100 text-right break-words">${match.home_team || '-'}</span>
                    </div>
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-center relative w-16">
                    ${(() => {
                        // Check if match is upcoming (not live, status = 0)
                        const isUpcoming = !match.is_live && (match.status === 0 || match.status === '0');
                        
                        if (isUpcoming) {
                            // No modal for upcoming matches
                            return `<span class="text-sm font-bold text-gray-100">${match.score || '-'}</span>`;
                        } else {
                            // Modal for live matches
                            const matchEvents = match.match_events || [];
                            const homeStats = match.home_stats || {};
                            const awayStats = match.away_stats || {};
                            const matchEventsAttr = JSON.stringify(matchEvents).replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/&/g, '&amp;');
                            const homeStatsAttr = JSON.stringify(homeStats).replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/&/g, '&amp;');
                            const awayStatsAttr = JSON.stringify(awayStats).replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/&/g, '&amp;');
                            const homeTeamId = match.home_team_id || '';
                            const awayTeamId = match.away_team_id || '';
                            return `<span class="text-sm font-bold text-gray-100 cursor-pointer hover:text-blue-400 relative group" 
                                  data-match-id="${matchId}" 
                                  data-home-team-id="${homeTeamId}"
                                  data-away-team-id="${awayTeamId}"
                                  data-match-events="${matchEventsAttr}"
                                  data-home-stats="${homeStatsAttr}"
                                  data-away-stats="${awayStatsAttr}"
                                  data-score-trigger>
                                ${match.score}
                                <div class="fixed left-0 top-0 w-96 bg-white border border-gray-300 rounded-lg shadow-xl z-[99999] hidden group-hover:block overflow-hidden" style="transform: translate(calc(var(--match-modal-x, 0px)), calc(var(--match-modal-y, 0px)));" data-match-modal>
                                    <div class="p-4 max-h-[600px] overflow-y-auto overflow-x-hidden">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4 text-center">Dữ liệu trận đấu</h3>
                                        <div class="text-center text-gray-500 py-4" data-match-modal-loading style="display: none;">
                                            Đang tải...
                                        </div>
                                        <div data-match-modal-content class="overflow-x-hidden">
                                            <div class="mb-4">
                                                <h4 class="text-sm font-bold text-gray-300 mb-2">Match Events</h4>
                                                <div class="border border-slate-600 rounded overflow-hidden">
                                                    <table class="w-full text-xs" style="table-layout: fixed; width: 100%;">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-2 py-2 text-left" style="width: 35%; max-width: 35%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <div class="flex items-start space-x-1" style="flex-wrap: wrap;">
                                                                        ${match.home_team_info?.img ? `<img src="${match.home_team_info.img}" alt="${match.home_team}" class="w-4 h-4 flex-shrink-0 mt-0.5" data-home-team-img>` : ''}
                                                                        <span class="break-words leading-tight" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; display: inline-block; max-width: 100%;" data-home-team-name>${match.home_team || '-'}</span>
                                                                    </div>
                                                                </th>
                                                                <th class="px-2 py-2 text-center" style="width: 30%; max-width: 30%;">Phút</th>
                                                                <th class="px-2 py-2 text-right" style="width: 35%; max-width: 35%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <div class="flex items-start justify-end space-x-1" style="flex-wrap: wrap;">
                                                                        <span class="break-words leading-tight text-right" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; display: inline-block; max-width: 100%;" data-away-team-name>${match.away_team || '-'}</span>
                                                                        ${match.away_team_info?.img ? `<img src="${match.away_team_info.img}" alt="${match.away_team}" class="w-4 h-4 flex-shrink-0 mt-0.5" data-away-team-img>` : ''}
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200" data-match-events-body>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-700 mb-2">Thống kê trận đấu</h4>
                                                <div class="border border-slate-600 rounded overflow-hidden">
                                                    <table class="w-full text-xs" style="table-layout: fixed; width: 100%;">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-2 py-2 text-left" style="width: 35%; max-width: 35%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <span class="break-words leading-tight" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; display: inline-block; max-width: 100%;" data-home-team-name-stat>${match.home_team || '-'}</span>
                                                                </th>
                                                                <th class="px-2 py-2 text-center" style="width: 30%; max-width: 30%;">Chỉ số</th>
                                                                <th class="px-2 py-2 text-right" style="width: 35%; max-width: 35%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <span class="break-words leading-tight" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; display: inline-block; max-width: 100%;" data-away-team-name-stat>${match.away_team || '-'}</span>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200" data-match-stats-body>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </span>`;
                        }
                    })()}
                </td>
                <td class="px-2 py-3 text-left max-w-32">
                    <div class="flex items-center space-x-1 text-left flex-wrap">
                        <span class="text-xs text-gray-100 text-left break-words">${match.away_team || '-'}</span>
                        ${(match.away_position || 0) > 0 ? `<span class="text-xs text-purple-400 whitespace-nowrap">[${match.away_position}]</span>` : ''}
                        ${(match.away_red_cards || 0) > 0 ? `<div class="bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded flex items-center justify-center min-w-[20px] relative">
                            ${match.away_red_cards}
                            <svg class="w-2 h-2 absolute -top-0.5 -right-0.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>` : ''}
                        ${(match.away_yellow_cards || 0) > 0 ? `<div class="bg-yellow-400 text-black text-xs font-bold px-1.5 py-0.5 rounded flex items-center justify-center min-w-[20px]">${match.away_yellow_cards}</div>` : ''}
                    </div>
                </td>
                ${cornersDisplay}
                <td class="px-2 py-3 whitespace-nowrap w-20">
                    <div class="flex items-center space-x-1">${statsIcons}</div>
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-left odds-cell w-32" data-match-id="${matchId}">
                    <div class="flex flex-col">
                        <span class="text-xs text-red-600 odds-1x2">${displayOdds1X2}</span>
                        <span class="text-xs text-blue-600 odds-over-under">${displayOddsOverUnder}</span>
                        <span class="text-xs text-yellow-600 odds-asian-handicap">${displayOddsAsianHandicap}</span>
                    </div>
                </td>
            </tr>
        `;
    }

    // Helper function to update tbody with matches
    function updateMatchesTbody(tbodyId, matches, selectedBookmaker = null) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        
        // Get current selected bookmaker if not provided
        if (!selectedBookmaker) {
            const selectedElement = document.getElementById('betting-provider-selected');
            selectedBookmaker = selectedElement ? selectedElement.textContent.trim() : null;
        }
        
        // Clear and rebuild
        tbody.innerHTML = '';
        
        matches.forEach((match, index) => {
            tbody.insertAdjacentHTML('beforeend', buildMatchRow(match, index, selectedBookmaker));
        });
        
        // Re-apply tournament filter after updating matches
        if (typeof filterMatchesByTournaments === 'function') {
            filterMatchesByTournaments();
        }
    }

    // Update odds when bookmaker changes
    function updateOddsForBookmaker(bookmakerName) {
        if (!bookmakerName) {
            console.warn('No bookmaker name provided');
            return;
        }

        // Reset all logging flags for new bookmaker search
        Object.keys(findBookmakerInOdds).forEach(key => {
            if (key.startsWith('_logged_')) {
                delete findBookmakerInOdds[key];
            }
        });

        // Update all odds cells in the document
        const allOddsCells = document.querySelectorAll('.odds-cell');
        let updatedCount = 0;
        let errorCount = 0;

        allOddsCells.forEach((cell) => {
            const matchId = cell.getAttribute('data-match-id');
            const row = cell.closest('tr');
            const oddsDataAttr = row ? row.getAttribute('data-odds-data') : null;
            
            if (!oddsDataAttr) {
                return; // Skip if no odds data
            }
            
            try {
                // Decode HTML entities and parse JSON
                let decoded = oddsDataAttr
                    .replace(/&amp;/g, '&')
                    .replace(/&quot;/g, '"')
                    .replace(/&#39;/g, "'");
                
                // Try to parse as JSON if it's a string
                let oddsData;
                try {
                    oddsData = JSON.parse(decoded);
                } catch (e) {
                    // If parsing fails, try to use decoded string directly
                    oddsData = decoded;
                }
                
                const odds = getOddsForBookmaker(oddsData, bookmakerName);
                
                const odds1X2Display = cell.querySelector('.odds-1x2');
                const oddsOUDisplay = cell.querySelector('.odds-over-under');
                const oddsAHDisplay = cell.querySelector('.odds-asian-handicap');
                
                if (odds1X2Display) {
                    odds1X2Display.textContent = odds['1X2'] || '- / - / -';
                    updatedCount++;
                }
                if (oddsOUDisplay) {
                    oddsOUDisplay.textContent = odds['Over/Under'] || '- / - / -';
                }
                if (oddsAHDisplay) {
                    oddsAHDisplay.textContent = odds['Asian Handicap'] || '- / - / -';
                }
            } catch (e) {
                errorCount++;
                console.error('Error updating odds for bookmaker:', e, { 
                    matchId, 
                    bookmakerName
                });
            }
        });

        if (updatedCount > 0) {
            console.log(`Updated odds for ${updatedCount} matches with bookmaker: ${bookmakerName}`);
        }
        if (errorCount > 0) {
            console.warn(`Failed to update odds for ${errorCount} matches`);
        }
    }

    // Listen for bookmaker changes
    document.addEventListener('bookmakerChanged', function(e) {
        const bookmakerName = e.detail.bookmaker;
        // updateOddsForBookmaker will handle all odds cells in the document
        updateOddsForBookmaker(bookmakerName);
    });

    // Auto-refresh table every 10 seconds
    let refreshInterval;
    
    function refreshMatchesTable() {
                    // Get current selected bookmaker
                    const selectedElement = document.getElementById('betting-provider-selected');
                    const selectedBookmaker = selectedElement ? selectedElement.textContent.trim() : null;
                    
        // Fetch all matches (live + upcoming) from single API
        // No cache - always get fresh data from server
        const cacheBuster = new Date().getTime();
        fetch(`{{ route("api.all.matches.table") }}?t=${cacheBuster}&_=${Math.random()}`, {
            method: 'GET',
            cache: 'no-store', // Don't store in cache at all
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(allMatchesData => {
                // Process all matches data
                if (allMatchesData.success && allMatchesData.data) {
                    // Update bookmakers dropdown if new bookmakers are available
                    if (allMatchesData.data.bookmakers && Array.isArray(allMatchesData.data.bookmakers) && allMatchesData.data.bookmakers.length > 0) {
                        updateBookmakersDropdown(allMatchesData.data.bookmakers, selectedBookmaker);
                    }
                    
                    // Update live matches
                    if (allMatchesData.data.live && Array.isArray(allMatchesData.data.live)) {
                        updateMatchesTbody('live-matches-tbody', allMatchesData.data.live, selectedBookmaker);
                    }
                    
                    // Process upcoming matches from all-matches-table API
                    let upcomingMatches = allMatchesData.data.upcoming || [];
                    
                    // Filter upcoming matches: only show matches with starting_datetime >= current time
                    const now = new Date();
                    upcomingMatches = upcomingMatches.filter(match => {
                        const startingDatetime = match.starting_datetime;
                        if (!startingDatetime) {
                            return false; // Exclude matches without datetime
                        }
                        try {
                            const matchDateTime = new Date(startingDatetime);
                            return matchDateTime >= now;
                        } catch (e) {
                            return false; // Exclude matches with invalid datetime
                        }
                    });
                    
                    // Update upcoming matches section
                    const upcomingTbody = document.getElementById('upcoming-matches-tbody');
                    const upcomingHeader = document.querySelector('thead tr td[colspan="9"]');
                    
                    // Show/hide upcoming section based on data
                    if (upcomingMatches.length > 0) {
                        if (!upcomingTbody) {
                            // Create header if doesn't exist
                            const table = document.querySelector('table');
                            if (table) {
                                const headerRow = document.createElement('thead');
                                headerRow.innerHTML = `
                                    <tr>
                                        <td colspan="9" class="px-4 py-3 bg-green-600">
                                            <h3 class="text-sm font-bold text-white uppercase">NHỮNG TRẬN SẮP BẮT ĐẦU</h3>
                                        </td>
                                    </tr>
                                `;
                                // Insert after live matches tbody
                                const liveTbody = document.getElementById('live-matches-tbody');
                                if (liveTbody && liveTbody.nextSibling) {
                                    table.insertBefore(headerRow, liveTbody.nextSibling);
                                }
                            }
                            
                            // Create tbody if doesn't exist
                            const newTbody = document.createElement('tbody');
                            newTbody.id = 'upcoming-matches-tbody';
                            newTbody.className = 'bg-white divide-y divide-gray-200';
                            table.appendChild(newTbody);
                        }
                        
                        // Show tbody and header if they exist
                        if (upcomingTbody) upcomingTbody.style.display = '';
                        if (upcomingHeader) upcomingHeader.style.display = '';
                        
                        // Matches are already sorted by starting_datetime from API
                        updateMatchesTbody('upcoming-matches-tbody', upcomingMatches, selectedBookmaker);
                    } else {
                        // Hide upcoming section if no data
                        if (upcomingTbody) upcomingTbody.style.display = 'none';
                        if (upcomingHeader) upcomingHeader.style.display = 'none';
                    }
                    
                    // Update tournament modal list with new leagues (live matches only)
                    updateTournamentModalList(allMatchesData.data.live || []);
                    
                    // Ensure all odds are updated with the selected bookmaker after refresh
                    if (selectedBookmaker) {
                        // Small delay to ensure DOM is updated
                        setTimeout(() => {
                            updateOddsForBookmaker(selectedBookmaker);
                        }, 100);
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing matches:', error);
            });
    }
    
    // Update tournament modal list with current leagues (live matches only)
    function updateTournamentModalList(liveMatches) {
        // Collect all unique leagues from live matches only
        const allLeaguesMap = new Map();
        
        // Process live matches only
        liveMatches.forEach(match => {
            const leagueName = match.league || '';
            const countryName = match.country_name || '';
            const leagueId = match.league_id || null;
            
            if (leagueName) {
                const leagueKey = leagueId ? String(leagueId) : (leagueName + '_' + (countryName || ''));
                if (!allLeaguesMap.has(leagueKey)) {
                    const displayName = countryName ? (countryName + ' - ' + leagueName) : leagueName;
                    allLeaguesMap.set(leagueKey, {
                        name: leagueName,
                        display_name: displayName,
                        country: countryName,
                        league_id: leagueId,
                    });
                }
            }
        });
        
        // Sort leagues
        const sortedLeagues = Array.from(allLeaguesMap.values()).sort((a, b) => {
            return a.display_name.localeCompare(b.display_name);
        });
        
        // Update modal content
        const tournamentList = document.getElementById('tournament-list');
        if (tournamentList && sortedLeagues.length > 0) {
            // Get currently selected leagues
            const currentSelected = Array.from(document.querySelectorAll('.tournament-checkbox:checked'))
                .map(cb => cb.value);
            
            // Rebuild tournament list
            tournamentList.innerHTML = '';
            sortedLeagues.forEach(league => {
                const isChecked = currentSelected.includes(league.name);
                const label = document.createElement('label');
                label.className = 'flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors';
                label.innerHTML = `
                    <input type="checkbox" 
                           name="tournament[]" 
                           value="${league.name.replace(/"/g, '&quot;')}" 
                           data-league-key="${league.league_id || (league.name + '_' + (league.country || ''))}"
                           class="tournament-checkbox w-4 h-4 text-[#1a5f2f] border-gray-300 rounded focus:ring-[#1a5f2f]" 
                           ${isChecked ? 'checked' : ''}>
                    <span class="text-sm text-gray-700">${league.display_name.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</span>
                `;
                tournamentList.appendChild(label);
            });
            
            // Re-attach event listeners to new checkboxes
            const newCheckboxes = tournamentList.querySelectorAll('.tournament-checkbox');
            newCheckboxes.forEach(checkbox => {
                // Add change listener
                checkbox.addEventListener('change', function() {
                    filterMatchesByTournaments();
                });
            });
        }
    }
    
    // Initial load: refresh immediately on page load
    refreshMatchesTable();
    
    // Auto-refresh: refresh every 1 minute (60 seconds) - even when tab is hidden
    refreshInterval = setInterval(refreshMatchesTable, 60000); // 60000ms = 60 seconds (1 minute)

    // Match Details Modal Logic
    const matchDetailsCache = new Map();
    const processedMatchModals = new WeakSet();
    
    function setupMatchDetailsModals() {
        document.querySelectorAll('[data-score-trigger]').forEach(trigger => {
            if (processedMatchModals.has(trigger)) return;
            
            const modal = trigger.querySelector('[data-match-modal]');
            if (!modal) return;
            
            processedMatchModals.add(trigger);
            
            let isLoading = false;
            let hasLoaded = false;
            
            trigger.addEventListener('mouseenter', function(e) {
                const matchId = this.getAttribute('data-match-id');
                if (!matchId) return;
                
                // Show modal
                modal.classList.remove('hidden');
                
                // Position modal
                positionMatchModal(modal, trigger);
                
                // Try to get data from data attributes first (pre-loaded, like corner data)
                const matchEventsAttr = this.getAttribute('data-match-events');
                const homeStatsAttr = this.getAttribute('data-home-stats');
                const awayStatsAttr = this.getAttribute('data-away-stats');
                const homeTeamId = this.getAttribute('data-home-team-id');
                const awayTeamId = this.getAttribute('data-away-team-id');
                
                if (matchEventsAttr || homeStatsAttr || awayStatsAttr) {
                    // Data is pre-loaded, use it immediately
                    try {
                        const matchEvents = matchEventsAttr ? JSON.parse(matchEventsAttr.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&')) : [];
                        const homeStats = homeStatsAttr ? JSON.parse(homeStatsAttr.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&')) : {};
                        const awayStats = awayStatsAttr ? JSON.parse(awayStatsAttr.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&')) : {};

                        
                        // Get team info from modal structure
                        const homeTeamName = modal.querySelector('[data-home-team-name]')?.textContent || '';
                        const awayTeamName = modal.querySelector('[data-away-team-name]')?.textContent || '';
                        const homeTeamImg = modal.querySelector('[data-home-team-img]')?.src || null;
                        const awayTeamImg = modal.querySelector('[data-away-team-img]')?.src || null;
                        
                        const matchData = {
                            events: matchEvents,
                            home_stats: homeStats,
                            away_stats: awayStats,
                            home_team: {
                                id: homeTeamId ? parseInt(homeTeamId) : null,
                                name: homeTeamName,
                                img: homeTeamImg,
                            },
                            away_team: {
                                id: awayTeamId ? parseInt(awayTeamId) : null,
                                name: awayTeamName,
                                img: awayTeamImg,
                            },
                        };
                        
                        // Cache it
                        matchDetailsCache.set(matchId, matchData);
                        
                        // Populate modal immediately
                        populateMatchModal(modal, matchData);
                        return;
                    } catch (e) {
                        console.error('Error parsing pre-loaded match data:', e);
                    }
                }
                
                // Fallback: Load data from API if not pre-loaded
                if (!matchDetailsCache.has(matchId) && !isLoading && !hasLoaded) {
                    isLoading = true;
                    const loadingDiv = modal.querySelector('[data-match-modal-loading]');
                    const contentDiv = modal.querySelector('[data-match-modal-content]');
                    
                    if (loadingDiv) loadingDiv.classList.remove('hidden');
                    if (contentDiv) contentDiv.classList.add('hidden');
                    
                    fetch(`{{ route('api.match.details') }}?match_id=${matchId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data) {
                                matchDetailsCache.set(matchId, data.data);
                                populateMatchModal(modal, data.data);
                                hasLoaded = true;
                            } else {
                                if (loadingDiv) {
                                    loadingDiv.textContent = 'Không có dữ liệu';
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching match details:', error);
                            const loadingDiv = modal.querySelector('[data-match-modal-loading]');
                            if (loadingDiv) {
                                loadingDiv.textContent = 'Lỗi khi tải dữ liệu';
                            }
                        })
                        .finally(() => {
                            isLoading = false;
                        });
                } else if (matchDetailsCache.has(matchId)) {
                    // Use cached data
                    populateMatchModal(modal, matchDetailsCache.get(matchId));
                }
            });
            
            let hideTimeout = null;
            
            // Keep modal visible when mouse enters modal
            modal.addEventListener('mouseenter', function() {
                // Clear any pending hide timeout
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                    hideTimeout = null;
                }
                // Ensure modal is visible
                modal.classList.remove('hidden');
            });
            
            trigger.addEventListener('mouseleave', function(e) {
                // Check if mouse is moving to modal
                const relatedTarget = e.relatedTarget;
                if (modal.contains(relatedTarget) || relatedTarget === modal) {
                    // Mouse is moving to modal, keep it visible
                    return;
                }
                
                // Add small delay before hiding to allow smooth transition
                hideTimeout = setTimeout(() => {
                    // Double check mouse is not over trigger or modal
                    const activeElement = document.elementFromPoint(e.clientX, e.clientY);
                    if (!trigger.contains(activeElement) && !modal.contains(activeElement) && activeElement !== modal) {
                        modal.classList.add('hidden');
                    }
                }, 100);
            });
            
            modal.addEventListener('mouseleave', function(e) {
                // Check if mouse is moving to trigger
                const relatedTarget = e.relatedTarget;
                if (trigger.contains(relatedTarget) || relatedTarget === trigger) {
                    // Mouse is moving to trigger, keep modal visible
                    return;
                }
                
                // Add small delay before hiding to allow smooth transition
                hideTimeout = setTimeout(() => {
                    // Double check mouse is not over trigger or modal
                    const activeElement = document.elementFromPoint(e.clientX, e.clientY);
                    if (!trigger.contains(activeElement) && !modal.contains(activeElement) && activeElement !== modal) {
                        modal.classList.add('hidden');
                    }
                }, 100);
            });
        });
    }
    
    function positionMatchModal(modal, trigger) {
        const rect = trigger.getBoundingClientRect();
        const modalRect = modal.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const margin = 10;
        
        let left = rect.right + 10;
        let top = rect.top;
        
        // Adjust if modal goes off right edge
        if (left + modalRect.width > viewportWidth - margin) {
            left = rect.left - modalRect.width - 10;
        }
        
        // Adjust if modal goes off left edge
        if (left < margin) {
            left = margin;
        }
        
        // Adjust if modal goes off bottom edge
        if (top + modalRect.height > viewportHeight - margin) {
            top = viewportHeight - modalRect.height - margin;
        }
        
        // Adjust if modal goes off top edge
        if (top < margin) {
            top = margin;
        }
        
        modal.style.setProperty('--match-modal-x', `${left}px`);
        modal.style.setProperty('--match-modal-y', `${top}px`);
    }
    
    function populateMatchModal(modal, data) {
        const loadingDiv = modal.querySelector('[data-match-modal-loading]');
        const contentDiv = modal.querySelector('[data-match-modal-content]');
        const eventsBody = modal.querySelector('[data-match-events-body]');
        const statsBody = modal.querySelector('[data-match-stats-body]');
        
        // Icon paths
        const goalIconPath = '{{ asset("assets/images/stast/goal-modal.gif") }}';
        
        if (loadingDiv) loadingDiv.classList.add('hidden');
        if (contentDiv) contentDiv.classList.remove('hidden');
        
        // Update team names and logos
        const homeTeamName = modal.querySelector('[data-home-team-name]');
        const awayTeamName = modal.querySelector('[data-away-team-name]');
        const homeTeamImg = modal.querySelector('[data-home-team-img]');
        const awayTeamImg = modal.querySelector('[data-away-team-img]');
        const homeTeamNameStat = modal.querySelector('[data-home-team-name-stat]');
        const awayTeamNameStat = modal.querySelector('[data-away-team-name-stat]');
        
        if (homeTeamName) homeTeamName.textContent = data.home_team.name;
        if (awayTeamName) awayTeamName.textContent = data.away_team.name;
        if (homeTeamNameStat) homeTeamNameStat.textContent = data.home_team.name;
        if (awayTeamNameStat) awayTeamNameStat.textContent = data.away_team.name;
        
        if (homeTeamImg && data.home_team.img) {
            homeTeamImg.src = data.home_team.img;
            homeTeamImg.style.display = 'block';
        } else if (homeTeamImg) {
            homeTeamImg.style.display = 'none';
        }
        
        if (awayTeamImg && data.away_team.img) {
            awayTeamImg.src = data.away_team.img;
            awayTeamImg.style.display = 'block';
        } else if (awayTeamImg) {
            awayTeamImg.style.display = 'none';
        }
        
        // Populate events
        if (eventsBody) {
            eventsBody.innerHTML = '';
            const events = data.events || [];
            
            if (events.length === 0) {
                eventsBody.innerHTML = '<tr><td colspan="3" class="px-3 py-2 text-center text-gray-500">Không có sự kiện</td></tr>';
            } else {
                events.forEach(event => {
                    const row = document.createElement('tr');
                    const homeTeamId = data.home_team.id;
                    const awayTeamId = data.away_team.id;
                    const eventTeamId = event.team_id;
                    
                    // Convert to numbers for comparison
                    const homeTeamIdNum = homeTeamId ? parseInt(homeTeamId) : null;
                    const awayTeamIdNum = awayTeamId ? parseInt(awayTeamId) : null;
                    const eventTeamIdNum = eventTeamId ? parseInt(eventTeamId) : null;
                    
                    const isHomeEvent = eventTeamIdNum !== null && eventTeamIdNum == homeTeamIdNum;
                    const isAwayEvent = eventTeamIdNum !== null && eventTeamIdNum == awayTeamIdNum;
                    
                    let eventIcon = '';
                    let eventText = '';
                    
                    if (event.type === 'goal') {
                        eventIcon = `<img src="${goalIconPath}" alt="Goal" class="w-4 h-4" onerror="this.style.display='none'">`;
                        eventText = event.player_name || 'Goal';
                        if (event.related_player_name) {
                            eventText += ` (Assist: ${event.related_player_name})`;
                        }
                    } else if (event.type === 'yellowcard' || event.type === 'yellowredcard') {
                        eventIcon = '<div class="w-4 h-4 bg-yellow-400 rounded"></div>';
                        eventText = event.player_name || 'Yellow Card';
                    } else if (event.type === 'redcard') {
                        eventIcon = '<div class="w-4 h-4 bg-red-600 rounded"></div>';
                        eventText = event.player_name || 'Red Card';
                    }
                    
                    const minute = event.minute || '';
                    const extraMinute = event.extra_minute ? `+${event.extra_minute}` : '';
                    const minuteDisplay = minute + extraMinute;
                    
                    if (isHomeEvent) {
                        row.innerHTML = `
                            <td class="px-3 py-2">
                                <div class="flex items-center space-x-2">
                                    ${eventIcon}
                                    <span class="text-gray-700">${eventText}</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center">${minuteDisplay}</td>
                            <td class="px-3 py-2"></td>
                        `;
                    } else if (isAwayEvent) {
                        row.innerHTML = `
                            <td class="px-3 py-2"></td>
                            <td class="px-3 py-2 text-center">${minuteDisplay}</td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <span class="text-gray-700">${eventText}</span>
                                    ${eventIcon}
                                </div>
                            </td>
                        `;
                    }
                    
                    eventsBody.appendChild(row);
                });
            }
        }
        
        // Populate statistics
        if (statsBody) {
            statsBody.innerHTML = '';
            const homeStats = data.home_stats || {};
            const awayStats = data.away_stats || {};
            
            const statsToShow = [
                { key: 'yellowcards', label: 'Thẻ vàng' },
                { key: 'shots_total', label: 'Tổng cú sút' },
                { key: 'shots_on_target', label: 'Sút trúng cầu môn' },
                { key: 'possessionpercent', label: 'Kiểm soát bóng (%)' },
                { key: 'fouls', label: 'Phạm lỗi' },
                { key: 'dangerous_attacks', label: 'Tấn công nguy hiểm' },
            ];
            
            statsToShow.forEach(stat => {
                const row = document.createElement('tr');
                const homeValue = homeStats[stat.key] ?? '-';
                const awayValue = awayStats[stat.key] ?? '-';
                
                row.innerHTML = `
                    <td class="px-3 py-2 text-left">${homeValue}</td>
                    <td class="px-3 py-2 text-center text-gray-600">${stat.label}</td>
                    <td class="px-3 py-2 text-right">${awayValue}</td>
                `;
                
                statsBody.appendChild(row);
            });
        }
    }
    
    // Setup match details modals on page load
    setupMatchDetailsModals();
    
    // Re-setup after dynamic content updates
    const matchModalObserver = new MutationObserver(function() {
        setupMatchDetailsModals();
    });
    
    if (document.body) {
        matchModalObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
});
</script>
