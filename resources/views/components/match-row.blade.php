@php
    $matchId = $match['match_id'] ?? null;
    $homeTeam = $match['home_team'] ?? 'N/A';
    $awayTeam = $match['away_team'] ?? 'N/A';
    $homeLogo = $match['home_team_info']['img'] ?? null;
    $awayLogo = $match['away_team_info']['img'] ?? null;
    
    // Get time display - prioritize time field, fallback to status_name or status
    $timeDisplay = $match['time'] ?? 
                  $match['status_name'] ?? 
                  ($match['status']['name'] ?? null) ?? 
                  ($match['status'] ?? '');
    
    // Check if match is live
    $isLive = $match['is_live'] ?? 
              ($match['status_name'] === 'LIVE' || $match['status_name'] === 'Inplay') ||
              ($match['status']['name'] ?? null) === 'LIVE' ||
              ($match['status'] ?? null) === 1 ||
              false;
    
    // Check if should blink (live match with minute, not HT, not FT)
    $shouldBlink = false;
    if ($isLive) {
        // Check if time contains a minute (e.g., "45'", "90+1'") and not "HT" or "FT"
        if (preg_match('/\d+\'/', $timeDisplay) && $timeDisplay !== 'HT' && $timeDisplay !== 'FT') {
            $shouldBlink = true;
        }
    }
    
    $dateDisplay = $match['date'] ?? '';
    
    $score = $match['score'] ?? '-';
    $htScore = $match['half_time'] ?? ($match['scores']['ht_score'] ?? '-');
    
    // Parse odds data from transformMatchToTableFormat structure
    // odds_data structure: ['1X2' => ['Bet365' => [...]], 'Asian Handicap' => [...], 'Over/Under' => [...]]
    $oddsData = $match['odds_data'] ?? [];
    $handicap = null;
    $overUnder = null;
    $odds1X2 = null;
    
    // Try to get Bet365 odds, fallback to first available
    $bet365Name = 'Bet365';
    
    // Get 1X2 odds
    if (isset($oddsData['1X2'])) {
        if (isset($oddsData['1X2'][$bet365Name])) {
            $odds1X2 = $oddsData['1X2'][$bet365Name];
        } elseif (!empty($oddsData['1X2'])) {
            $odds1X2 = reset($oddsData['1X2']);
        }
    }
    
    // Get Asian Handicap
    if (isset($oddsData['Asian Handicap'])) {
        if (isset($oddsData['Asian Handicap'][$bet365Name])) {
            $handicap = $oddsData['Asian Handicap'][$bet365Name];
        } elseif (!empty($oddsData['Asian Handicap'])) {
            $handicap = reset($oddsData['Asian Handicap']);
        }
    }
    
    // Get Over/Under
    if (isset($oddsData['Over/Under'])) {
        if (isset($oddsData['Over/Under'][$bet365Name])) {
            $overUnder = $oddsData['Over/Under'][$bet365Name];
        } elseif (!empty($oddsData['Over/Under'])) {
            $overUnder = reset($oddsData['Over/Under']);
        }
    }
    
    // Fallback to direct odds fields if odds_data structure is different
    if (!$handicap && isset($match['odds_asian_handicap']) && isset($match['odds_asian_handicap_value'])) {
        $handicap = [
            'handicap' => $match['odds_asian_handicap_value'],
            'home' => $match['odds_asian_handicap']['home'] ?? '-',
            'away' => $match['odds_asian_handicap']['away'] ?? '-',
        ];
    }
    
    if (!$overUnder && isset($match['odds_over_under']) && isset($match['odds_over_under_handicap'])) {
        $overUnder = [
            'handicap' => $match['odds_over_under_handicap'],
            'over' => $match['odds_over_under']['over'] ?? '-',
            'under' => $match['odds_over_under']['under'] ?? '-',
        ];
    }
    
    if (!$odds1X2 && isset($match['odds_1x2'])) {
        $odds1X2 = $match['odds_1x2'];
    }
@endphp

