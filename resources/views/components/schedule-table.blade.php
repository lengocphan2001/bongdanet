@props([
    'scheduleMatches' => [],
    'date' => null,
])

@php
    // Get date from request or use provided date or default to today
    $currentDate = $date ?? request()->get('date', date('Y-m-d'));
    
    // Group matches by league_id to ensure unique leagues (avoid grouping different leagues with same name)
    // ALWAYS use league_id as the primary key to distinguish leagues, even if they have the same name
    $matchesByLeague = [];
    foreach ($scheduleMatches as $match) {
        $leagueId = $match['league_id'] ?? null;
        $leagueName = $match['league'] ?? 'Other';
        $countryName = $match['country_name'] ?? null;
        
        // ALWAYS use league_id as key if available (even if it's 0 or empty string, convert to string)
        // This ensures different leagues with same name (e.g., "Ligue 1" France vs "Ligue 1" Algeria) are separated
        if ($leagueId !== null && $leagueId !== '') {
            // Use league_id as the unique key - this is the most reliable way to distinguish leagues
            $leagueKey = (string)$leagueId;
        } elseif ($countryName) {
            // Fallback: use league_name + country_name if no league_id
            $leagueKey = $leagueName . '_' . $countryName;
        } else {
            // Last fallback: just league_name (should rarely happen)
            $leagueKey = $leagueName;
        }
        
        if (!isset($matchesByLeague[$leagueKey])) {
            $matchesByLeague[$leagueKey] = [
                'league_id' => $leagueId,
                'league_name' => $leagueName,
                'country_name' => $countryName,
                'matches' => []
            ];
        }
        $matchesByLeague[$leagueKey]['matches'][] = $match;
    }
    
    // If no matches, show empty state
    if (empty($matchesByLeague)) {
        $matchesByLeague['empty'] = [
            'league_name' => 'Không có trận đấu',
            'country_name' => null,
            'matches' => []
        ];
    }
@endphp

