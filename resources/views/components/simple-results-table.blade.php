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
            <div class="bg-white divide-y divide-gray-200">
                @foreach ($matches as $match)
                    @php
                        $matchId = $match['match_id'] ?? null;
                        $score = $match['score'] ?? '0-0';
                    @endphp
                    {{-- Mobile Layout --}}
                    <div class="sm:hidden px-2 py-3 hover:bg-gray-50 transition-colors border-b border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-xs text-gray-700">{{ $match['time'] ?? '-' }}</div>
                            @if($matchId)
                                <a href="{{ route('match.detail', $matchId) }}" 
                                   class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-2 py-1 rounded transition-colors">
                                    {{ $score }}
                                </a>
                            @else
                                <div class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">
                                    {{ $score }}
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex-1 text-right pr-2 truncate">{{ $match['home_team'] ?? '-' }}</div>
                            <div class="flex-1 text-left pl-2 truncate">{{ $match['away_team'] ?? '-' }}</div>
                        </div>
                    </div>
                    
                    {{-- Desktop Layout --}}
                    <div class="hidden sm:flex items-center px-4 py-3 hover:bg-gray-50 transition-colors">
                        {{-- Time --}}
                        <div class="w-20 text-sm text-gray-700">
                            {{ $match['time'] ?? '-' }}
                        </div>
                        
                        {{-- Home Team --}}
                        <div class="flex-1 text-sm text-gray-900 text-right truncate pr-4">
                            {{ $match['home_team'] ?? '-' }}
                        </div>
                        
                        {{-- Full-time Score (green) - Clickable link to match detail --}}
                        @if($matchId)
                            <a href="{{ route('match.detail', $matchId) }}" 
                               class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-3 py-1 rounded mx-4 min-w-[50px] text-center transition-colors cursor-pointer flex-shrink-0">
                                {{ $score }}
                            </a>
                        @else
                            <div class="bg-green-600 text-white text-sm font-bold px-3 py-1 rounded mx-4 min-w-[50px] text-center">
                                {{ $score }}
                            </div>
                        @endif
                        
                        {{-- Away Team --}}
                        <div class="flex-1 text-sm text-gray-900">
                            {{ $match['away_team'] ?? '-' }}
                        </div>
                        
                        {{-- Half-time Score (grey) --}}
                        <div class="bg-gray-600 text-white text-xs font-medium px-2 py-1 rounded ml-4 min-w-[45px] text-center">
                            {{ $match['half_time'] ?? '-' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
    
    @if (empty($finishedMatches))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <p class="text-gray-500">Không có kết quả trận đấu nào</p>
        </div>
    @endif
</div>

