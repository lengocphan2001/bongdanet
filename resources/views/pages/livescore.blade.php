@extends('layouts.app')

@section('title', 'keobong88 - Livescore')

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
        ['label' => 'keobong88', 'url' => route('home')],
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
                                @php
                                    $leagueKeyId = 'live-' . ($leagueId ?? str_replace(['|', ' '], ['-', ''], $leagueKey));
                                @endphp
                                <div class="mb-6 sm:mb-8">
                                    <div class="flex items-center gap-2 sm:gap-3 mb-2 p-1 sm:p-2 bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                                        <button onclick="toggleLeagueTable('{{ $leagueKeyId }}')" 
                                                class="flex-shrink-0 p-1.5 sm:p-2 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 rounded-lg transition-all duration-200 group"
                                                aria-label="Toggle table">
                                            <svg id="toggle-icon-{{ $leagueKeyId }}" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    
                                    {{-- Matches List --}}
                                    <div id="{{ $leagueKeyId }}" class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
                                    @foreach($matches as $match)
                                        @php
                                            $matchId = $match['match_id'] ?? null;
                                            $homeTeam = $match['home_team'] ?? '-';
                                            $awayTeam = $match['away_team'] ?? '-';
                                            
                                            // Get team logos
                                            $homeLogo = $match['home_team_info']['img'] ?? null;
                                            $awayLogo = $match['away_team_info']['img'] ?? null;
                                            
                                            // Get FT score (full_time) - prioritize this
                                            $ftScore = $match['full_time'] ?? null;
                                            
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
                                            
                                            // Use FT score if available, otherwise use current score
                                            $displayScore = $ftScore ? $ftScore : $currentScore;
                                            
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
                                                        <div class="bg-slate-700 text-white text-xs font-medium px-2 py-1.5 rounded-lg border border-slate-600/50">
                                                            {{ $htScore }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between gap-2">
                                                    {{-- Home Team with Logo --}}
                                                    <div class="flex items-center gap-2 flex-1 min-w-0 justify-end">
                                                        <span class="text-xs font-medium text-white truncate group-hover:text-emerald-400 transition-colors">{{ $homeTeam }}</span>
                                                        @if($homeLogo)
                                                            <div class="w-5 h-5 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                                <img src="{{ $homeLogo }}" alt="{{ $homeTeam }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[9px] text-white font-bold\'>{{ substr($homeTeam, 0, 1) }}</div>';">
                                                            </div>
                                                        @else
                                                            <div class="w-5 h-5 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[9px] text-white font-bold flex-shrink-0">{{ substr($homeTeam, 0, 1) }}</div>
                                                        @endif
                                                    </div>
                                                    
                                                    {{-- FT Score --}}
                                                    @if($matchId)
                                                        <a href="{{ route('match.detail', $matchId) }}" 
                                                           class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-sm font-black px-3 py-1.5 rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25 flex-shrink-0 min-w-[50px] text-center">
                                                            {{ $displayScore }}
                                                        </a>
                                                    @else
                                                        <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-black px-3 py-1.5 rounded-lg shadow-lg shadow-emerald-500/25 flex-shrink-0 min-w-[50px] text-center">
                                                            {{ $displayScore }}
                                                        </div>
                                                    @endif
                                                    
                                                    {{-- Away Team with Logo --}}
                                                    <div class="flex items-center gap-2 flex-1 min-w-0 justify-start">
                                                        @if($awayLogo)
                                                            <div class="w-5 h-5 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                                <img src="{{ $awayLogo }}" alt="{{ $awayTeam }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[9px] text-white font-bold\'>{{ substr($awayTeam, 0, 1) }}</div>';">
                                                            </div>
                                                        @else
                                                            <div class="w-5 h-5 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[9px] text-white font-bold flex-shrink-0">{{ substr($awayTeam, 0, 1) }}</div>
                                                        @endif
                                                        <span class="text-xs font-medium text-white truncate group-hover:text-emerald-400 transition-colors">{{ $awayTeam }}</span>
                                                    </div>
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
                                                
                                                {{-- Home Team with Logo --}}
                                                <div class="flex-1 flex items-center justify-end gap-2 pr-4 min-w-0">
                                                    <span class="text-sm text-white truncate font-medium group-hover:text-emerald-400 transition-colors">{{ $homeTeam }}</span>
                                                    @if($homeLogo)
                                                        <div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                            <img src="{{ $homeLogo }}" alt="{{ $homeTeam }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\'>{{ substr($homeTeam, 0, 1) }}</div>';">
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">{{ substr($homeTeam, 0, 1) }}</div>
                                                    @endif
                                                </div>
                                                
                                                {{-- FT Score --}}
                                                @if($matchId)
                                                    <a href="{{ route('match.detail', $matchId) }}" 
                                                       class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-base font-black px-4 py-2 rounded-lg mx-4 min-w-[60px] text-center transition-all duration-200 flex-shrink-0 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                                        {{ $displayScore }}
                                                    </a>
                                                @else
                                                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-base font-black px-4 py-2 rounded-lg mx-4 min-w-[60px] text-center flex-shrink-0 shadow-lg shadow-emerald-500/25">
                                                        {{ $displayScore }}
                                                    </div>
                                                @endif
                                                
                                                {{-- Away Team with Logo --}}
                                                <div class="flex-1 flex items-center justify-start gap-2 pl-4 min-w-0">
                                                    @if($awayLogo)
                                                        <div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                                            <img src="{{ $awayLogo }}" alt="{{ $awayTeam }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\'>{{ substr($awayTeam, 0, 1) }}</div>';">
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">{{ substr($awayTeam, 0, 1) }}</div>
                                                    @endif
                                                    <span class="text-sm text-white truncate font-medium group-hover:text-emerald-400 transition-colors">{{ $awayTeam }}</span>
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
// Toggle league table function - same as home-matches-table
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

// Make it globally available
if (typeof window !== 'undefined') {
    window.toggleLeagueTable = toggleLeagueTable;
}

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
            
            const leagueKeyId = 'live-' + (leagueId || leagueKey.replace(/[| ]/g, '-'));
            
            html += `
                <div class="mb-6 sm:mb-8">
                    <div class="flex items-center gap-2 sm:gap-3 mb-2 p-1 sm:p-2 bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                        <button onclick="toggleLeagueTable('${leagueKeyId}')" 
                                class="flex-shrink-0 p-1.5 sm:p-2 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 rounded-lg transition-all duration-200 group"
                                aria-label="Toggle table">
                            <svg id="toggle-icon-${leagueKeyId}" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md bg-red-500/20 text-red-400 text-[10px] sm:text-xs font-semibold animate-pulse flex-shrink-0">LIVE</span>
                        <h2 class="flex text-sm sm:text-base md:text-lg font-bold text-white overflow-hidden text-ellipsis whitespace-nowrap flex-1 min-w-0">
                            <span class="inline-block truncate">
                                ${countryName ? countryName + ': ' : ''}${leagueName}
                                ${matches.length > 0 ? `<span class="text-emerald-400 text-[10px] sm:text-xs md:text-sm font-normal ml-1 sm:ml-2">(${matches.length})</span>` : ''}
                            </span>
                        </h2>
                        ${(leagueId && leagueId !== 'unknown' && leagueId !== null && !isNaN(leagueId)) ? `
                            <a href="${standingsShowBaseUrl}/${leagueId}" 
                               class="hidden sm:inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-[10px] sm:text-xs text-white rounded-lg transition-all duration-200 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 hover:scale-105 flex-shrink-0">
                                <span>BXH</span>
                            </a>
                        ` : ''}
                    </div>
                    <div id="${leagueKeyId}" class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl overflow-hidden border border-slate-700/50 shadow-xl backdrop-blur-sm">
            `;
            
            matches.forEach(match => {
                const matchId = match.match_id ?? null;
                const homeTeam = match.home_team ?? '-';
                const awayTeam = match.away_team ?? '-';
                const homeLogo = match.home_team_info?.img ?? null;
                const awayLogo = match.away_team_info?.img ?? null;
                const ftScore = match.full_time ?? null;
                const currentScore = formatScore(match.scores ?? {});
                const displayScore = ftScore ? ftScore : currentScore;
                const htScore = match.scores?.ht_score ?? '0-0';
                const { timeDisplay, shouldBlink } = formatTimeDisplay(match);
                const isLive = match.is_live ?? false;
                
                const timeClass = shouldBlink ? 'live-minute-blink font-bold' : 'text-gray-300 font-semibold';
                const matchDetailUrl = matchId ? `${matchDetailBaseUrl}/${matchId}` : '';
                const onClick = matchId ? `onclick="window.location.href='${matchDetailUrl}'"` : '';
                const cursorClass = matchId ? 'cursor-pointer group' : '';
                
                const homeLogoHtml = homeLogo ? 
                    `<div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                        <img src="${homeLogo}" alt="${homeTeam}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\'>${homeTeam.charAt(0)}</div>';">
                    </div>` :
                    `<div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">${homeTeam.charAt(0)}</div>`;
                
                const awayLogoHtml = awayLogo ? 
                    `<div class="w-6 h-6 rounded bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                        <img src="${awayLogo}" alt="${awayTeam}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-slate-600 to-slate-700 rounded flex items-center justify-center text-[10px] text-white font-bold\'>${awayTeam.charAt(0)}</div>';">
                    </div>` :
                    `<div class="w-6 h-6 rounded bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0">${awayTeam.charAt(0)}</div>`;
                
                html += `
                    <div class="px-3 sm:px-4 py-4 border-b border-slate-700/50 hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 ${cursorClass}" ${onClick}>
                        <div class="flex flex-col sm:hidden space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    ${isLive ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-[10px] font-bold animate-pulse">LIVE</span>` : ''}
                                    <span class="text-xs ${timeClass}">${timeDisplay}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="bg-slate-700 text-white text-xs font-medium px-2 py-1.5 rounded-lg border border-slate-600/50">
                                        ${htScore}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 flex-1 min-w-0 justify-end">
                                    <span class="text-xs font-medium text-white truncate group-hover:text-emerald-400 transition-colors">${homeTeam}</span>
                                    ${homeLogoHtml}
                                </div>
                                ${matchId ? `
                                    <a href="${matchDetailUrl}" 
                                       class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-sm font-black px-3 py-1.5 rounded-lg transition-all duration-200 shadow-lg shadow-emerald-500/25 flex-shrink-0 min-w-[50px] text-center">
                                        ${displayScore}
                                    </a>
                                ` : `
                                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-black px-3 py-1.5 rounded-lg shadow-lg shadow-emerald-500/25 flex-shrink-0 min-w-[50px] text-center">
                                        ${displayScore}
                                    </div>
                                `}
                                <div class="flex items-center gap-2 flex-1 min-w-0 justify-start">
                                    ${awayLogoHtml}
                                    <span class="text-xs font-medium text-white truncate group-hover:text-emerald-400 transition-colors">${awayTeam}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hidden sm:flex items-center gap-4">
                            <div class="flex items-center gap-2 w-24 flex-shrink-0">
                                ${isLive ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-[10px] font-bold animate-pulse">LIVE</span>` : ''}
                                <span class="text-sm ${timeClass}">${timeDisplay}</span>
                            </div>
                            <div class="flex-1 flex items-center justify-end gap-2 pr-4 min-w-0">
                                <span class="text-sm text-white truncate font-medium group-hover:text-emerald-400 transition-colors">${homeTeam}</span>
                                ${homeLogoHtml}
                            </div>
                            ${matchId ? `
                                <a href="${matchDetailUrl}" 
                                   class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white text-base font-black px-4 py-2 rounded-lg mx-4 min-w-[60px] text-center transition-all duration-200 flex-shrink-0 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                                    ${displayScore}
                                </a>
                            ` : `
                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-base font-black px-4 py-2 rounded-lg mx-4 min-w-[60px] text-center flex-shrink-0 shadow-lg shadow-emerald-500/25">
                                    ${displayScore}
                                </div>
                            `}
                            <div class="flex-1 flex items-center justify-start gap-2 pl-4 min-w-0">
                                ${awayLogoHtml}
                                <span class="text-sm text-white truncate font-medium group-hover:text-emerald-400 transition-colors">${awayTeam}</span>
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

