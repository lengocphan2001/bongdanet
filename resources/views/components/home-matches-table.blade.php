@props([
    'liveMatches' => [],
    'upcomingMatches' => [],
])

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
</script>

