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
            {{-- League Header --}}
            <div class="bg-[#1a5f2f] text-white px-4 py-2 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-bold">Lịch thi đấu {{ $leagueDisplay }}</span>
                </div>
            </div>
            
            {{-- Schedule Table --}}
            <div class="bg-white overflow-hidden border border-gray-200 rounded-lg">
                <div class="overflow-x-auto -mx-2 sm:mx-0">
                    <table class="min-w-[480px] sm:min-w-[600px] w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap" style="min-width: 50px;">Giờ</th>
                                <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap" style="min-width: 120px;"></th>
                                <th class="px-2 sm:px-4 py-2 sm:py-3 text-center text-xs font-bold text-gray-700 uppercase whitespace-nowrap" style="min-width: 70px;"></th>
                                <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap" style="min-width: 120px;"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($matches as $match)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Time --}}
                                    <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                        {{ $match['time'] ?? '-' }}
                                    </td>
                                    
                                    {{-- Home Team --}}
                                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-900">
                                        <div class="flex items-center space-x-1 sm:space-x-2 justify-end">
                                            @if (!empty($match['home_team_info']['img'] ?? null))
                                                <img src="{{ $match['home_team_info']['img'] }}" 
                                                     alt="{{ $match['home_team'] }}" 
                                                     class="w-4 h-4 sm:w-6 sm:h-6 object-contain flex-shrink-0">
                                            @endif
                                            <span class="truncate">{{ $match['home_team'] ?? '-' }}</span>
                                        </div>
                                    </td>
                                    
                                    {{-- Score button (green with ?-?) --}}
                                    <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center">
                                        <div class="inline-block bg-green-600 text-white text-xs sm:text-sm font-medium px-2 sm:px-4 py-1 rounded min-w-[45px] sm:min-w-[60px] text-center">
                                            ?-?
                                        </div>
                                    </td>
                                    
                                    {{-- Away Team --}}
                                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-900">
                                        <div class="flex items-center space-x-1 sm:space-x-2 justify-start">
                                            <span class="truncate">{{ $match['away_team'] ?? '-' }}</span>
                                            @if (!empty($match['away_team_info']['img'] ?? null))
                                                <img src="{{ $match['away_team_info']['img'] }}" 
                                                     alt="{{ $match['away_team'] }}" 
                                                     class="w-4 h-4 sm:w-6 sm:h-6 object-contain flex-shrink-0">
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
    
    @if (empty($scheduleMatches))
        <div id="schedule-empty-state" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <p class="text-gray-500">Không có trận đấu nào trong ngày này</p>
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
        
        Object.keys(matchesByLeague).forEach(league => {
            const matches = matchesByLeague[league];
            if (matches.length > 0) {
                html += `
                    <div class="bg-[#1a5f2f] text-white px-4 py-2 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-bold">Lịch thi đấu ${league}</span>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden border border-gray-200 rounded-lg">
                        <div class="overflow-x-auto -mx-2 sm:mx-0">
                            <table class="min-w-[480px] sm:min-w-[600px] w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap" style="min-width: 50px;">Giờ</th>
                                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap" style="min-width: 120px;"></th>
                                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-center text-xs font-bold text-gray-700 uppercase whitespace-nowrap" style="min-width: 70px;"></th>
                                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap" style="min-width: 120px;"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${matches.map(match => `
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                                ${match.time || '-'}
                                            </td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-900">
                                                <div class="flex items-center space-x-1 sm:space-x-2 justify-end">
                                                    ${match.home_team_info?.img ? `<img src="${match.home_team_info.img}" alt="${match.home_team}" class="w-4 h-4 sm:w-6 sm:h-6 object-contain flex-shrink-0">` : ''}
                                                    <span class="truncate">${match.home_team || '-'}</span>
                                                </div>
                                            </td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center">
                                                <div class="inline-block bg-green-600 text-white text-xs sm:text-sm font-medium px-2 sm:px-4 py-1 rounded min-w-[45px] sm:min-w-[60px] text-center">
                                                    ?-?
                                                </div>
                                            </td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-900">
                                                <div class="flex items-center space-x-1 sm:space-x-2 justify-start">
                                                    <span class="truncate">${match.away_team || '-'}</span>
                                                    ${match.away_team_info?.img ? `<img src="${match.away_team_info.img}" alt="${match.away_team}" class="w-4 h-4 sm:w-6 sm:h-6 object-contain flex-shrink-0">` : ''}
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
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
                            container.innerHTML += '<div id="schedule-empty-state" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center"><p class="text-gray-500">Không có trận đấu nào trong ngày này</p></div>';
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
</script>

