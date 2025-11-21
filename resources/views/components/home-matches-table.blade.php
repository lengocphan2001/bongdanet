@props([
    'liveMatches' => [],
    'upcomingMatches' => [],
])

<style>
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    .live-minute-blink {
        animation: blink 1s ease-in-out infinite;
        font-weight: 600;
        color: #ef4444 !important; /* red-500 - nhấp nháy màu đỏ */
    }
</style>

@php
    // Group matches by league
    $liveGrouped = [];
    foreach ($liveMatches as $match) {
        $leagueId = $match['league_id'] ?? null;
        $leagueName = $match['league'] ?? 'Unknown League';
        $countryName = $match['country_name'] ?? '';
        
        if (!$leagueId) {
            $leagueId = 'unknown_' . md5($leagueName);
        }
        
        if (!isset($liveGrouped[$leagueId])) {
            $liveGrouped[$leagueId] = [
                'league' => [
                    'id' => $leagueId,
                    'name' => $leagueName,
                    'country_name' => $countryName,
                ],
                'matches' => [],
            ];
        }
        
        $liveGrouped[$leagueId]['matches'][] = $match;
    }
    
    $upcomingGrouped = [];
    foreach ($upcomingMatches as $match) {
        $leagueId = $match['league_id'] ?? null;
        $leagueName = $match['league'] ?? 'Unknown League';
        $countryName = $match['country_name'] ?? '';
        
        if (!$leagueId) {
            $leagueId = 'unknown_' . md5($leagueName);
        }
        
        if (!isset($upcomingGrouped[$leagueId])) {
            $upcomingGrouped[$leagueId] = [
                'league' => [
                    'id' => $leagueId,
                    'name' => $leagueName,
                    'country_name' => $countryName,
                ],
                'matches' => [],
            ];
        }
        
        $upcomingGrouped[$leagueId]['matches'][] = $match;
    }
@endphp

