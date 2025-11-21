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
<div class="md:hidden border-b border-slate-700/50 hover:bg-gradient-to-r hover:from-slate-800/80 hover:to-slate-900/80 transition-all duration-200 p-4 cursor-pointer group backdrop-blur-sm" 
     onclick="openMatchModal({{ $matchId }})"
     data-match-id="{{ $matchId }}">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            @if($isLive)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-500/20 text-red-400 text-xs font-bold animate-pulse">LIVE</span>
            @endif
            <div class="text-xs {{ $shouldBlink ? 'live-minute-blink' : ($isLive ? 'text-red-400 font-bold' : 'text-gray-400') }} font-semibold" data-time>{{ $timeDisplay }}</div>
        </div>
        <div class="text-white text-lg font-bold bg-gradient-to-r from-emerald-500 to-green-600 bg-clip-text text-transparent" data-score>{{ $score }}</div>
    </div>
    
    <!-- Teams -->
    <div class="space-y-3 mb-4">
        <div class="flex items-center gap-3 group/team">
            @if($homeLogo)
                <div class="w-8 h-8 rounded-lg bg-slate-800/50 border border-slate-700/50 p-1 flex items-center justify-center flex-shrink-0 group-hover/team:border-emerald-500/50 transition-colors">
                    <img src="{{ $homeLogo }}" alt="{{ $homeTeam }}" class="w-full h-full object-contain" loading="lazy" decoding="async" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-xs text-white font-bold\'>{{ substr($homeTeam, 0, 1) }}</div>';">
                </div>
            @else
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-xs text-white font-bold flex-shrink-0">{{ substr($homeTeam, 0, 1) }}</div>
            @endif
            <span class="text-white text-sm font-medium truncate min-w-0 flex-1 group-hover/team:text-emerald-400 transition-colors">{{ $homeTeam }}</span>
        </div>
        <div class="flex items-center gap-3 group/team">
            @if($awayLogo)
                <div class="w-8 h-8 rounded-lg bg-slate-800/50 border border-slate-700/50 p-1 flex items-center justify-center flex-shrink-0 group-hover/team:border-emerald-500/50 transition-colors">
                    <img src="{{ $awayLogo }}" alt="{{ $awayTeam }}" class="w-full h-full object-contain" loading="lazy" decoding="async" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-xs text-white font-bold\'>{{ substr($awayTeam, 0, 1) }}</div>';">
                </div>
            @else
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-xs text-white font-bold flex-shrink-0">{{ substr($awayTeam, 0, 1) }}</div>
            @endif
            <span class="text-white text-sm font-medium truncate min-w-0 flex-1 group-hover/team:text-emerald-400 transition-colors">{{ $awayTeam }}</span>
        </div>
    </div>
    
    <!-- Odds - Mobile -->
    <div class="grid grid-cols-3 gap-3 text-xs">
        <div class="bg-slate-800/50 rounded-lg p-2 border border-slate-700/50">
            <div class="text-gray-400 mb-1.5 text-[10px] font-semibold uppercase">Hiệp 1</div>
            <div class="text-emerald-400 font-bold" data-ht-score>{{ $htScore }}</div>
        </div>
        <div class="bg-slate-800/50 rounded-lg p-2 border border-slate-700/50">
            <div class="text-gray-400 mb-1.5 text-[10px] font-semibold uppercase">Cược chấp</div>
            @if($handicap && is_array($handicap))
                @php
                    $handicapValue = $handicap['handicap'] ?? '0';
                    $homeOdds = $handicap['home'] ?? '-';
                    $awayOdds = $handicap['away'] ?? '-';
                @endphp
                <div class="text-gray-300 font-semibold mb-1">{{ $handicapValue }}</div>
                <div class="text-emerald-400 font-bold text-[11px]">{{ $homeOdds }} / {{ $awayOdds }}</div>
            @else
                <div class="text-gray-500">-</div>
            @endif
        </div>
        <div class="bg-slate-800/50 rounded-lg p-2 border border-slate-700/50">
            <div class="text-gray-400 mb-1.5 text-[10px] font-semibold uppercase">Tài/Xỉu</div>
            @if($overUnder && is_array($overUnder))
                @php
                    $totalValue = $overUnder['handicap'] ?? '2.5';
                    $overOdds = $overUnder['over'] ?? '-';
                    $underOdds = $overUnder['under'] ?? '-';
                @endphp
                <div class="text-gray-300 font-semibold mb-1">{{ $totalValue }}</div>
                <div class="text-emerald-400 font-bold text-[11px]">{{ $overOdds }} / {{ $underOdds }}</div>
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
        <div class="mt-4 pt-4 border-t border-slate-700/50">
            <div class="text-gray-400 text-xs mb-2 font-semibold uppercase">1X2</div>
            <div class="flex gap-3">
                <div class="flex-1 bg-slate-800/50 rounded-lg p-2 border border-slate-700/50 text-center">
                    <div class="text-gray-400 text-[10px] mb-1">1</div>
                    <div class="text-emerald-400 font-bold text-sm">{{ $homeWin }}</div>
                </div>
                <div class="flex-1 bg-slate-800/50 rounded-lg p-2 border border-slate-700/50 text-center">
                    <div class="text-gray-400 text-[10px] mb-1">X</div>
                    <div class="text-emerald-400 font-bold text-sm">{{ $draw }}</div>
                </div>
                <div class="flex-1 bg-slate-800/50 rounded-lg p-2 border border-slate-700/50 text-center">
                    <div class="text-gray-400 text-[10px] mb-1">2</div>
                    <div class="text-emerald-400 font-bold text-sm">{{ $awayWin }}</div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Desktop Table Row -->