<!-- Mobile Card Layout -->
<div class="md:hidden border-b border-slate-700 hover:bg-slate-800 transition-colors p-3 cursor-pointer" 
     onclick="openMatchModal({{ $matchId }})"
     data-match-id="{{ $matchId }}">
    <div class="flex items-center justify-between mb-2">
        <div class="text-xs {{ $shouldBlink ? 'live-minute-blink' : ($isLive ? 'text-red-500' : 'text-gray-400') }} font-medium" data-time>{{ $timeDisplay }}</div>
        <div class="text-gray-400 text-sm" data-score>{{ $score }}</div>
    </div>
    
    <!-- Teams -->
    <div class="space-y-2 mb-3">
        <div class="flex items-center gap-2">
            @if($homeLogo)
                <img src="{{ $homeLogo }}" alt="{{ $homeTeam }}" class="w-5 h-5 object-contain flex-shrink-0" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" style="display: none;">{{ substr($homeTeam, 0, 1) }}</div>
            @else
                <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">{{ substr($homeTeam, 0, 1) }}</div>
            @endif
            <span class="text-white text-sm truncate min-w-0 flex-1">{{ $homeTeam }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if($awayLogo)
                <img src="{{ $awayLogo }}" alt="{{ $awayTeam }}" class="w-5 h-5 object-contain flex-shrink-0" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" style="display: none;">{{ substr($awayTeam, 0, 1) }}</div>
            @else
                <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">{{ substr($awayTeam, 0, 1) }}</div>
            @endif
            <span class="text-white text-sm truncate min-w-0 flex-1">{{ $awayTeam }}</span>
        </div>
    </div>
    
    <!-- Odds - Mobile -->
    <div class="grid grid-cols-3 gap-3 text-xs">
        <div>
                                    <div class="text-gray-400 mb-1">Hiệp 1</div>
                                    <div class="text-gray-500" data-ht-score>{{ $htScore }}</div>
        </div>
        <div>
            <div class="text-gray-400 mb-1">Cược chấp</div>
            @if($handicap && is_array($handicap))
                @php
                    $handicapValue = $handicap['handicap'] ?? '0';
                    $homeOdds = $handicap['home'] ?? '-';
                    $awayOdds = $handicap['away'] ?? '-';
                @endphp
                <div class="text-gray-300">{{ $handicapValue }}</div>
                <div class="text-green-400">{{ $homeOdds }} / {{ $awayOdds }}</div>
            @else
                <div class="text-gray-500">-</div>
            @endif
        </div>
        <div>
            <div class="text-gray-400 mb-1">Tài/Xỉu</div>
            @if($overUnder && is_array($overUnder))
                @php
                    $totalValue = $overUnder['handicap'] ?? '2.5';
                    $overOdds = $overUnder['over'] ?? '-';
                    $underOdds = $overUnder['under'] ?? '-';
                @endphp
                <div class="text-gray-300">{{ $totalValue }}</div>
                <div class="text-green-400">{{ $overOdds }} / {{ $underOdds }}</div>
            @else
                <div class="text-gray-500">-</div>
            @endif
        </div>
    </div>
    
    @if($odds1X2 && is_array($odds1X2))
        @php
            $homeWin = $odds1X2['home'] ?? '-';
            $draw = $odds1X2['draw'] ?? '-';
            $awayWin = $odds1X2['away'] ?? '-';
        @endphp
        <div class="mt-3 pt-3 border-t border-slate-700">
            <div class="text-gray-400 text-xs mb-2">1X2</div>
            <div class="flex gap-4 text-green-400 text-xs">
                <div>1: {{ $homeWin }}</div>
                <div>X: {{ $draw }}</div>
                <div>2: {{ $awayWin }}</div>
            </div>
        </div>
    @endif
</div>

<!-- Desktop Table Row -->
<div class="hidden md:grid md:grid-cols-11 gap-2 px-4 py-3 border-b border-slate-700 hover:bg-slate-800 transition-colors cursor-pointer" 
     onclick="openMatchModal({{ $matchId }})"
     data-match-id="{{ $matchId }}">
    <!-- Match Info -->
    <div class="col-span-3 flex items-center gap-2 min-w-0">
                                <div class="text-xs {{ $shouldBlink ? 'live-minute-blink' : ($isLive ? 'text-red-500' : 'text-gray-400') }} font-medium mb-2 hidden lg:block" data-time>{{ $timeDisplay }}</div>
        <div class="flex-1 min-w-0">
            <!-- Home Team -->
            <div class="flex items-center gap-2 mb-1 min-w-0">
                @if($homeLogo)
                    <img src="{{ $homeLogo }}" alt="{{ $homeTeam }}" class="w-5 h-5 object-contain flex-shrink-0" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" style="display: none;">{{ substr($homeTeam, 0, 1) }}</div>
                @else
                    <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">{{ substr($homeTeam, 0, 1) }}</div>
                @endif
                <span class="text-white text-sm truncate min-w-0">{{ $homeTeam }}</span>
            </div>
            <!-- Away Team -->
            <div class="flex items-center gap-2 min-w-0">
                @if($awayLogo)
                    <img src="{{ $awayLogo }}" alt="{{ $awayTeam }}" class="w-5 h-5 object-contain flex-shrink-0" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" style="display: none;">{{ substr($awayTeam, 0, 1) }}</div>
                @else
                    <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">{{ substr($awayTeam, 0, 1) }}</div>
                @endif
                <span class="text-white text-sm truncate min-w-0">{{ $awayTeam }}</span>
            </div>
        </div>
    </div>
    
    <!-- Score (FT) -->
    <div class="col-span-1 text-center text-gray-400 text-sm flex items-center justify-center" data-score>
        {{ $score }}
    </div>
    
                                <!-- Half 1 -->
                                <div class="col-span-1 text-center text-gray-400 text-sm flex items-center justify-center" data-ht-score>
                                    {{ $htScore }}
                                </div>
    
    <!-- Handicap Odds -->
    <div class="col-span-2 text-xs min-w-0">
        @if($handicap && is_array($handicap))
            @php
                $handicapValue = $handicap['handicap'] ?? '0';
                $homeOdds = $handicap['home'] ?? '-';
                $awayOdds = $handicap['away'] ?? '-';
            @endphp
            <div class="flex items-start justify-end gap-2">
                <!-- Cột 1: Handicap value -->
                <div class="flex items-start flex-shrink-0">
                    <span class="text-gray-300 whitespace-nowrap">{{ $handicapValue }}</span>
                </div>
                <!-- Cột 2: 2 dòng odds -->
                <div class="flex flex-col gap-1 items-start min-w-0">
                    <div class="text-green-400 truncate w-full">{{ $homeOdds }}</div>
                    <div class="text-green-400 truncate w-full">{{ $awayOdds }}</div>
                </div>
            </div>
        @else
            <div class="text-end text-gray-500">-</div>
        @endif
    </div>
    
    <!-- Over/Under Odds -->
    <div class="col-span-2 text-xs min-w-0">
        @if($overUnder && is_array($overUnder))
            @php
                $totalValue = $overUnder['handicap'] ?? '2.5';
                $overOdds = $overUnder['over'] ?? '-';
                $underOdds = $overUnder['under'] ?? '-';
            @endphp
            <div class="flex items-start justify-end gap-2">
                <!-- Cột 1: Handicap value -->
                <div class="flex items-end flex-shrink-0">
                    <span class="text-gray-300 whitespace-nowrap">{{ $totalValue }}</span>
                </div>
                <!-- Cột 2: 2 dòng odds -->
                <div class="flex flex-col gap-1 items-start min-w-0">
                    <div class="text-green-400 truncate w-full">{{ $overOdds }}</div>
                    <div class="text-green-400 truncate w-full">{{ $underOdds }}</div>
                </div>
            </div>
        @else
            <div class="text-end text-gray-500">-</div>
        @endif
    </div>
    
    <!-- 1X2 Odds -->
    <div class="col-span-2 text-xs min-w-0">
        @if($odds1X2 && is_array($odds1X2))
            @php
                $homeWin = $odds1X2['home'] ?? '-';
                $draw = $odds1X2['draw'] ?? '-';
                $awayWin = $odds1X2['away'] ?? '-';
            @endphp
            <div class="flex flex-col gap-1 items-end">
                <div class="text-green-400 truncate w-full text-right">{{ $homeWin }}</div>
                <div class="text-green-400 truncate w-full text-right">{{ $draw }}</div>
                <div class="text-green-400 truncate w-full text-right">{{ $awayWin }}</div>
            </div>
        @else
            <div class="text-end text-gray-500">-</div>
        @endif
    </div>
</div>