<div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-1 h-8 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Lịch Thi Đấu Bóng Đá Hôm Nay</span>
        </h1>
    </div>
    
    <!-- Matches Section -->
    <div class="space-y-4 sm:space-y-6">
        {{-- Live Matches Section --}}
        @if(!empty($liveGrouped))
            @foreach($liveGrouped as $leagueId => $leagueGroup)
                @php
                    $league = $leagueGroup['league'];
                    $matches = $leagueGroup['matches'];
                    $countryName = $league['country_name'] ?? '';
                    $leagueName = $league['name'] ?? 'Unknown League';
                @endphp
                
                <div class="mb-6 sm:mb-8">
                    <div class="flex items-center gap-2 sm:gap-3 mb-2 p-1 sm:p-2 bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                        <button onclick="toggleLeagueTable('live-{{ $leagueId }}')" 
                                class="flex-shrink-0 p-1.5 sm:p-2 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 rounded-lg transition-all duration-200 group"
                                aria-label="Toggle table">
                            <svg id="toggle-icon-live-{{ $leagueId }}" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md bg-red-500/20 text-red-400 text-[10px] sm:text-xs font-semibold animate-pulse flex-shrink-0">LIVE</span>
                        <h2 class="flex text-sm sm:text-base md:text-lg font-bold text-white overflow-hidden text-ellipsis whitespace-nowrap flex-1 min-w-0">
                            <span class="inline-block truncate">
                                {{ $countryName ? $countryName . ': ' : '' }}{{ $leagueName }}
                                @if(count($matches) > 0)
                                    <span class="text-emerald-400 text-[10px] sm:text-xs md:text-sm font-normal ml-1 sm:ml-2">({{ count($matches) }})</span>
                                @endif
                            </span>
                        </h2>
                        @if($leagueId && $leagueId !== 'unknown' && is_numeric($leagueId))
                            <a href="{{ route('standings.show', $leagueId) }}" 
                               class="hidden sm:inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-[10px] sm:text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 hover:scale-105 flex-shrink-0">
                                <span>BXH</span>
                            </a>
                        @endif
                    </div>
                    
                    <div id="live-{{ $leagueId }}" class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
                        <!-- Desktop Table Header -->
                        <div class="hidden md:grid md:grid-cols-11 gap-2 bg-gradient-to-r from-slate-800/90 to-slate-700/90 px-4 py-3 text-xs font-bold text-gray-200 border-b border-slate-600/50 backdrop-blur-sm">
                            <div class="col-span-3">Trận đấu</div>
                            <div class="col-span-1 text-center">Tỷ số</div>
                            <div class="col-span-1 text-center">Hiệp 1</div>
                            <div class="col-span-2 text-end">Cược chấp</div>
                            <div class="col-span-2 text-end">Tài/Xỉu</div>
                            <div class="col-span-2 text-end">1X2</div>
                        </div>
                        
                        <!-- Matches -->
                        @foreach($matches as $match)
                            @include('components.match-row', ['match' => $match])
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
        
        {{-- Upcoming Matches Section --}}
        @if(!empty($upcomingGrouped))
            @foreach($upcomingGrouped as $leagueId => $leagueGroup)
                @php
                    $league = $leagueGroup['league'];
                    $matches = $leagueGroup['matches'];
                    $countryName = $league['country_name'] ?? '';
                    $leagueName = $league['name'] ?? 'Unknown League';
                @endphp
                
                <div class="mb-6 sm:mb-8">
                    <div class="flex items-center gap-2 sm:gap-3 mb-2 p-1 sm:p-2 bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                        <button onclick="toggleLeagueTable('upcoming-{{ $leagueId }}')" 
                                class="flex-shrink-0 p-1.5 sm:p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-500/10 rounded-lg transition-all duration-200 group"
                                aria-label="Toggle table">
                            <svg id="toggle-icon-upcoming-{{ $leagueId }}" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md bg-blue-500/20 text-blue-400 text-[10px] sm:text-xs font-semibold flex-shrink-0">SẮP BẮT ĐẦU</span>
                        <h2 class="flex text-sm sm:text-base md:text-lg font-bold text-white overflow-hidden text-ellipsis whitespace-nowrap flex-1 min-w-0">
                            <span class="inline-block truncate">
                                {{ $countryName ? $countryName . ': ' : '' }}{{ $leagueName }}
                                @if(count($matches) > 0)
                                    <span class="text-blue-400 text-[10px] sm:text-xs md:text-sm font-normal ml-1 sm:ml-2">({{ count($matches) }})</span>
                                @endif
                            </span>
                        </h2>
                        @if($leagueId && $leagueId !== 'unknown' && is_numeric($leagueId))
                            <div class="hidden sm:flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                                <a href="{{ route('schedule.league', $leagueId) }}" class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-[10px] sm:text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-105">
                                    <span>Lịch</span>
                                </a>
                                <a href="{{ route('results.league', $leagueId) }}" class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-[10px] sm:text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                    <span>KQ</span>
                                </a>
                                <a href="{{ route('standings.show', $leagueId) }}" class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-[10px] sm:text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 hover:scale-105">
                                    <span>BXH</span>
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <div id="upcoming-{{ $leagueId }}" class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
                        <!-- Desktop Table Header -->
                        <div class="hidden md:grid md:grid-cols-11 gap-2 bg-gradient-to-r from-slate-800/90 to-slate-700/90 px-4 py-3 text-xs font-bold text-gray-200 border-b border-slate-600/50 backdrop-blur-sm">
                            <div class="col-span-3">Trận đấu</div>
                            <div class="col-span-1 text-center">Tỷ số</div>
                            <div class="col-span-1 text-center">Hiệp 1</div>
                            <div class="col-span-2 text-end">Cược chấp</div>
                            <div class="col-span-2 text-end">Tài/Xỉu</div>
                            <div class="col-span-2 text-end">1X2</div>
                        </div>
                        
                        <!-- Matches -->
                        @foreach($matches as $match)
                            @include('components.match-row', ['match' => $match])
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
        
        @if(empty($liveGrouped) && empty($upcomingGrouped))
            <div class="text-center py-12 sm:py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-400 text-sm sm:text-base font-medium">Không có trận đấu nào hôm nay</p>
            </div>
        @endif
    </div>
</div>

{{-- Match Detail Modal --}}
<x-match-detail-modal />

<script>
function toggleLeagueTable(leagueId) {
    const table = document.getElementById(leagueId);
    const icon = document.getElementById('toggle-icon-' + leagueId);
    
    if (!table || !icon) return;
    
    if (table.classList.contains('hidden')) {
        table.classList.remove('hidden');
        icon.style.transform = 'rotate(0deg)';
    } else {
        table.classList.add('hidden');
        icon.style.transform = 'rotate(-90deg)';
    }
}

// Auto-refresh matches data every 1 minute - Update DOM directly without page reload
let refreshInterval = null;