<div class="hidden md:grid md:grid-cols-11 gap-2 px-4 py-4 border-b border-slate-700/50 hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 cursor-pointer group backdrop-blur-sm" 
     onclick="openMatchModal({{ $matchId }})"
     data-match-id="{{ $matchId }}">
    <!-- Match Info -->
    <div class="col-span-3 flex items-center gap-3 min-w-0">
        <div class="text-xs {{ $shouldBlink ? 'live-minute-blink' : ($isLive ? 'text-red-400 font-bold' : 'text-gray-400') }} font-semibold mb-2 hidden lg:block" data-time>
            @if($isLive)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-red-500/20 text-red-400 text-[10px] font-bold mr-1">LIVE</span>
            @endif
            {{ $timeDisplay }}
        </div>
        <div class="flex-1 min-w-0">
            <!-- Home Team -->
            <div class="flex items-center gap-2 mb-2 min-w-0 group/team">
                @if($homeLogo)
                    <div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover/team:border-emerald-500/50 transition-colors">
                        <img src="{{ $homeLogo }}" alt="{{ $homeTeam }}" class="w-full h-full object-contain" loading="lazy" decoding="async" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\'>{{ substr($homeTeam, 0, 1) }}</div>';">
                    </div>
                @else
                    <div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">{{ substr($homeTeam, 0, 1) }}</div>
                @endif
                <span class="text-white text-sm font-medium truncate min-w-0 group-hover/team:text-emerald-400 transition-colors">{{ $homeTeam }}</span>
            </div>
            <!-- Away Team -->
            <div class="flex items-center gap-2 min-w-0 group/team">
                @if($awayLogo)
                    <div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover/team:border-emerald-500/50 transition-colors">
                        <img src="{{ $awayLogo }}" alt="{{ $awayTeam }}" class="w-full h-full object-contain" loading="lazy" decoding="async" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\'>{{ substr($awayTeam, 0, 1) }}</div>';">
                    </div>
                @else
                    <div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">{{ substr($awayTeam, 0, 1) }}</div>
                @endif
                <span class="text-white text-sm font-medium truncate min-w-0 group-hover/team:text-emerald-400 transition-colors">{{ $awayTeam }}</span>
            </div>
        </div>
    </div>
    
    <!-- Score (FT) -->
    <div class="col-span-1 text-center text-white text-base font-bold bg-gradient-to-r from-emerald-500 to-green-600 bg-clip-text text-transparent flex items-center justify-center" data-score>
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
                    <div class="text-emerald-400 font-bold truncate w-full">{{ $homeOdds }}</div>
                    <div class="text-emerald-400 font-bold truncate w-full">{{ $awayOdds }}</div>
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
                    <div class="text-emerald-400 font-bold truncate w-full">{{ $overOdds }}</div>
                    <div class="text-emerald-400 font-bold truncate w-full">{{ $underOdds }}</div>
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
                <div class="text-emerald-400 font-bold truncate w-full text-right">{{ $homeWin }}</div>
                <div class="text-emerald-400 font-bold truncate w-full text-right">{{ $draw }}</div>
                <div class="text-emerald-400 font-bold truncate w-full text-right">{{ $awayWin }}</div>
            </div>
        @else
            <div class="text-end text-gray-500">-</div>
        @endif
    </div>
</div>

