@extends('layouts.app')

@section('title', 'Livescore - Bongdanet')

@section('content')
<style>
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    .live-minute-blink {
        animation: blink 1.5s ease-in-out infinite;
        font-weight: 600;
        color: #059669; /* green-600 */
    }
</style>
<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'BONGDANET', 'url' => route('home')],
        ['label' => 'Livescore', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 order-2 lg:order-1">
                <x-football-schedule-menu activeItem="Ngoại Hạng Anh" />
            </aside>
            
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-2">
                <div class="space-y-4" id="livescore-content">
                    @if(empty($groupedMatches))
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                            <p class="text-gray-500">Không có trận đấu nào đang diễn ra</p>
                        </div>
                    @else
                        @foreach($groupedMatches as $leagueKey => $leagueData)
                            @php
                                $leagueId = $leagueData['league_id'] ?? null;
                                $leagueName = $leagueData['league_name'] ?? 'N/A';
                                $countryName = $leagueData['country_name'] ?? '';
                                $matches = $leagueData['matches'] ?? [];
                            @endphp
                            
                            {{-- League Section --}}
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                {{-- League Header --}}
                                <div class="bg-[#1a5f2f] px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-sm font-bold text-white">{{ $leagueName }}</h3>
                                        @if($countryName)
                                            <span class="text-xs text-gray-300">({{ $countryName }})</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        @if($leagueId && $leagueId !== 'unknown' && is_numeric($leagueId))
                                            <a href="{{ route('schedule.league', $leagueId) }}" class="text-xs text-white hover:text-green-300 transition-colors">Lịch</a>
                                            <a href="{{ route('results.league', $leagueId) }}" class="text-xs text-white hover:text-green-300 transition-colors">KQ</a>
                                            <a href="{{ route('standings.show', $leagueId) }}" class="text-xs text-white hover:text-green-300 transition-colors">BXH</a>
                                        @else
                                            <span class="text-xs text-gray-400">Lịch</span>
                                            <span class="text-xs text-gray-400">KQ</span>
                                            <span class="text-xs text-gray-400">BXH</span>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Matches List --}}
                                <div class="bg-gray-50">
                                    @foreach($matches as $match)
                                        @php
                                            $matchId = $match['match_id'] ?? null;
                                            $homeTeam = $match['home_team'] ?? '-';
                                            $awayTeam = $match['away_team'] ?? '-';
                                            
                                            // Get current score
                                            $currentScore = $match['score'] ?? '0-0';
                                            $scores = $match['scores'] ?? [];
                                            $homeScore = $scores['home_score'] ?? '';
                                            $awayScore = $scores['away_score'] ?? '';
                                            if ($homeScore === '' && $awayScore === '') {
                                                $currentScore = '0-0';
                                            } else {
                                                $homeScore = $homeScore === '' ? '0' : $homeScore;
                                                $awayScore = $awayScore === '' ? '0' : $awayScore;
                                                $currentScore = $homeScore . '-' . $awayScore;
                                            }
                                            
                                            // Get HT score
                                            $htScore = $scores['ht_score'] ?? '0-0';
                                            if (empty($htScore)) {
                                                $htScore = '0-0';
                                            }
                                            
                                            // Get minute display - giống trang home
                                            $minute = $match['minute'] ?? null;
                                            $statusPeriod = $match['status_period'] ?? null;
                                            $statusName = $match['status_name'] ?? null;
                                            $extraMinute = $match['extra_minute'] ?? null;
                                            $status = $match['status'] ?? null;
                                            $timeDisplay = '-';
                                            $shouldBlink = false; // Flag để xác định có nhấp nháy không
                                            
                                            // Ưu tiên hiển thị số phút đang thi đấu nếu có
                                            if ($match['is_live'] ?? false) {
                                                // Nếu đang live và có minute, hiển thị minute
                                                if ($minute !== null && $minute !== '') {
                                                    // Format minute with extra time if available (e.g., "90+1'")
                                                    if ($extraMinute !== null && $extraMinute !== '' && $extraMinute > 0) {
                                                        $timeDisplay = $minute . '+' . $extraMinute . "'";
                                                    } else {
                                                        $timeDisplay = $minute . "'";
                                                    }
                                                    $shouldBlink = true; // Nhấp nháy khi có số phút
                                                } elseif ($statusPeriod == 'Halftime' || $statusName == 'Halftime' || $status == 11) {
                                                    // Nếu đang Halftime và không có minute, hiển thị HT
                                                    $timeDisplay = 'HT';
                                                } else {
                                                    // Nếu live nhưng chưa có minute, hiển thị "0'"
                                                    $timeDisplay = "0'";
                                                    $shouldBlink = true; // Nhấp nháy khi live
                                                }
                                            } elseif ($statusPeriod == 'Halftime' || $statusName == 'Halftime' || $status == 11) {
                                                // Nếu không live nhưng đang Halftime
                                                $timeDisplay = 'HT';
                                            } elseif ($status == 2 || $statusName == 'Finished') {
                                                // Nếu đã kết thúc
                                                $timeDisplay = 'FT';
                                            } else {
                                                // Fallback
                                                $timeDisplay = $match['time'] ?? '-';
                                            }
                                            
                                            $isLive = $match['is_live'] ?? false;
                                        @endphp
                                        
                                        <div class="px-2 sm:px-4 py-3 border-b border-gray-200 hover:bg-gray-100 transition-colors {{ $matchId ? 'cursor-pointer' : '' }}"
                                             @if($matchId) onclick="window.location.href='{{ route('match.detail', $matchId) }}'" @endif>
                                            {{-- Mobile Layout --}}
                                            <div class="flex flex-col sm:hidden space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-1.5">
                                                        @if($isLive)
                                                            <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M8 5v14l11-7z"/>
                                                            </svg>
                                                        @endif
                                                        <span class="text-xs {{ $shouldBlink ? 'live-minute-blink' : 'text-gray-700' }}">{{ $timeDisplay }}</span>
                                                    </div>
                                                    @if($matchId)
                                                        <a href="{{ route('match.detail', $matchId) }}" 
                                                           class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-2 py-1 rounded transition-colors">
                                                            {{ $currentScore }}
                                                        </a>
                                                    @else
                                                        <div class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">
                                                            {{ $currentScore }}
                                                        </div>
                                                    @endif
                                                    <div class="bg-gray-600 text-white text-xs font-medium px-2 py-1 rounded">
                                                        {{ $htScore }}
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between text-xs">
                                                    <div class="flex-1 text-right pr-2 truncate">{{ $homeTeam }}</div>
                                                    <div class="flex-1 text-left pl-2 truncate">{{ $awayTeam }}</div>
                                                </div>
                                            </div>
                                            
                                            {{-- Desktop Layout --}}
                                            <div class="hidden sm:flex items-center">
                                                {{-- Left: Time and Play Icon --}}
                                                <div class="flex items-center space-x-1.5 w-20 flex-shrink-0">
                                                    @if($isLive)
                                                        <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z"/>
                                                        </svg>
                                                    @endif
                                                    <span class="text-sm {{ $shouldBlink ? 'live-minute-blink' : 'text-gray-700' }}">{{ $timeDisplay }}</span>
                                                </div>
                                                
                                                {{-- Home Team --}}
                                                <div class="flex-1 text-sm text-gray-900 text-right pr-4 truncate">
                                                    {{ $homeTeam }}
                                                </div>
                                                
                                                {{-- Score --}}
                                                @if($matchId)
                                                    <a href="{{ route('match.detail', $matchId) }}" 
                                                       class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-3 py-1 rounded mx-4 min-w-[50px] text-center transition-colors flex-shrink-0">
                                                        {{ $currentScore }}
                                                    </a>
                                                @else
                                                    <div class="bg-green-600 text-white text-sm font-bold px-3 py-1 rounded mx-4 min-w-[50px] text-center flex-shrink-0">
                                                        {{ $currentScore }}
                                                    </div>
                                                @endif
                                                
                                                {{-- Away Team --}}
                                                <div class="flex-1 text-sm text-gray-900 text-left pl-4 truncate">
                                                    {{ $awayTeam }}
                                                </div>
                                                
                                                {{-- HT Score --}}
                                                <div class="bg-gray-600 text-white text-xs font-medium px-2 py-1 rounded ml-4 min-w-[45px] text-center flex-shrink-0">
                                                    {{ $htScore }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4 order-3">
                <x-football-results-menu activeItem="Ngoại Hạng Anh" />
                <x-match-schedule activeDate="H.nay" />
                <x-fifa-ranking />
            </aside>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const livescoreContent = document.getElementById('livescore-content');
    const updateUrl = '{{ route("api.livescore.data") }}';
    // Base URLs for routes
    const matchDetailBaseUrl = '{{ url("/ket-qua") }}';
    const scheduleLeagueBaseUrl = '{{ url("/lich-thi-dau") }}';
    const resultsLeagueBaseUrl = '{{ url("/ket-qua-bong-da") }}';
    const standingsShowBaseUrl = '{{ url("/bang-xep-hang-bong-da") }}';
    
    function formatTimeDisplay(match) {
        const minute = match.minute ?? null;
        const statusPeriod = match.status_period ?? null;
        const statusName = match.status_name ?? null;
        const extraMinute = match.extra_minute ?? null;
        const status = match.status ?? null;
        const isLive = match.is_live ?? false;
        
        let timeDisplay = '-';
        let shouldBlink = false;
        
        if (isLive) {
            if (minute !== null && minute !== '') {
                if (extraMinute !== null && extraMinute !== '' && extraMinute > 0) {
                    timeDisplay = minute + '+' + extraMinute + "'";
                } else {
                    timeDisplay = minute + "'";
                }
                shouldBlink = true;
            } else if (statusPeriod == 'Halftime' || statusName == 'Halftime' || status == 11) {
                timeDisplay = 'HT';
            } else {
                timeDisplay = "0'";
                shouldBlink = true;
            }
        } else if (statusPeriod == 'Halftime' || statusName == 'Halftime' || status == 11) {
            timeDisplay = 'HT';
        } else if (status == 2 || statusName == 'Finished') {
            timeDisplay = 'FT';
        } else {
            timeDisplay = match.time ?? '-';
        }
        
        return { timeDisplay, shouldBlink };
    }
    
    function formatScore(scores) {
        const homeScore = scores.home_score ?? '';
        const awayScore = scores.away_score ?? '';
        
        if (homeScore === '' && awayScore === '') {
            return '0-0';
        }
        
        const home = homeScore === '' ? '0' : homeScore;
        const away = awayScore === '' ? '0' : awayScore;
        return home + '-' + away;
    }
    
    function renderMatches(groupedMatches) {
        if (!groupedMatches || Object.keys(groupedMatches).length === 0) {
            livescoreContent.innerHTML = `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <p class="text-gray-500">Không có trận đấu nào đang diễn ra</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        for (const [leagueKey, leagueData] of Object.entries(groupedMatches)) {
            const leagueId = leagueData.league_id ?? null;
            const leagueName = leagueData.league_name ?? 'N/A';
            const countryName = leagueData.country_name ?? '';
            const matches = leagueData.matches ?? [];
            
            html += `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-[#1a5f2f] px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-bold text-white">${leagueName}</h3>
                            ${countryName ? `<span class="text-xs text-gray-300">(${countryName})</span>` : ''}
                        </div>
                        <div class="flex items-center space-x-4">
                            ${(leagueId && leagueId !== 'unknown' && leagueId !== null && !isNaN(leagueId)) ? `
                                <a href="${scheduleLeagueBaseUrl}/${leagueId}" class="text-xs text-white hover:text-green-300 transition-colors">Lịch</a>
                                <a href="${resultsLeagueBaseUrl}/${leagueId}" class="text-xs text-white hover:text-green-300 transition-colors">KQ</a>
                                <a href="${standingsShowBaseUrl}/${leagueId}" class="text-xs text-white hover:text-green-300 transition-colors">BXH</a>
                            ` : `
                                <span class="text-xs text-gray-400">Lịch</span>
                                <span class="text-xs text-gray-400">KQ</span>
                                <span class="text-xs text-gray-400">BXH</span>
                            `}
                        </div>
                    </div>
                    <div class="bg-gray-50">
            `;
            
            matches.forEach(match => {
                const matchId = match.match_id ?? null;
                const homeTeam = match.home_team ?? '-';
                const awayTeam = match.away_team ?? '-';
                const currentScore = formatScore(match.scores ?? {});
                const htScore = match.scores?.ht_score ?? '0-0';
                const { timeDisplay, shouldBlink } = formatTimeDisplay(match);
                const isLive = match.is_live ?? false;
                
                const timeClass = shouldBlink ? 'live-minute-blink' : 'text-gray-700';
                const matchDetailUrl = matchId ? `${matchDetailBaseUrl}/${matchId}` : '';
                const onClick = matchId ? `onclick="window.location.href='${matchDetailUrl}'"` : '';
                const cursorClass = matchId ? 'cursor-pointer' : '';
                
                html += `
                    <div class="px-4 py-3 border-b border-gray-200 hover:bg-gray-100 transition-colors ${cursorClass}" ${onClick}>
                        <div class="flex items-center">
                            <div class="flex items-center space-x-1.5 w-20 flex-shrink-0">
                                ${isLive ? `
                                    <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                ` : ''}
                                <span class="text-sm ${timeClass}">${timeDisplay}</span>
                            </div>
                            <div class="flex-1 text-sm text-gray-900 text-right pr-4">
                                ${homeTeam}
                            </div>
                            ${matchId ? `
                                <a href="${matchDetailUrl}" 
                                   class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-3 py-1 rounded mx-4 min-w-[50px] text-center transition-colors flex-shrink-0">
                                    ${currentScore}
                                </a>
                            ` : `
                                <div class="bg-green-600 text-white text-sm font-bold px-3 py-1 rounded mx-4 min-w-[50px] text-center flex-shrink-0">
                                    ${currentScore}
                                </div>
                            `}
                            <div class="flex-1 text-sm text-gray-900 text-left pl-4">
                                ${awayTeam}
                            </div>
                            <div class="bg-gray-600 text-white text-xs font-medium px-2 py-1 rounded ml-4 min-w-[45px] text-center flex-shrink-0">
                                ${htScore}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }
        
        livescoreContent.innerHTML = html;
    }
    
    function updateLivescore() {
        fetch(updateUrl)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.groupedMatches) {
                    renderMatches(data.data.groupedMatches);
                }
            })
            .catch(error => {
                console.error('Error updating livescore:', error);
            });
    }
    
    // Update every 1 minute
    setInterval(updateLivescore, 60000); // 60000ms = 60 seconds (1 minute)
});
</script>
@endsection