// Helper function to build match row HTML (similar to match-list-table)
function buildMatchRowHTML(match, index) {
    const matchId = match.match_id || index;
    const homeTeam = match.home_team || 'N/A';
    const awayTeam = match.away_team || 'N/A';
    const homeLogo = match.home_team_info?.img || '';
    const awayLogo = match.away_team_info?.img || '';
    
    // Get time display
    const timeDisplay = match.time || match.status_name || match.status?.name || match.status || '-';
    
    // Check if match is live
    const isLive = match.is_live || 
                  match.status_name === 'LIVE' || 
                  match.status_name === 'Inplay' ||
                  match.status?.name === 'LIVE' ||
                  match.status === 1 ||
                  false;
    
    // Check if should blink
    const hasMinute = /\d+'/.test(timeDisplay) || /\d+/.test(timeDisplay);
    const isNotHT = timeDisplay !== 'HT' && timeDisplay !== 'HT\'';
    const isNotFT = timeDisplay !== 'FT' && timeDisplay !== 'FT\'';
    const shouldBlink = isLive && hasMinute && isNotHT && isNotFT;
    
    const timeClass = shouldBlink ? 'live-minute-blink' : (isLive ? 'text-red-500' : 'text-gray-400');
    
    // Parse score - prioritize ft_score, then home_score/away_score
    let score = '-';
    if (match.scores) {
        // Priority 1: ft_score (full time score)
        if (match.scores.ft_score && match.scores.ft_score !== '' && match.scores.ft_score !== null) {
            score = match.scores.ft_score;
        } 
        // Priority 2: home_score and away_score
        else if (match.scores.home_score !== undefined && match.scores.away_score !== undefined) {
            const home = match.scores.home_score === '' ? '0' : match.scores.home_score;
            const away = match.scores.away_score === '' ? '0' : match.scores.away_score;
            score = `${home} - ${away}`;
        } 
        // Priority 3: full_time (from transformMatchToTableFormat)
        else if (match.full_time) {
            score = match.full_time;
        }
        // Priority 4: score field
        else if (match.score) {
            score = match.score;
        }
    } else if (match.full_time) {
        score = match.full_time;
    } else if (match.score) {
        score = match.score;
    }
    
    // Parse half time score
    let htScore = '-';
    if (match.scores?.ht_score) {
        htScore = match.scores.ht_score;
    } else if (match.scores?.halftime) {
        const ht = match.scores.halftime;
        if (ht.home !== undefined && ht.away !== undefined) {
            htScore = `${ht.home} - ${ht.away}`;
        }
    } else if (match.half_time) {
        htScore = match.half_time;
    }
    
    // Parse odds data
    const oddsData = match.odds_data || {};
    const handicap = oddsData['Asian Handicap']?.[Object.keys(oddsData['Asian Handicap'] || {})[0]] || null;
    const overUnder = oddsData['Over/Under']?.[Object.keys(oddsData['Over/Under'] || {})[0]] || null;
    const odds1X2 = oddsData['1X2']?.[Object.keys(oddsData['1X2'] || {})[0]] || null;
    
    // Mobile card HTML
    const mobileHTML = `
        <div class="md:hidden border-b border-slate-700 hover:bg-slate-800 transition-colors p-3 cursor-pointer" 
             onclick="openMatchModal(${matchId})"
             data-match-id="${matchId}">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs ${timeClass} font-medium" data-time>${timeDisplay}</div>
                <div class="text-gray-400 text-sm" data-score>${score}</div>
            </div>
            <div class="space-y-2 mb-3">
                <div class="flex items-center gap-2">
                    ${homeLogo ? `<img src="${homeLogo}" alt="${homeTeam}" class="w-5 h-5 object-contain flex-shrink-0" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` : ''}
                    <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" ${homeLogo ? 'style="display: none;"' : ''}>${homeTeam.charAt(0)}</div>
                    <span class="text-white text-sm truncate min-w-0 flex-1">${homeTeam}</span>
                </div>
                <div class="flex items-center gap-2">
                    ${awayLogo ? `<img src="${awayLogo}" alt="${awayTeam}" class="w-5 h-5 object-contain flex-shrink-0" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` : ''}
                    <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" ${awayLogo ? 'style="display: none;"' : ''}>${awayTeam.charAt(0)}</div>
                    <span class="text-white text-sm truncate min-w-0 flex-1">${awayTeam}</span>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3 text-xs">
                <div>
                    <div class="text-gray-400 mb-1">Hiệp 1</div>
                    <div class="text-gray-500" data-ht-score>${htScore}</div>
                </div>
                <div>
                    <div class="text-gray-400 mb-1">Cược chấp</div>
                    ${handicap ? `<div class="text-gray-300">${handicap.handicap || '0'}</div><div class="text-green-400">${handicap.home || '-'} / ${handicap.away || '-'}</div>` : '<div class="text-gray-500">-</div>'}
                </div>
                <div>
                    <div class="text-gray-400 mb-1">Tài/Xỉu</div>
                    ${overUnder ? `<div class="text-gray-300">${overUnder.handicap || overUnder.total || '2.5'}</div><div class="text-green-400">${overUnder.over || '-'} / ${overUnder.under || '-'}</div>` : '<div class="text-gray-500">-</div>'}
                </div>
            </div>
        </div>
    `;
    
    // Desktop row HTML
    const desktopHTML = `
        <div class="hidden md:grid md:grid-cols-11 gap-2 px-4 py-3 border-b border-slate-700 hover:bg-slate-800 transition-colors cursor-pointer" 
             onclick="openMatchModal(${matchId})"
             data-match-id="${matchId}">
            <div class="col-span-3 flex items-center gap-2 min-w-0">
                <div class="text-xs ${timeClass} font-medium mb-2 hidden lg:block" data-time>${timeDisplay}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 min-w-0">
                        ${homeLogo ? `<img src="${homeLogo}" alt="${homeTeam}" class="w-5 h-5 object-contain flex-shrink-0" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` : ''}
                        <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" ${homeLogo ? 'style="display: none;"' : ''}>${homeTeam.charAt(0)}</div>
                        <span class="text-white text-sm truncate min-w-0">${homeTeam}</span>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        ${awayLogo ? `<img src="${awayLogo}" alt="${awayTeam}" class="w-5 h-5 object-contain flex-shrink-0" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` : ''}
                        <div class="w-5 h-5 bg-slate-700 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" ${awayLogo ? 'style="display: none;"' : ''}>${awayTeam.charAt(0)}</div>
                        <span class="text-white text-sm truncate min-w-0">${awayTeam}</span>
                    </div>
                </div>
            </div>
            <div class="col-span-1 text-center text-gray-400 text-sm flex items-center justify-center" data-score>${score}</div>
            <div class="col-span-1 text-center text-gray-400 text-sm flex items-center justify-center" data-ht-score>${htScore}</div>
            <div class="col-span-2 text-xs min-w-0">
                ${handicap ? `<div class="flex items-start justify-end gap-2"><div class="flex items-start flex-shrink-0"><span class="text-gray-300 whitespace-nowrap">${handicap.handicap || '0'}</span></div><div class="flex flex-col gap-1 items-start min-w-0"><div class="text-green-400 truncate w-full">${handicap.home || '-'}</div><div class="text-green-400 truncate w-full">${handicap.away || '-'}</div></div></div>` : '<div class="text-end text-gray-500">-</div>'}
            </div>
            <div class="col-span-2 text-xs min-w-0">
                ${overUnder ? `<div class="flex items-start justify-end gap-2"><div class="flex items-end flex-shrink-0"><span class="text-gray-300 whitespace-nowrap">${overUnder.handicap || overUnder.total || '2.5'}</span></div><div class="flex flex-col gap-1 items-start min-w-0"><div class="text-green-400 truncate w-full">${overUnder.over || '-'}</div><div class="text-green-400 truncate w-full">${overUnder.under || '-'}</div></div></div>` : '<div class="text-end text-gray-500">-</div>'}
            </div>
            <div class="col-span-2 text-xs min-w-0">
                ${odds1X2 ? `<div class="flex flex-col gap-1 items-end"><div class="text-green-400 truncate w-full text-right">${odds1X2.home || '-'}</div><div class="text-green-400 truncate w-full text-right">${odds1X2.draw || '-'}</div><div class="text-green-400 truncate w-full text-right">${odds1X2.away || '-'}</div></div>` : '<div class="text-end text-gray-500">-</div>'}
            </div>
        </div>
    `;
    
    return mobileHTML + desktopHTML;
}

