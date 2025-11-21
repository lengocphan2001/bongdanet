@extends('layouts.app')

@section('title', 'keobongda.co - Livescore')

@section('content')
<style>
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    .live-minute-blink {
        animation: blink 1.5s ease-in-out infinite;
        font-weight: 600;
        color: #10b981; /* emerald-500 */
    }
</style>
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Livescore', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-gradient-to-b from-red-500 to-red-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Livescore - Trận Đấu Đang Diễn Ra</span>
                        </h1>
                    </div>
                    
                    <div class="space-y-4 sm:space-y-6" id="livescore-content">
                        @if(empty($groupedMatches))
                            <div class="text-center py-12 sm:py-16">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-400 text-sm sm:text-base font-medium">Không có trận đấu nào đang diễn ra</p>
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
                                <div class="mb-6 sm:mb-8">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 mb-4 p-4 bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-red-500/20 text-red-400 text-xs font-semibold animate-pulse">LIVE</span>
                                            <h2 class="text-base sm:text-lg font-bold text-white truncate">
                                                <span class="truncate block">{{ $countryName ? $countryName . ': ' : '' }}{{ $leagueName }}
                                                    @if(count($matches) > 0)
                                                        <span class="text-red-400 text-xs sm:text-sm font-normal ml-2">({{ count($matches) }})</span>
                                                    @endif
                                                </span>
                                            </h2>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            @if($leagueId && $leagueId !== 'unknown' && is_numeric($leagueId))
                                                <a href="{{ route('schedule.league', $leagueId) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-105">
                                                    <span>Lịch</span>
                                                </a>
                                                <a href="{{ route('results.league', $leagueId) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                                    <span>KQ</span>
                                                </a>
                                                <a href="{{ route('standings.show', $leagueId) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 hover:scale-105">
                                                    <span>BXH</span>
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 px-3 py-1.5">Lịch</span>
                                                <span class="text-xs text-gray-400 px-3 py-1.5">KQ</span>
                                                <span class="text-xs text-gray-400 px-3 py-1.5">BXH</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- Matches List --}}
                                    <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
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
                                        
                                        <div class="px-3 sm:px-4 py-4 border-b border-slate-700/50 hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 {{ $matchId ? 'cursor-pointer group' : '' }}"
                                             @if($matchId) onclick="window.location.href='{{ route('match.detail', $matchId) }}'" @endif>
                                            {{-- Mobile Layout --}}
                                            <div class="flex flex-col sm:hidden space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-2">
                                                        @if($isLive)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-[10px] font-bold animate-pulse">LIVE</span>
                                                        @endif
                                                        <span class="text-xs {{ $shouldBlink ? 'live-minute-blink font-bold' : 'text-gray-300 font-semibold' }}">{{ $timeDisplay }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        @if($matchId)
                                                            <a href="{{ route('match.detail', $matchId) }}" 
                                                               class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25">
                                                                {{ $currentScore }}
                                                            </a>
                                                        @else
                                                            <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg shadow-emerald-500/25">
                                                                {{ $currentScore }}
                                                            </div>
                                                        @endif
                                                        <div class="bg-slate-700 text-white text-xs font-medium px-2 py-1.5 rounded-lg border border-slate-600/50">
                                                            {{ $htScore }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between text-xs">
                                                    <div class="flex-1 text-right pr-2 truncate font-medium text-white group-hover:text-emerald-400 transition-colors">{{ $homeTeam }}</div>
                                                    <div class="flex-1 text-left pl-2 truncate font-medium text-white group-hover:text-emerald-400 transition-colors">{{ $awayTeam }}</div>
                                                </div>
                                            </div>
                                            
                                            {{-- Desktop Layout --}}
                                            <div class="hidden sm:flex items-center gap-4">
                                                {{-- Left: Time and Play Icon --}}
                                                <div class="flex items-center gap-2 w-24 flex-shrink-0">
                                                    @if($isLive)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-[10px] font-bold animate-pulse">LIVE</span>
                                                    @endif
                                                    <span class="text-sm {{ $shouldBlink ? 'live-minute-blink font-bold' : 'text-gray-300 font-semibold' }}">{{ $timeDisplay }}</span>
                                                </div>
                                                
                                                {{-- Home Team --}}
                                                <div class="flex-1 text-sm text-white text-right pr-4 truncate font-medium group-hover:text-emerald-400 transition-colors">
                                                    {{ $homeTeam }}
                                                </div>
                                                
                                                {{-- Score --}}
                                                @if($matchId)
                                                    <a href="{{ route('match.detail', $matchId) }}" 
                                                       class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-base font-black px-4 py-2 rounded-lg mx-4 min-w-[60px] text-center transition-all duration-200 flex-shrink-0 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                                        {{ $currentScore }}
                                                    </a>
                                                @else
                                                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-base font-black px-4 py-2 rounded-lg mx-4 min-w-[60px] text-center flex-shrink-0 shadow-lg shadow-emerald-500/25">
                                                        {{ $currentScore }}
                                                    </div>
                                                @endif
                                                
                                                {{-- Away Team --}}
                                                <div class="flex-1 text-sm text-white text-left pl-4 truncate font-medium group-hover:text-emerald-400 transition-colors">
                                                    {{ $awayTeam }}
                                                </div>
                                                
                                                {{-- HT Score --}}
                                                <div class="bg-slate-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg ml-4 min-w-[50px] text-center flex-shrink-0 border border-slate-600/50">
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
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4 order-2 lg:order-2">
                <x-football-schedule-menu activeItem="Ngoại Hạng Anh" />
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
                <div class="text-center py-12 sm:py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400 text-sm sm:text-base font-medium">Không có trận đấu nào đang diễn ra</p>
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
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 mb-4 p-4 bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-red-500/20 text-red-400 text-xs font-semibold animate-pulse">LIVE</span>
                            <h2 class="text-base sm:text-lg font-bold text-white truncate">
                                <span class="truncate block">${countryName ? countryName + ': ' : ''}${leagueName}
                                    ${matches.length > 0 ? `<span class="text-red-400 text-xs sm:text-sm font-normal ml-2">(${matches.length})</span>` : ''}
                                </span>
                            </h2>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            ${(leagueId && leagueId !== 'unknown' && leagueId !== null && !isNaN(leagueId)) ? `
                                <a href="${scheduleLeagueBaseUrl}/${leagueId}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-105">
                                    <span>Lịch</span>
                                </a>
                                <a href="${resultsLeagueBaseUrl}/${leagueId}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                    <span>KQ</span>
                                </a>
                                <a href="${standingsShowBaseUrl}/${leagueId}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 hover:scale-105">
                                    <span>BXH</span>
                                </a>
                            ` : `
                                <span class="text-xs text-gray-400 px-3 py-1.5">Lịch</span>
                                <span class="text-xs text-gray-400 px-3 py-1.5">KQ</span>
                                <span class="text-xs text-gray-400 px-3 py-1.5">BXH</span>
                            `}
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
            `;
            
            matches.forEach(match => {
                const matchId = match.match_id ?? null;
                const homeTeam = match.home_team ?? '-';
                const awayTeam = match.away_team ?? '-';
                const currentScore = formatScore(match.scores ?? {});
                const htScore = match.scores?.ht_score ?? '0-0';
                const { timeDisplay, shouldBlink } = formatTimeDisplay(match);
                const isLive = match.is_live ?? false;
                
                const timeClass = shouldBlink ? 'live-minute-blink font-bold' : 'text-gray-300 font-semibold';
                const matchDetailUrl = matchId ? `${matchDetailBaseUrl}/${matchId}` : '';
                const onClick = matchId ? `onclick="window.location.href='${matchDetailUrl}'"` : '';
                const cursorClass = matchId ? 'cursor-pointer group' : '';
                
                html += `
                    <div class="px-3 sm:px-4 py-4 border-b border-slate-700/50 hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 ${cursorClass}" ${onClick}>
                        <div class="flex flex-col sm:hidden space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    ${isLive ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-[10px] font-bold animate-pulse">LIVE</span>` : ''}
                                    <span class="text-xs ${timeClass}">${timeDisplay}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    ${matchId ? `
                                        <a href="${matchDetailUrl}" 
                                           class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25">
                                            ${currentScore}
                                        </a>
                                    ` : `
                                        <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg shadow-emerald-500/25">
                                            ${currentScore}
                                        </div>
                                    `}
                                    <div class="bg-slate-700 text-white text-xs font-medium px-2 py-1.5 rounded-lg border border-slate-600/50">
                                        ${htScore}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex-1 text-right pr-2 truncate font-medium text-white group-hover:text-emerald-400 transition-colors">${homeTeam}</div>
                                <div class="flex-1 text-left pl-2 truncate font-medium text-white group-hover:text-emerald-400 transition-colors">${awayTeam}</div>
                            </div>
                        </div>
                        
                        <div class="hidden sm:flex items-center gap-4">
                            <div class="flex items-center gap-2 w-24 flex-shrink-0">
                                ${isLive ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-[10px] font-bold animate-pulse">LIVE</span>` : ''}
                                <span class="text-sm ${timeClass}">${timeDisplay}</span>
                            </div>
                            <div class="flex-1 text-sm text-white text-right pr-4 truncate font-medium group-hover:text-emerald-400 transition-colors">
                                ${homeTeam}
                            </div>
                            ${matchId ? `
                                <a href="${matchDetailUrl}" 
                                   class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-base font-black px-4 py-2 rounded-lg mx-4 min-w-[60px] text-center transition-all duration-200 flex-shrink-0 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                    ${currentScore}
                                </a>
                            ` : `
                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-base font-black px-4 py-2 rounded-lg mx-4 min-w-[60px] text-center flex-shrink-0 shadow-lg shadow-emerald-500/25">
                                    ${currentScore}
                                </div>
                            `}
                            <div class="flex-1 text-sm text-white text-left pl-4 truncate font-medium group-hover:text-emerald-400 transition-colors">
                                ${awayTeam}
                            </div>
                            <div class="bg-slate-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg ml-4 min-w-[50px] text-center flex-shrink-0 border border-slate-600/50">
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
    
    // Initial load
    updateLivescore();
    
    // Auto-refresh: update every 1 minute (60 seconds) - even when tab is hidden
    setInterval(updateLivescore, 60000); // 60000ms = 60 seconds (1 minute)
});
</script>
@endsection

