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

<div class="bg-slate-800 rounded p-3 sm:p-4 md:p-6 overflow-hidden">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-4 sm:mb-6 uppercase break-words">Lịch Thi Đấu Bóng Đá Hôm Nay</h1>
    
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
                
                <div class="mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-0 mb-3">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <button onclick="toggleLeagueTable('live-{{ $leagueId }}')" 
                                    class="flex-shrink-0 p-1 text-gray-400 hover:text-white transition-colors"
                                    aria-label="Toggle table">
                                <svg id="toggle-icon-live-{{ $leagueId }}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <h2 class="text-base sm:text-lg font-bold text-white truncate pr-2">
                                <span class="truncate block">{{ $countryName ? $countryName . ': ' : '' }}{{ $leagueName }}
                                    @if(count($matches) > 0)
                                        <span class="text-gray-400 text-xs sm:text-sm font-normal">({{ count($matches) }})</span>
                                    @endif
                                </span>
                            </h2>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('standings.show', $leagueId) }}" 
                               class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-blue-600 hover:bg-blue-700 text-xs sm:text-sm text-white rounded transition-colors">
                                <span>BXH</span>
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div id="live-{{ $leagueId }}" class="bg-slate-900 rounded overflow-hidden">
                        <!-- Desktop Table Header -->
                        <div class="hidden md:grid md:grid-cols-11 gap-2 bg-slate-700 px-4 py-2 text-xs font-semibold text-gray-300">
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
                
                <div class="mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-0 mb-3">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <button onclick="toggleLeagueTable('upcoming-{{ $leagueId }}')" 
                                    class="flex-shrink-0 p-1 text-gray-400 hover:text-white transition-colors"
                                    aria-label="Toggle table">
                                <svg id="toggle-icon-upcoming-{{ $leagueId }}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <h2 class="text-base sm:text-lg font-bold text-white truncate pr-2">
                                <span class="truncate block">{{ $countryName ? $countryName . ': ' : '' }}{{ $leagueName }}
                                    @if(count($matches) > 0)
                                        <span class="text-gray-400 text-xs sm:text-sm font-normal">({{ count($matches) }})</span>
                                    @endif
                                </span>
                            </h2>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('standings.show', $leagueId) }}" 
                               class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-blue-600 hover:bg-blue-700 text-xs sm:text-sm text-white rounded transition-colors">
                                <span>BXH</span>
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div id="upcoming-{{ $leagueId }}" class="bg-slate-900 rounded overflow-hidden">
                        <!-- Desktop Table Header -->
                        <div class="hidden md:grid md:grid-cols-11 gap-2 bg-slate-700 px-4 py-2 text-xs font-semibold text-gray-300">
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
            <div class="text-center py-8 sm:py-12 text-gray-400 text-sm sm:text-base">
                <p>Không có trận đấu nào hôm nay</p>
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