// Update matches for a league - smart update (only update changed fields)
function updateLeagueMatches(leagueId, matches, isLive = true) {
    const container = document.getElementById((isLive ? 'live-' : 'upcoming-') + leagueId);
    if (!container) return;
    
    // Find all match rows (both mobile and desktop)
    const existingRows = container.querySelectorAll('[data-match-id]');
    const matchMap = new Map();
    
    // Group rows by match ID (mobile and desktop are separate)
    existingRows.forEach(row => {
        const matchId = row.getAttribute('data-match-id');
        if (matchId) {
            if (!matchMap.has(matchId)) {
                matchMap.set(matchId, []);
            }
            matchMap.get(matchId).push(row);
        }
    });
    
    // Update or add matches
    matches.forEach((match, index) => {
        const matchId = String(match.match_id || index);
        const existingRows = matchMap.get(matchId) || [];
        
        if (existingRows.length > 0) {
            // Update existing rows - only update time, score, and other dynamic fields
            existingRows.forEach(row => {
                // Update time display
                const timeEl = row.querySelector('[data-time]');
                if (timeEl) {
                    const timeDisplay = match.time || match.status_name || match.status?.name || match.status || '-';
                    const isLive = match.is_live || match.status_name === 'LIVE' || match.status_name === 'Inplay' || match.status === 1 || false;
                    const hasMinute = /\d+'/.test(timeDisplay) || /\d+/.test(timeDisplay);
                    const isNotHT = timeDisplay !== 'HT' && timeDisplay !== 'HT\'';
                    const isNotFT = timeDisplay !== 'FT' && timeDisplay !== 'FT\'';
                    const shouldBlink = isLive && hasMinute && isNotHT && isNotFT;
                    const timeClass = shouldBlink ? 'live-minute-blink' : (isLive ? 'text-red-500' : 'text-gray-400');
                    timeEl.className = `text-xs ${timeClass} font-medium`;
                    timeEl.textContent = timeDisplay;
                }
                
                // Update score - parse correctly, prioritize ft_score
                let scoreDisplay = '-';
                if (match.scores) {
                    // Priority 1: ft_score (full time score)
                    if (match.scores.ft_score && match.scores.ft_score !== '' && match.scores.ft_score !== null) {
                        scoreDisplay = match.scores.ft_score;
                    } 
                    // Priority 2: home_score and away_score
                    else if (match.scores.home_score !== undefined && match.scores.away_score !== undefined) {
                        const home = match.scores.home_score === '' ? '0' : match.scores.home_score;
                        const away = match.scores.away_score === '' ? '0' : match.scores.away_score;
                        scoreDisplay = `${home} - ${away}`;
                    } 
                    // Priority 3: full_time (from transformMatchToTableFormat)
                    else if (match.full_time) {
                        scoreDisplay = match.full_time;
                    }
                    // Priority 4: score field
                    else if (match.score) {
                        scoreDisplay = match.score;
                    }
                } else if (match.full_time) {
                    scoreDisplay = match.full_time;
                } else if (match.score) {
                    scoreDisplay = match.score;
                }
                
                const scoreEls = row.querySelectorAll('[data-score]');
                scoreEls.forEach(el => {
                    el.textContent = scoreDisplay;
                });
                
                // Update HT score - parse correctly
                let htScoreDisplay = '-';
                if (match.scores?.ht_score) {
                    htScoreDisplay = match.scores.ht_score;
                } else if (match.scores?.halftime) {
                    const ht = match.scores.halftime;
                    if (ht.home !== undefined && ht.away !== undefined) {
                        htScoreDisplay = `${ht.home} - ${ht.away}`;
                    }
                } else if (match.half_time) {
                    htScoreDisplay = match.half_time;
                }
                
                const htScoreEls = row.querySelectorAll('[data-ht-score]');
                htScoreEls.forEach(el => {
                    el.textContent = htScoreDisplay;
                });
            });
        } else {
            // Add new match - insert at the end
            container.insertAdjacentHTML('beforeend', buildMatchRowHTML(match, index));
        }
    });
    
    // Remove matches that are no longer in the list
    const currentMatchIds = new Set(matches.map(m => String(m.match_id || matches.indexOf(m))));
    existingRows.forEach(row => {
        const matchId = row.getAttribute('data-match-id');
        if (matchId && !currentMatchIds.has(matchId)) {
            row.remove();
        }
    });
}

