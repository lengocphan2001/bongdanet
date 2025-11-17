@props([
    'finishedMatches' => [],
])

@php
    // Group matches by league_id to ensure unique leagues (avoid grouping different leagues with same name)
    // ALWAYS use league_id as the primary key to distinguish leagues, even if they have the same name
    $matchesByLeague = [];
    foreach ($finishedMatches as $match) {
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
            'league_name' => 'Không có kết quả',
            'country_name' => null,
            'matches' => []
        ];
    }
@endphp

<div class="space-y-4">
    @foreach ($matchesByLeague as $leagueKey => $leagueData)
        @php
            $leagueName = $leagueData['league_name'] ?? 'Other';
            $countryName = $leagueData['country_name'] ?? null;
            $matches = $leagueData['matches'] ?? [];
            // Format league display: "League Name - Country Name" or just "League Name"
            $leagueDisplay = $countryName ? $leagueName . ' - ' . $countryName : $leagueName;
        @endphp
        @if (!empty($matches))
            @php
                $leagueId = $leagueData['league_id'] ?? null;
            @endphp
            {{-- League Header --}}
            <div class="bg-[#1a5f2f] text-white px-4 py-2 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-bold">Kết quả bóng đá {{ $leagueDisplay }}</span>
                </div>
                <div class="flex items-center space-x-4 text-sm">
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
            
            {{-- Match Results --}}
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
                                @php
                                    $matchId = $match['match_id'] ?? null;
                                    $score = $match['score'] ?? '0-0';
                                @endphp
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
                                    
                                    {{-- Full-time Score (green) - Clickable link to match detail --}}
                                    <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center">
                                        @if($matchId)
                                            <a href="{{ route('match.detail', $matchId) }}" 
                                               class="inline-block bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-bold px-2 sm:px-4 py-1 rounded min-w-[45px] sm:min-w-[60px] text-center transition-colors cursor-pointer">
                                                {{ $score }}
                                            </a>
                                        @else
                                            <div class="inline-block bg-green-600 text-white text-xs sm:text-sm font-bold px-2 sm:px-4 py-1 rounded min-w-[45px] sm:min-w-[60px] text-center">
                                                {{ $score }}
                                            </div>
                                        @endif
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
    
    @if (empty($finishedMatches))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <p class="text-gray-500">Không có kết quả trận đấu nào</p>
        </div>
    @endif
</div>