<div id="schedule-table-container" class="space-y-4" data-date="{{ $currentDate }}">
    @foreach ($matchesByLeague as $leagueKey => $leagueData)
        @php
            $leagueName = $leagueData['league_name'] ?? 'Other';
            $countryName = $leagueData['country_name'] ?? null;
            $matches = $leagueData['matches'] ?? [];
            // Format league display: "League Name - Country Name" or just "League Name"
            $leagueDisplay = $countryName ? $leagueName . ' - ' . $countryName : $leagueName;
        @endphp
        @if (!empty($matches))
            {{-- League Section --}}
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center gap-2 sm:gap-3 mb-2 p-1 sm:p-2 bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                    <button onclick="toggleLeagueTable('schedule-{{ $leagueKey }}')" 
                            class="flex-shrink-0 p-1.5 sm:p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-500/10 rounded-lg transition-all duration-200 group"
                            aria-label="Toggle table">
                        <svg id="toggle-icon-schedule-{{ $leagueKey }}" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md bg-blue-500/20 text-blue-400 text-[10px] sm:text-xs font-semibold flex-shrink-0">LỊCH</span>
                    <h2 class="flex text-sm sm:text-base md:text-lg font-bold text-white overflow-hidden text-ellipsis whitespace-nowrap flex-1 min-w-0">
                        <span class="inline-block truncate">
                            {{ $countryName ? $countryName . ': ' : '' }}{{ $leagueName }}
                            @if(count($matches) > 0)
                                <span class="text-blue-400 text-[10px] sm:text-xs md:text-sm font-normal ml-1 sm:ml-2">({{ count($matches) }})</span>
                            @endif
                        </span>
                    </h2>
                    @if($leagueData['league_id'] && $leagueData['league_id'] !== 'unknown' && is_numeric($leagueData['league_id']))
                        <div class="hidden sm:flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                            <a href="{{ route('results.league', $leagueData['league_id']) }}" class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-[10px] sm:text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                <span>KQ</span>
                            </a>
                            <a href="{{ route('standings.show', $leagueData['league_id']) }}" class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-[10px] sm:text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 hover:scale-105">
                                <span>BXH</span>
                            </a>
                        </div>
                    @endif
                </div>
                
                {{-- Schedule Table --}}
                <div id="schedule-{{ $leagueKey }}" class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b border-slate-600/50 backdrop-blur-sm">
                                <tr>
                                    <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase whitespace-nowrap">Giờ</th>
                                    <th class="px-3 sm:px-4 py-3 text-right text-xs font-bold text-gray-200 uppercase whitespace-nowrap">Đội nhà</th>
                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase whitespace-nowrap">Tỷ số</th>
                                    <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase whitespace-nowrap">Đội khách</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/50">
                                @foreach ($matches as $match)
                                    @php
                                        $matchId = $match['match_id'] ?? null;
                                    @endphp
                                    <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 {{ $matchId ? 'cursor-pointer group' : '' }}"
                                        @if($matchId) onclick="openMatchModal({{ $matchId }})" @endif>
                                        {{-- Time --}}
                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap">
                                            <div class="text-xs sm:text-sm font-bold text-blue-400 bg-blue-500/10 px-2 py-1 rounded inline-block">
                                                {{ $match['time'] ?? '-' }}
                                            </div>
                                        </td>
                                        
                                        {{-- Home Team --}}
                                        <td class="px-3 sm:px-4 py-3 text-right">
                                            <div class="flex items-center justify-end space-x-2 group-hover:text-emerald-400 transition-colors">
                                                <span class="text-xs sm:text-sm text-white font-medium truncate">{{ $match['home_team'] ?? '-' }}</span>
                                                @if (!empty($match['home_team_info']['img'] ?? null))
                                                    <div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                        <img src="{{ $match['home_team_info']['img'] }}" 
                                                             alt="{{ $match['home_team'] }}" 
                                                             class="w-full h-full object-contain"
                                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\'>{{ substr($match['home_team'] ?? 'H', 0, 1) }}</div>';">
                                                    </div>
                                                @else
                                                    <div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">{{ substr($match['home_team'] ?? 'H', 0, 1) }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        {{-- Score button --}}
                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-center">
                                            @if($matchId)
                                                <button onclick="event.stopPropagation(); openMatchModal({{ $matchId }})" 
                                                        class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-lg min-w-[50px] sm:min-w-[60px] transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                                    ?-?
                                                </button>
                                            @else
                                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-lg min-w-[50px] sm:min-w-[60px] inline-block shadow-lg shadow-emerald-500/25">
                                                    ?-?
                                                </div>
                                            @endif
                                        </td>
                                        
                                        {{-- Away Team --}}
                                        <td class="px-3 sm:px-4 py-3 text-left">
                                            <div class="flex items-center space-x-2 group-hover:text-emerald-400 transition-colors">
                                                @if (!empty($match['away_team_info']['img'] ?? null))
                                                    <div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                        <img src="{{ $match['away_team_info']['img'] }}" 
                                                             alt="{{ $match['away_team'] }}" 
                                                             class="w-full h-full object-contain"
                                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\'>{{ substr($match['away_team'] ?? 'A', 0, 1) }}</div>';">
                                                    </div>
                                                @else
                                                    <div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">{{ substr($match['away_team'] ?? 'A', 0, 1) }}</div>
                                                @endif
                                                <span class="text-xs sm:text-sm text-white font-medium truncate">{{ $match['away_team'] ?? '-' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    
    @if (empty($scheduleMatches))
        <div id="schedule-empty-state" class="text-center py-12 sm:py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-gray-400 text-sm sm:text-base font-medium">Không có trận đấu nào trong ngày này</p>
        </div>
    @endif
</div>

<script>
(function() {
    // Auto-refresh schedule table every 10 seconds
    let refreshInterval;
    
    function groupMatchesByLeague(matches) {
        const grouped = {};
        matches.forEach(match => {
            const league = match.league || 'Other';
            if (!grouped[league]) {
                grouped[league] = [];
            }
            grouped[league].push(match);
        });
        return grouped;
    }
    
    function buildScheduleTable(matchesByLeague) {
        let html = '';
        
        Object.keys(matchesByLeague).forEach((league, index) => {
            const matches = matchesByLeague[league];
            if (matches.length > 0) {
                // Generate unique league key for toggle
                const leagueKey = `schedule-${league.replace(/\s+/g, '-').toLowerCase()}-${index}`;
                html += `
                    <div class="mb-6 sm:mb-8">
                        <div class="flex items-center gap-2 sm:gap-3 mb-2 p-1 sm:p-2 bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                            <button onclick="toggleLeagueTable('${leagueKey}')" 
                                    class="flex-shrink-0 p-1.5 sm:p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-500/10 rounded-lg transition-all duration-200 group"
                                    aria-label="Toggle table">
                                <svg id="toggle-icon-${leagueKey}" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md bg-blue-500/20 text-blue-400 text-[10px] sm:text-xs font-semibold flex-shrink-0">LỊCH</span>
                            <h2 class="flex text-sm sm:text-base md:text-lg font-bold text-white overflow-hidden text-ellipsis whitespace-nowrap flex-1 min-w-0">
                                <span class="inline-block truncate">
                                    ${league}
                                    ${matches.length > 0 ? `<span class="text-blue-400 text-[10px] sm:text-xs md:text-sm font-normal ml-1 sm:ml-2">(${matches.length})</span>` : ''}
                                </span>
                            </h2>
                        </div>
                        <div id="${leagueKey}" class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b border-slate-600/50 backdrop-blur-sm">
                                        <tr>
                                            <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase whitespace-nowrap">Giờ</th>
                                            <th class="px-3 sm:px-4 py-3 text-right text-xs font-bold text-gray-200 uppercase whitespace-nowrap">Đội nhà</th>
                                            <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase whitespace-nowrap">Tỷ số</th>
                                            <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase whitespace-nowrap">Đội khách</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-700/50">
                                        ${matches.map(match => {
                                            const matchId = match.match_id || null;
                                            const onClick = matchId ? `onclick="openMatchModal(${matchId})"` : '';
                                            const cursorClass = matchId ? 'cursor-pointer group' : '';
                                            return `
                                            <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 ${cursorClass}" ${onClick}>
                                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs sm:text-sm font-bold text-blue-400 bg-blue-500/10 px-2 py-1 rounded inline-block">
                                                        ${match.time || '-'}
                                                    </div>
                                                </td>
                                                <td class="px-3 sm:px-4 py-3 text-right">
                                                    <div class="flex items-center justify-end space-x-2 group-hover:text-emerald-400 transition-colors">
                                                        <span class="text-xs sm:text-sm text-white font-medium truncate">${match.home_team || '-'}</span>
                                                        ${match.home_team_info?.img ? `<div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors"><img src="${match.home_team_info.img}" alt="${match.home_team}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\\'>${(match.home_team || 'H').charAt(0)}</div>';"></div>` : `<div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">${(match.home_team || 'H').charAt(0)}</div>`}
                                                    </div>
                                                </td>
                                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-center">
                                                    ${matchId ? `<button onclick="event.stopPropagation(); openMatchModal(${matchId})" class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-lg min-w-[50px] sm:min-w-[60px] transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">?-?</button>` : `<div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-xs sm:text-sm font-black px-3 py-1.5 rounded-lg min-w-[50px] sm:min-w-[60px] inline-block shadow-lg shadow-emerald-500/25">?-?</div>`}
                                                </td>
                                                <td class="px-3 sm:px-4 py-3 text-left">
                                                    <div class="flex items-center space-x-2 group-hover:text-emerald-400 transition-colors">
                                                        ${match.away_team_info?.img ? `<div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors"><img src="${match.away_team_info.img}" alt="${match.away_team}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\\'>${(match.away_team || 'A').charAt(0)}</div>';"></div>` : `<div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">${(match.away_team || 'A').charAt(0)}</div>`}
                                                        <span class="text-xs sm:text-sm text-white font-medium truncate">${match.away_team || '-'}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
        
        return html;
    }
    
    function refreshScheduleTable() {
        const container = document.getElementById('schedule-table-container');
        if (!container) return;
        
        // Get date from URL parameter or container data attribute
        const urlParams = new URLSearchParams(window.location.search);
        const date = urlParams.get('date') || container.getAttribute('data-date') || '{{ $currentDate }}';
        
        // Update container data attribute
        container.setAttribute('data-date', date);
        
        fetch(`{{ route('api.schedule.matches.table') }}?date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.scheduleMatches) {
                    const matches = data.data.scheduleMatches;
                    const matchesByLeague = groupMatchesByLeague(matches);
                    
                    // Update container content
                    const newHtml = buildScheduleTable(matchesByLeague);
                    container.innerHTML = newHtml;
                    
                    // Show/hide empty state
                    const emptyState = document.getElementById('schedule-empty-state');
                    if (matches.length === 0) {
                        if (!emptyState) {
                            container.innerHTML += '<div id="schedule-empty-state" class="text-center py-12 sm:py-16"><div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4"><svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><p class="text-gray-400 text-sm sm:text-base font-medium">Không có trận đấu nào trong ngày này</p></div>';
                        }
                    } else if (emptyState) {
                        emptyState.remove();
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing schedule table:', error);
            });
    }
    
    // Start auto-refresh: refresh every 3 minutes (reduced from 10 seconds to save API calls)
    // Schedule matches don't change frequently, so we can refresh less often
    // Only refresh if page is visible (not in background tab)
    function checkAndRefreshScheduleTable() {
        if (document.hidden) {
            return;
        }
        refreshScheduleTable();
    }
    
    refreshInterval = setInterval(checkAndRefreshScheduleTable, 180000); // 180000ms = 180 seconds (3 minutes)
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
})();

// Toggle league table function for schedule page
// Check if function already exists to avoid redefinition
if (typeof window.toggleLeagueTable === 'undefined') {
    window.toggleLeagueTable = function(leagueId) {
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
    };
}
</script>