// Group matches by league
function groupMatchesByLeague(matches) {
    const grouped = {};
    matches.forEach(match => {
        const leagueId = match.league_id || 'unknown_' + md5(match.league || '');
        if (!grouped[leagueId]) {
            grouped[leagueId] = [];
        }
        grouped[leagueId].push(match);
    });
    return grouped;
}

function refreshHomeMatches() {
    // Multiple cache busting strategies to ensure fresh data
    const cacheBuster = new Date().getTime();
    const random = Math.random();
    const timestamp = performance.now();
    
    // Force bypass any cache (browser, service worker, etc.)
    fetch(`{{ route("api.all.matches.table") }}?t=${cacheBuster}&_=${random}&ts=${timestamp}&nocache=${cacheBuster}`, {
        method: 'GET',
        cache: 'no-store', // Don't store in cache
        headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate, max-age=0',
            'Pragma': 'no-cache',
            'Expires': '0',
            'X-Requested-With': 'XMLHttpRequest',
            'If-Modified-Since': '0',
            'If-None-Match': '*'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            // Prefetch match data from live and upcoming matches into prefetchCache for modal
            // Each match item already has: match_events, home_stats, away_stats, odds_data, h2h
            if (typeof window !== 'undefined') {
                if (!window.prefetchCache) {
                    window.prefetchCache = new Map();
                }
                
                let cachedCount = 0;
                
                // Cache live matches
                if (data.data.live && Array.isArray(data.data.live)) {
                    data.data.live.forEach(match => {
                        const matchId = match.match_id;
                        if (matchId) {
                            const matchIdStr = String(matchId);
                            // Store match item directly (already has all needed data)
                            window.prefetchCache.set(matchIdStr, match);
                            window.prefetchCache.set(Number(matchId), match);
                            cachedCount++;
                        }
                    });
                }
                
                // Cache upcoming matches
                if (data.data.upcoming && Array.isArray(data.data.upcoming)) {
                    data.data.upcoming.forEach(match => {
                        const matchId = match.match_id;
                        if (matchId) {
                            const matchIdStr = String(matchId);
                            // Store match item directly (already has all needed data)
                            window.prefetchCache.set(matchIdStr, match);
                            window.prefetchCache.set(Number(matchId), match);
                            cachedCount++;
                        }
                    });
                }
                
                console.log(`✅ Prefetched ${cachedCount} match items for modal. Cache size: ${window.prefetchCache.size}`);
            } else if (typeof prefetchCache !== 'undefined') {
                let cachedCount = 0;
                
                if (data.data.live && Array.isArray(data.data.live)) {
                    data.data.live.forEach(match => {
                        const matchId = match.match_id;
                        if (matchId) {
                            const matchIdStr = String(matchId);
                            prefetchCache.set(matchIdStr, match);
                            prefetchCache.set(Number(matchId), match);
                            cachedCount++;
                        }
                    });
                }
                
                if (data.data.upcoming && Array.isArray(data.data.upcoming)) {
                    data.data.upcoming.forEach(match => {
                        const matchId = match.match_id;
                        if (matchId) {
                            const matchIdStr = String(matchId);
                            prefetchCache.set(matchIdStr, match);
                            prefetchCache.set(Number(matchId), match);
                            cachedCount++;
                        }
                    });
                }
                
                console.log(`Prefetched ${cachedCount} match items for modal`);
            } else {
                console.warn('⚠️ prefetchCache not available - cannot cache match details');
            }
            
            // Update live matches
            if (data.data.live && Array.isArray(data.data.live)) {
                const liveGrouped = groupMatchesByLeague(data.data.live);
                Object.keys(liveGrouped).forEach(leagueId => {
                    updateLeagueMatches(leagueId, liveGrouped[leagueId], true);
                });
            }
            
            // Update upcoming matches
            let upcomingMatches = data.data.upcoming || [];
            const now = new Date();
            upcomingMatches = upcomingMatches.filter(match => {
                const startingDatetime = match.starting_datetime;
                if (!startingDatetime) return false;
                try {
                    return new Date(startingDatetime) >= now;
                } catch (e) {
                    return false;
                }
            });
            
            if (upcomingMatches.length > 0) {
                const upcomingGrouped = groupMatchesByLeague(upcomingMatches);
                Object.keys(upcomingGrouped).forEach(leagueId => {
                    updateLeagueMatches(leagueId, upcomingGrouped[leagueId], false);
                });
            }
        }
    })
    .catch(error => {
        console.error('Error refreshing home matches:', error);
    });
}

// Start auto-refresh on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add data-match-id to existing rows for tracking
    document.querySelectorAll('[onclick*="openMatchModal"]').forEach((row, index) => {
        const matchId = row.getAttribute('onclick')?.match(/\d+/)?.[0] || index;
        row.setAttribute('data-match-id', matchId);
    });
    
    // Initial refresh immediately to populate cache for modal
    refreshHomeMatches();
    
    // Then refresh every 1 minute
    refreshInterval = setInterval(refreshHomeMatches, 60000); // 60 seconds = 1 minute
});
</script>

