{{-- Match Detail Modal --}}
<div id="match-detail-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-slate-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col my-8">
        {{-- Modal Header --}}
        <div class="bg-slate-900 px-4 py-3 flex items-center justify-between border-b border-slate-700">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                {{-- Status Indicator --}}
                <div id="modal-status-indicator" class="flex items-center gap-2 flex-shrink-0">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <span class="text-xs text-gray-300" id="modal-status-text">Chưa bắt đầu</span>
                </div>
                
                {{-- League Info --}}
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <div id="modal-league-icon" class="w-5 h-5 bg-slate-700 rounded flex-shrink-0 flex items-center justify-center overflow-hidden">
                        <span class="text-white text-xs" id="modal-league-icon-text">L</span>
                    </div>
                    <span class="text-sm text-white truncate" id="modal-league-name">League Name</span>
                </div>
            </div>
            
            {{-- Close Button --}}
            <button onclick="closeMatchModal()" class="text-gray-400 hover:text-white transition-colors flex-shrink-0 ml-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        {{-- Match Info Section --}}
        <div class="px-4 py-6 bg-slate-800">
            <div class="text-center mb-4">
                <div class="text-sm text-gray-400 mb-4" id="modal-match-datetime">20/11/2025 lúc 15:30</div>
                
                {{-- Teams and Score --}}
                <div class="flex items-center justify-center gap-6 mb-4">
                    {{-- Home Team --}}
                    <div class="flex flex-col items-center flex-1">
                        <div id="modal-home-logo" class="w-16 h-16 bg-slate-700 rounded-full mb-2 flex items-center justify-center">
                            <span class="text-white text-xs">Logo</span>
                        </div>
                        <div class="text-white font-medium text-center" id="modal-home-team">Team 1</div>
                    </div>
                    
                    {{-- Score --}}
                    <div class="flex flex-col items-center">
                        <div class="text-3xl font-bold text-white mb-2" id="modal-score">--- ---</div>
                        <div class="text-sm text-gray-400">Kết quả hiệp 1</div>
                        <div class="text-lg text-gray-300" id="modal-ht-score">---</div>
                    </div>
                    
                    {{-- Away Team --}}
                    <div class="flex flex-col items-center flex-1">
                        <div id="modal-away-logo" class="w-16 h-16 bg-slate-700 rounded-full mb-2 flex items-center justify-center">
                            <span class="text-white text-xs">Logo</span>
                        </div>
                        <div class="text-white font-medium text-center" id="modal-away-team">Team 2</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Navigation Tabs --}}
        <div class="bg-slate-900 border-b border-slate-700 px-4">
            <div class="flex items-center gap-1 overflow-x-auto">
                <button onclick="switchModalTab('overview')" class="modal-tab px-4 py-2 text-sm font-medium text-white border-b-2 border-green-500 transition-colors whitespace-nowrap" data-tab="overview">
                    TỔNG QUAN
                </button>
                <button onclick="switchModalTab('stats')" class="modal-tab px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition-colors whitespace-nowrap" data-tab="stats">
                    THỐNG KÊ
                </button>
                <button onclick="switchModalTab('odds')" class="modal-tab px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition-colors whitespace-nowrap" data-tab="odds">
                    ODDS
                </button>
                <button onclick="switchModalTab('h2h')" class="modal-tab px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition-colors whitespace-nowrap" data-tab="h2h">
                    H2H
                </button>
                <button onclick="switchModalTab('standings')" class="modal-tab px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition-colors whitespace-nowrap" data-tab="standings">
                    BXH
                </button>
            </div>
        </div>
        
        {{-- Tab Content --}}
        <div class="flex-1 overflow-y-auto p-4">
            {{-- Overview Tab --}}
            <div id="modal-tab-overview" class="modal-tab-content">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-white mb-4">THÔNG TIN TRẬN ĐẤU</h3>
                </div>
                
                {{-- Match Events will be loaded here --}}
                <div id="modal-match-events">
                    <div class="text-center text-gray-400 py-8">Đang tải dữ liệu...</div>
                </div>
            </div>
            
            {{-- Other Tabs --}}
            <div id="modal-tab-stats" class="modal-tab-content hidden">
                <div class="text-center text-gray-400 py-8">Nội dung THỐNG KÊ</div>
            </div>
            
            <div id="modal-tab-odds" class="modal-tab-content hidden">
                <div class="text-center text-gray-400 py-8">Nội dung ODDS</div>
            </div>
            
            <div id="modal-tab-h2h" class="modal-tab-content hidden">
                <div id="h2h-content" class="space-y-6">
                    <div class="text-center text-gray-400 py-8">Đang tải dữ liệu H2H...</div>
                </div>
            </div>
            
            <div id="modal-tab-standings" class="modal-tab-content hidden">
                <div class="text-center text-gray-400 py-8">Nội dung BXH</div>
            </div>
        </div>
    </div>
</div>

<script>
let currentMatchId = null;
let matchEventsData = null;
let matchOddsPrematch = null;
let matchStatsData = null;
let matchH2HData = null;
let cachedTeamNames = { home: null, away: null };

async function openMatchModal(matchId) {
    currentMatchId = matchId;
    
    // Show loading overlay
    showModalLoading();
    
    // Clear old data and reset UI
    clearModalData();
    
    // Reset to overview tab
    switchModalTab('overview');
    
    try {
        // Load all data in one optimized API call
        await loadAllMatchData(matchId);
        
        // Hide loading and show modal
        hideModalLoading();
        const modal = document.getElementById('match-detail-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        console.error('Error loading match data:', error);
        hideModalLoading();
        alert('Có lỗi xảy ra khi tải dữ liệu trận đấu');
    }
}

async function loadAllMatchData(matchId) {
    try {
        // Use optimized endpoint that returns all data in one call
        // Add cache busting and no-cache headers for fresh data
        const cacheBuster = Date.now();
        const response = await fetch(`/api/match-all/${matchId}?t=${cacheBuster}`, {
            method: 'GET',
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0',
            },
            cache: 'no-store'
        });
        const data = await response.json();
        
        if (!data.success || !data.data) {
            throw new Error('Failed to load match data');
        }
        
        const allData = data.data;
        
        // Process match detail
        if (allData.match_detail) {
            processMatchDetail(allData.match_detail);
        }
        
        // Process events, odds, and stats
        if (allData.events !== undefined) {
            matchEventsData = Array.isArray(allData.events) ? allData.events : null;
        }
        matchOddsPrematch = allData.odds_prematch || null;
        matchStatsData = allData.stats || null;
        matchTeamIds = {
            home_team_id: allData.home_team_id || null,
            away_team_id: allData.away_team_id || null,
        };
        
        // Process H2H data
        if (allData.h2h) {
            matchH2HData = {
                h2hData: allData.h2h,
                currentHomeTeamId: allData.home_team_id || null,
                currentAwayTeamId: allData.away_team_id || null,
            };
        }
        
        // Render overview tab (events)
        renderMatchEvents();
        
    } catch (error) {
        console.error('Error loading all match data:', error);
        // Fallback to separate API calls if optimized endpoint fails
        await Promise.all([
            loadMatchDetail(matchId),
            loadMatchEventsAndOdds(matchId),
            loadH2HData(matchId)
        ]);
    }
}

function processMatchDetail(match) {
    // Update header
    const statusName = match.status_name || match.status?.name || 'Chưa bắt đầu';
    document.getElementById('modal-status-text').textContent = statusName;
    
    // Update status indicator
    const statusIndicator = document.getElementById('modal-status-indicator');
    if (match.status_name === 'LIVE' || match.status?.name === 'LIVE') {
        statusIndicator.querySelector('.w-2').classList.remove('bg-red-500');
        statusIndicator.querySelector('.w-2').classList.add('bg-red-500', 'animate-pulse');
    } else {
        statusIndicator.querySelector('.w-2').classList.remove('bg-red-500', 'animate-pulse');
        statusIndicator.querySelector('.w-2').classList.add('bg-gray-500');
    }
    
    document.getElementById('modal-league-name').textContent = match.league?.name || 'Unknown League';
    
    // Update league logo
    const leagueIconEl = document.getElementById('modal-league-icon');
    const leagueIconText = document.getElementById('modal-league-icon-text');
    const league = match.league || {};
    if (league.img || league.logo) {
        const leagueImg = league.img || league.logo;
        leagueIconEl.innerHTML = `<img src="${leagueImg}" alt="${league.name || 'League'}" class="w-full h-full object-contain" onerror="this.parentElement.innerHTML='<span class=\\'text-white text-xs\\'>${(league.name || 'L').charAt(0)}</span>'">`;
    } else if (league.name) {
        if (leagueIconText) {
            leagueIconText.textContent = league.name.charAt(0).toUpperCase();
        } else {
            leagueIconEl.innerHTML = `<span class="text-white text-xs">${league.name.charAt(0).toUpperCase()}</span>`;
        }
    }
    
    // Update match info - parse datetime
    let dateStr = '';
    let timeStr = '';
    if (match.time?.datetime) {
        const datetime = new Date(match.time.datetime);
        dateStr = datetime.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        timeStr = datetime.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }
    document.getElementById('modal-match-datetime').textContent = dateStr && timeStr ? `${dateStr} lúc ${timeStr}` : '';
    
    // Update teams
    const homeTeam = match.teams?.home || {};
    const awayTeam = match.teams?.away || {};
    document.getElementById('modal-home-team').textContent = homeTeam.name || 'N/A';
    document.getElementById('modal-away-team').textContent = awayTeam.name || 'N/A';
    
    // Cache team names for stats tab (no API call needed)
    cachedTeamNames.home = homeTeam.name || 'Đội nhà';
    cachedTeamNames.away = awayTeam.name || 'Đội khách';
    
    // Update logos
    const homeLogoEl = document.getElementById('modal-home-logo');
    if (homeTeam.img) {
        homeLogoEl.innerHTML = `<img src="${homeTeam.img}" alt="${homeTeam.name}" class="w-full h-full rounded-full object-contain" onerror="this.parentElement.innerHTML='<span class=\\'text-white text-xs\\'>${(homeTeam.name || 'N/A').charAt(0)}</span>'">`;
    } else {
        homeLogoEl.innerHTML = `<span class="text-white text-xs">${(homeTeam.name || 'N/A').charAt(0)}</span>`;
    }
    
    const awayLogoEl = document.getElementById('modal-away-logo');
    if (awayTeam.img) {
        awayLogoEl.innerHTML = `<img src="${awayTeam.img}" alt="${awayTeam.name}" class="w-full h-full rounded-full object-contain" onerror="this.parentElement.innerHTML='<span class=\\'text-white text-xs\\'>${(awayTeam.name || 'N/A').charAt(0)}</span>'">`;
    } else {
        awayLogoEl.innerHTML = `<span class="text-white text-xs">${(awayTeam.name || 'N/A').charAt(0)}</span>`;
    }
    
    // Update scores - handle multiple possible score structures
    const scores = match.scores || match.score || {};
    
    // Try to get full time score from different possible structures
    let homeScore = null;
    let awayScore = null;
    let htHomeScore = null;
    let htAwayScore = null;
    
    // Method 1: Check ft_score (full time score as string "1-0")
    if (scores.ft_score) {
        const ftParts = scores.ft_score.split('-');
        if (ftParts.length === 2) {
            homeScore = parseInt(ftParts[0].trim());
            awayScore = parseInt(ftParts[1].trim());
        }
    }
    
    // Method 2: Check home_score and away_score directly
    if (homeScore === null || awayScore === null) {
        homeScore = scores.home_score ?? scores.home ?? null;
        awayScore = scores.away_score ?? scores.away ?? null;
    }
    
    // Method 3: Check score.fulltime structure
    if (homeScore === null || awayScore === null) {
        const fulltimeScore = scores.fulltime || {};
        homeScore = fulltimeScore.home ?? homeScore;
        awayScore = fulltimeScore.away ?? awayScore;
    }
    
    // Method 4: Check score object directly
    if (homeScore === null || awayScore === null) {
        const scoreObj = match.score || {};
        homeScore = scoreObj.home ?? homeScore;
        awayScore = scoreObj.away ?? awayScore;
    }
    
    // Get half time score
    if (scores.ht_score) {
        const htParts = scores.ht_score.split('-');
        if (htParts.length === 2) {
            htHomeScore = parseInt(htParts[0].trim());
            htAwayScore = parseInt(htParts[1].trim());
        }
    } else {
        const halftimeScore = scores.halftime || {};
        htHomeScore = halftimeScore.home ?? scores.ht_home ?? null;
        htAwayScore = halftimeScore.away ?? scores.ht_away ?? null;
    }
    
    // Display full time score
    if (homeScore !== null && awayScore !== null && !isNaN(homeScore) && !isNaN(awayScore)) {
        document.getElementById('modal-score').textContent = `${homeScore} - ${awayScore}`;
    } else {
        document.getElementById('modal-score').textContent = '--- ---';
    }
    
    // Display half time score
    if (htHomeScore !== null && htAwayScore !== null && !isNaN(htHomeScore) && !isNaN(htAwayScore)) {
        document.getElementById('modal-ht-score').textContent = `${htHomeScore} - ${htAwayScore}`;
    } else {
        document.getElementById('modal-ht-score').textContent = '---';
    }
}

function showModalLoading() {
    let loadingOverlay = document.getElementById('modal-loading-overlay');
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'modal-loading-overlay';
        loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center';
        loadingOverlay.innerHTML = `
            <div class="bg-slate-800 rounded-lg p-8 flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
                <p class="text-white text-sm">Đang tải dữ liệu...</p>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    }
    loadingOverlay.classList.remove('hidden');
}

function hideModalLoading() {
    const loadingOverlay = document.getElementById('modal-loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.classList.add('hidden');
    }
}

function clearModalData() {
    // Clear header data
    document.getElementById('modal-status-text').textContent = 'Chưa bắt đầu';
    document.getElementById('modal-league-name').textContent = 'League Name';
    document.getElementById('modal-match-datetime').textContent = '';
    
    // Clear league icon
    const leagueIconEl = document.getElementById('modal-league-icon');
    const leagueIconText = document.getElementById('modal-league-icon-text');
    if (leagueIconText) {
        leagueIconText.textContent = 'L';
    } else {
        leagueIconEl.innerHTML = '<span class="text-white text-xs" id="modal-league-icon-text">L</span>';
    }
    
    // Clear team data
    document.getElementById('modal-home-team').textContent = 'Team 1';
    document.getElementById('modal-away-team').textContent = 'Team 2';
    document.getElementById('modal-score').textContent = '--- ---';
    document.getElementById('modal-ht-score').textContent = '---';
    
    // Clear team logos
    const homeLogoEl = document.getElementById('modal-home-logo');
    const awayLogoEl = document.getElementById('modal-away-logo');
    homeLogoEl.innerHTML = '<span class="text-white text-xs">Logo</span>';
    awayLogoEl.innerHTML = '<span class="text-white text-xs">Logo</span>';
    
    // Clear events
    const eventsContainer = document.getElementById('modal-match-events');
    if (eventsContainer) {
        eventsContainer.innerHTML = '<div class="text-center text-gray-400 py-4">Đang tải...</div>';
    }
    
    // Clear H2H data
    const h2hContainer = document.getElementById('h2h-content');
    if (h2hContainer) {
        h2hContainer.innerHTML = '<div class="text-center text-gray-400 py-8">Đang tải dữ liệu H2H...</div>';
    }
    
    // Clear cached data
    matchEventsData = null;
    matchOddsPrematch = null;
    matchStatsData = null;
    matchTeamIds = { home_team_id: null, away_team_id: null };
    matchH2HData = null;
    cachedTeamNames = { home: null, away: null };
    
    // Clear other tab contents
    const statsTab = document.getElementById('modal-tab-stats');
    if (statsTab) {
        statsTab.innerHTML = '<div class="text-center text-gray-400 py-8">Nội dung THỐNG KÊ</div>';
    }
    
    const oddsTab = document.getElementById('modal-tab-odds');
    if (oddsTab) {
        oddsTab.innerHTML = '<div class="text-center text-gray-400 py-8">Nội dung ODDS</div>';
    }
    
    const standingsTab = document.getElementById('modal-tab-standings');
    if (standingsTab) {
        standingsTab.innerHTML = '<div class="text-center text-gray-400 py-8">Nội dung BXH</div>';
    }
}

function closeMatchModal() {
    const modal = document.getElementById('match-detail-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
    currentMatchId = null;
}

function switchModalTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.modal-tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.modal-tab').forEach(tab => {
        tab.classList.remove('text-white', 'border-green-500', 'border-b-2');
        tab.classList.add('text-gray-400');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(`modal-tab-${tabName}`);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    // Activate selected tab
    const selectedTab = document.querySelector(`[data-tab="${tabName}"]`);
    if (selectedTab) {
        selectedTab.classList.remove('text-gray-400');
        selectedTab.classList.add('text-white', 'border-green-500', 'border-b-2');
    }
    
    // Load tab data if needed
    if (tabName === 'overview') {
        // Use cached events data if available
        renderMatchEvents();
    } else if (tabName === 'odds') {
        // Use cached odds data if available
        renderMatchOdds();
    } else if (tabName === 'stats') {
        // Use cached stats data if available
        renderMatchStats();
    } else if (tabName === 'h2h') {
        // Use cached H2H data if available, otherwise load it
        if (matchH2HData) {
            renderH2HData();
        } else if (currentMatchId) {
            loadH2HData(currentMatchId);
        }
    }
}

async function loadMatchDetail(matchId) {
    try {
        const response = await fetch(`/api/match-detail/${matchId}`);
        const data = await response.json();
        
        if (data.success && data.data) {
            const match = data.data;
            
            // Update header
            const statusName = match.status_name || match.status?.name || 'Chưa bắt đầu';
            document.getElementById('modal-status-text').textContent = statusName;
            
            // Update status indicator
            const statusIndicator = document.getElementById('modal-status-indicator');
            if (match.status_name === 'LIVE' || match.status?.name === 'LIVE') {
                statusIndicator.querySelector('.w-2').classList.remove('bg-red-500');
                statusIndicator.querySelector('.w-2').classList.add('bg-red-500', 'animate-pulse');
            } else {
                statusIndicator.querySelector('.w-2').classList.remove('bg-red-500', 'animate-pulse');
                statusIndicator.querySelector('.w-2').classList.add('bg-gray-500');
            }
            
            document.getElementById('modal-league-name').textContent = match.league?.name || 'Unknown League';
            
            // Update league logo
            const leagueIconEl = document.getElementById('modal-league-icon');
            const leagueIconText = document.getElementById('modal-league-icon-text');
            const league = match.league || {};
            if (league.img || league.logo) {
                const leagueImg = league.img || league.logo;
                leagueIconEl.innerHTML = `<img src="${leagueImg}" alt="${league.name || 'League'}" class="w-full h-full object-contain" onerror="this.parentElement.innerHTML='<span class=\\'text-white text-xs\\'>${(league.name || 'L').charAt(0)}</span>'">`;
            } else if (league.name) {
                if (leagueIconText) {
                    leagueIconText.textContent = league.name.charAt(0).toUpperCase();
                } else {
                    leagueIconEl.innerHTML = `<span class="text-white text-xs">${league.name.charAt(0).toUpperCase()}</span>`;
                }
            }
            
            // Update match info - parse datetime
            let dateStr = '';
            let timeStr = '';
            if (match.time?.datetime) {
                const datetime = new Date(match.time.datetime);
                dateStr = datetime.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
                timeStr = datetime.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            } else if (match.date && match.time) {
                // Fallback: combine date and time strings
                dateStr = match.date;
                timeStr = match.time;
            }
            document.getElementById('modal-match-datetime').textContent = dateStr && timeStr ? `${dateStr} lúc ${timeStr}` : '';
            
            // Update teams
            const homeTeam = match.teams?.home || {};
            const awayTeam = match.teams?.away || {};
            document.getElementById('modal-home-team').textContent = homeTeam.name || 'N/A';
            document.getElementById('modal-away-team').textContent = awayTeam.name || 'N/A';
            
            // Cache team names for stats tab (no API call needed)
            cachedTeamNames.home = homeTeam.name || 'Đội nhà';
            cachedTeamNames.away = awayTeam.name || 'Đội khách';
            
            // Update logos
            const homeLogoEl = document.getElementById('modal-home-logo');
            if (homeTeam.img) {
                homeLogoEl.innerHTML = `<img src="${homeTeam.img}" alt="${homeTeam.name}" class="w-full h-full rounded-full object-contain" onerror="this.parentElement.innerHTML='<span class=\\'text-white text-xs\\'>${(homeTeam.name || 'N/A').charAt(0)}</span>'">`;
            } else {
                homeLogoEl.innerHTML = `<span class="text-white text-xs">${(homeTeam.name || 'N/A').charAt(0)}</span>`;
            }
            
            const awayLogoEl = document.getElementById('modal-away-logo');
            if (awayTeam.img) {
                awayLogoEl.innerHTML = `<img src="${awayTeam.img}" alt="${awayTeam.name}" class="w-full h-full rounded-full object-contain" onerror="this.parentElement.innerHTML='<span class=\\'text-white text-xs\\'>${(awayTeam.name || 'N/A').charAt(0)}</span>'">`;
            } else {
                awayLogoEl.innerHTML = `<span class="text-white text-xs">${(awayTeam.name || 'N/A').charAt(0)}</span>`;
            }
            
            // Update scores
            const scores = match.scores || {};
            const homeScore = scores.home_score ?? scores.home ?? '';
            const awayScore = scores.away_score ?? scores.away ?? '';
            document.getElementById('modal-score').textContent = `${homeScore !== '' ? homeScore : '?'} - ${awayScore !== '' ? awayScore : '?'}`;
            
            const htScore = scores.ht_score || scores.ht || '0-0';
            document.getElementById('modal-ht-score').textContent = htScore;
            
            // Load events and odds_prematch in one call
            loadMatchEventsAndOdds(matchId);
        }
    } catch (error) {
        console.error('Error loading match detail:', error);
    }
}

let matchTeamIds = { home_team_id: null, away_team_id: null };

async function loadMatchEventsAndOdds(matchId) {
    try {
        const response = await fetch(`/api/match-events/${matchId}`);
        const data = await response.json();
        
        // Store data globally
        // Response structure: { success: true, data: { data: [...], odds_prematch: ..., stats: {...}, home_team_id: ..., away_team_id: ... } }
        if (data.success && data.data) {
            matchEventsData = Array.isArray(data.data.data) ? data.data.data : null;
            matchOddsPrematch = data.data.odds_prematch || null;
            matchStatsData = data.data.stats || null;
            matchTeamIds = {
                home_team_id: data.data.home_team_id || null,
                away_team_id: data.data.away_team_id || null,
            };
        } else {
            matchEventsData = null;
            matchOddsPrematch = null;
            matchStatsData = null;
            matchTeamIds = { home_team_id: null, away_team_id: null };
        }
        
        // Render events if on overview tab
        renderMatchEvents();
        
        // Render odds if on odds tab
        if (document.getElementById('modal-tab-odds') && !document.getElementById('modal-tab-odds').classList.contains('hidden')) {
            renderMatchOdds();
        }
        
        // Render stats if on stats tab
        if (document.getElementById('modal-tab-stats') && !document.getElementById('modal-tab-stats').classList.contains('hidden')) {
            renderMatchStats();
        }
    } catch (error) {
        console.error('Error loading match events and odds:', error);
        matchEventsData = null;
        matchOddsPrematch = null;
        matchStatsData = null;
        matchTeamIds = { home_team_id: null, away_team_id: null };
        const eventsContainer = document.getElementById('modal-match-events');
        if (eventsContainer) {
            eventsContainer.innerHTML = '<div class="text-center text-red-400 py-4">Lỗi tải dữ liệu</div>';
        }
    }
}

function renderMatchEvents() {
    const eventsContainer = document.getElementById('modal-match-events');
    if (!eventsContainer) return;
    
    if (matchEventsData && Array.isArray(matchEventsData) && matchEventsData.length > 0) {
        const homeTeamId = matchTeamIds.home_team_id;
        const awayTeamId = matchTeamIds.away_team_id;
        
        // Filter only relevant events
        const relevantEvents = matchEventsData.filter(event => {
            const type = (event.type || '').toLowerCase();
            return ['goal', 'yellowcard', 'redcard', 'yellowredcard', 'substitution'].includes(type);
        });
        
        if (relevantEvents.length === 0) {
            eventsContainer.innerHTML = '<div class="text-center text-gray-400 py-4">Chưa có sự kiện nào</div>';
            return;
        }
        
        // Sort events by period, then by minute
        relevantEvents.sort((a, b) => {
            const periodA = (a.period || '').toLowerCase();
            const periodB = (b.period || '').toLowerCase();
            
            // First half comes before second half
            if (periodA.includes('1st') || periodA.includes('1')) {
                if (periodB.includes('2nd') || periodB.includes('2')) return -1;
            } else if (periodA.includes('2nd') || periodA.includes('2')) {
                if (periodB.includes('1st') || periodB.includes('1')) return 1;
            }
            
            // Within same period, sort by minute
            const minuteA = parseInt(a.minute || 0) + parseInt(a.extra_minute || 0);
            const minuteB = parseInt(b.minute || 0) + parseInt(b.extra_minute || 0);
            return minuteA - minuteB;
        });
        
        // Group events by period and calculate scores
        const eventsByPeriod = {
            '1st half': [],
            '2nd half': [],
            'other': []
        };
        
        let homeScore = 0;
        let awayScore = 0;
        
        relevantEvents.forEach(event => {
            const period = event.period || 'other';
            const periodKey = period.includes('1st') || period.includes('1') ? '1st half' : 
                           period.includes('2nd') || period.includes('2') ? '2nd half' : 'other';
            
            // Calculate score for goals
            if (event.type === 'goal' && !event.own_goal) {
                if (event.team_id === homeTeamId) {
                    homeScore++;
                } else if (event.team_id === awayTeamId) {
                    awayScore++;
                }
            } else if (event.type === 'goal' && event.own_goal) {
                // Own goal - opposite team gets the goal
                if (event.team_id === homeTeamId) {
                    awayScore++;
                } else if (event.team_id === awayTeamId) {
                    homeScore++;
                }
            }
            
            eventsByPeriod[periodKey].push({
                ...event,
                currentHomeScore: homeScore,
                currentAwayScore: awayScore
            });
        });
        
        // Render events by period
        let html = '';
        
        // First Half
        if (eventsByPeriod['1st half'].length > 0) {
            html += '<div class="mb-6">';
            html += '<h4 class="text-sm font-bold text-white mb-3 flex items-center gap-2">';
            html += '<span class="w-1 h-4 bg-green-500"></span>';
            html += 'HIỆP 1';
            html += '</h4>';
            html += '<div class="space-y-2">';
            
            eventsByPeriod['1st half'].forEach(event => {
                html += renderEventItem(event, homeTeamId, awayTeamId);
            });
            
            html += '</div>';
            html += '</div>';
        }
        
        // Half-time separator
        if (eventsByPeriod['1st half'].length > 0 && eventsByPeriod['2nd half'].length > 0) {
            html += '<div class="text-center py-3 mb-6">';
            html += '<span class="text-sm text-gray-400 italic">Giao bóng giữa sân</span>';
            html += '</div>';
        }
        
        // Second Half
        if (eventsByPeriod['2nd half'].length > 0) {
            html += '<div class="mb-6">';
            html += '<h4 class="text-sm font-bold text-white mb-3 flex items-center gap-2">';
            html += '<span class="w-1 h-4 bg-green-500"></span>';
            html += 'HIỆP 2';
            html += '</h4>';
            html += '<div class="space-y-2">';
            
            eventsByPeriod['2nd half'].forEach(event => {
                html += renderEventItem(event, homeTeamId, awayTeamId);
            });
            
            html += '</div>';
            html += '</div>';
        }
        
        eventsContainer.innerHTML = html;
    } else {
        eventsContainer.innerHTML = '<div class="text-center text-gray-400 py-4">Chưa có sự kiện nào</div>';
    }
}

function renderEventItem(event, homeTeamId, awayTeamId) {
    const minute = event.minute || '';
    const extraMinute = event.extra_minute || '';
    const minuteDisplay = minute + (extraMinute ? '+' + extraMinute : '') + "'";
    const type = (event.type || '').toLowerCase();
    const playerName = event.player_name || '';
    const relatedPlayerName = event.related_player_name || '';
    const isHomeTeam = event.team_id === homeTeamId;
    
    // Determine icon and styling based on event type
    let icon = '⚽';
    let bgColor = 'bg-slate-900';
    let textColor = 'text-white';
    let eventText = '';
    
    if (type === 'goal') {
        icon = '⚽';
        bgColor = 'bg-blue-900';
        const score = `${event.currentHomeScore} - ${event.currentAwayScore}`;
        eventText = event.own_goal ? 
            `${score} (Phản lưới nhà) ${playerName}` : 
            `${score} ${playerName}`;
    } else if (type === 'yellowcard') {
        icon = '🟨';
        bgColor = 'bg-yellow-900';
        eventText = playerName || 'Thẻ vàng';
    } else if (type === 'redcard') {
        icon = '🟥';
        bgColor = 'bg-red-900';
        eventText = playerName || 'Thẻ đỏ';
    } else if (type === 'yellowredcard') {
        icon = '🟨🟥';
        bgColor = 'bg-orange-900';
        eventText = playerName || 'Thẻ vàng thứ 2';
    } else if (type === 'substitution') {
        icon = '↕️';
        bgColor = 'bg-green-900';
        eventText = relatedPlayerName ? `${relatedPlayerName} → ${playerName}` : playerName || 'Thay người';
    } else {
        eventText = playerName || type;
    }
    
    return `
        <div class="flex items-center gap-3 p-3 ${bgColor} rounded border border-slate-700 hover:border-slate-600 transition-colors">
            <span class="text-xs text-gray-300 font-medium min-w-[50px] text-right">${minuteDisplay}</span>
            <span class="text-lg flex-shrink-0">${icon}</span>
            <span class="text-sm ${textColor} flex-1">${eventText}</span>
        </div>
    `;
}

function renderMatchOdds() {
    const oddsContainer = document.getElementById('modal-tab-odds');
    if (!oddsContainer) {
        return;
    }
    
    if (!matchOddsPrematch || typeof matchOddsPrematch !== 'object') {
        oddsContainer.innerHTML = '<div class="text-center text-gray-400 py-8">Không có dữ liệu odds</div>';
        return;
    }
    
    // Find Bet365 odds (bookmaker_id = 2)
    let bet365Odds = null;
    if (Array.isArray(matchOddsPrematch)) {
        bet365Odds = matchOddsPrematch.find(odds => odds.bookmaker_id === 2 || odds.bookmaker?.id === 2);
    } else if (matchOddsPrematch.bookmaker_id === 2 || matchOddsPrematch.bookmaker?.id === 2) {
        bet365Odds = matchOddsPrematch;
    }
    
    // If no Bet365, use first available
    if (!bet365Odds && Array.isArray(matchOddsPrematch) && matchOddsPrematch.length > 0) {
        bet365Odds = matchOddsPrematch[0];
    } else if (!bet365Odds && !Array.isArray(matchOddsPrematch)) {
        bet365Odds = matchOddsPrematch;
    }
    
    if (!bet365Odds) {
        oddsContainer.innerHTML = '<div class="text-center text-gray-400 py-8">Không có dữ liệu odds</div>';
        return;
    }
    
    // Extract opening and pre-match odds
    const openingOdds = bet365Odds.opening || bet365Odds.initial || {};
    const prematchOdds = bet365Odds.current || bet365Odds.prematch || bet365Odds;
    
    // Get handicap, over/under, and 1X2 odds
    const openingHandicap = openingOdds.asian_handicap || openingOdds.handicap || {};
    const prematchHandicap = prematchOdds.asian_handicap || prematchOdds.handicap || {};
    
    const openingOverUnder = openingOdds.over_under || openingOdds.total || {};
    const prematchOverUnder = prematchOdds.over_under || prematchOdds.total || {};
    
    const opening1X2 = openingOdds['1x2'] || openingOdds['1X2'] || openingOdds.full_time_result || {};
    const prematch1X2 = prematchOdds['1x2'] || prematchOdds['1X2'] || prematchOdds.full_time_result || {};
    
    let html = '<div class="space-y-4">';
    
    // Bookmaker header
    html += '<div class="mb-4">';
    html += '<span class="text-sm text-gray-300">Nhà cái: </span>';
    html += '<span class="inline-block px-3 py-1 bg-green-600 text-yellow-300 text-sm font-semibold rounded">bet365</span>';
    html += '</div>';
    
    // Odds table
    html += '<div class="bg-slate-900 rounded-lg overflow-hidden">';
    html += '<table class="w-full border-collapse">';
    html += '<thead>';
    html += '<tr class="bg-slate-800 border-b border-slate-700">';
    html += '<th class="px-4 py-3 text-left text-xs font-semibold text-gray-300"></th>';
    html += '<th class="px-4 py-3 text-center text-xs font-semibold text-white border-l border-slate-700">CƯỢC CHẤP TOÀN TRẬN</th>';
    html += '<th class="px-4 py-3 text-center text-xs font-semibold text-white border-l border-slate-700">TÀI XỈU TOÀN TRẬN ĐẤU</th>';
    html += '<th class="px-4 py-3 text-center text-xs font-semibold text-white border-l border-slate-700">1 X 2 TOÀN TRẬN ĐẤU</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    // Row 1: Opening Odds
    html += '<tr class="border-b border-slate-700">';
    html += '<td class="px-4 py-3 text-xs text-gray-300 font-medium">TỶ LỆ CƯỢC MỞ MÀN</td>';
    
    // Handicap Opening
    const openingHandicapValue = openingHandicap.handicap || openingHandicap.value || '0';
    const openingHandicapHome = openingHandicap.home || openingHandicap.home_odds || '-';
    const openingHandicapAway = openingHandicap.away || openingHandicap.away_odds || '-';
    html += `<td class="px-4 py-3 text-center text-xs text-white border-l border-slate-700">
        <div class="flex flex-col gap-1">
            <div>${openingHandicapValue} ${openingHandicapHome}</div>
            <div>${openingHandicapAway}</div>
        </div>
    </td>`;
    
    // Over/Under Opening
    const openingTotalValue = openingOverUnder.total || openingOverUnder.handicap || '2.5';
    const openingOver = openingOverUnder.over || openingOverUnder.over_odds || '-';
    const openingUnder = openingOverUnder.under || openingOverUnder.under_odds || '-';
    html += `<td class="px-4 py-3 text-center text-xs text-white border-l border-slate-700">
        <div class="flex flex-col gap-1">
            <div>${openingTotalValue} ${openingOver}</div>
            <div>${openingUnder}</div>
        </div>
    </td>`;
    
    // 1X2 Opening
    const opening1 = opening1X2.home || opening1X2['1'] || '-';
    const openingX = opening1X2.draw || opening1X2['X'] || '-';
    const opening2 = opening1X2.away || opening1X2['2'] || '-';
    html += `<td class="px-4 py-3 text-center text-xs text-white border-l border-slate-700">
        <div class="flex flex-col gap-1">
            <div>${opening1}</div>
            <div>${openingX}</div>
            <div>${opening2}</div>
        </div>
    </td>`;
    
    html += '</tr>';
    
    // Row 2: Pre-match Odds
    html += '<tr class="border-b border-slate-700">';
    html += '<td class="px-4 py-3 text-xs text-gray-300 font-medium">TỶ LỆ CƯỢC TRƯỚC TRẬN ĐẤU</td>';
    
    // Handicap Pre-match
    const prematchHandicapValue = prematchHandicap.handicap || prematchHandicap.value || '0';
    const prematchHandicapHome = prematchHandicap.home || prematchHandicap.home_odds || '-';
    const prematchHandicapAway = prematchHandicap.away || prematchHandicap.away_odds || '-';
    html += `<td class="px-4 py-3 text-center text-xs text-white border-l border-slate-700">
        <div class="flex flex-col gap-1">
            <div>${prematchHandicapValue} ${prematchHandicapHome}</div>
            <div>${prematchHandicapAway}</div>
        </div>
    </td>`;
    
    // Over/Under Pre-match
    const prematchTotalValue = prematchOverUnder.total || prematchOverUnder.handicap || '2.5';
    const prematchOver = prematchOverUnder.over || prematchOverUnder.over_odds || '-';
    const prematchUnder = prematchOverUnder.under || prematchOverUnder.under_odds || '-';
    html += `<td class="px-4 py-3 text-center text-xs text-white border-l border-slate-700">
        <div class="flex flex-col gap-1">
            <div>${prematchTotalValue} ${prematchOver}</div>
            <div>${prematchUnder}</div>
        </div>
    </td>`;
    
    // 1X2 Pre-match
    const prematch1 = prematch1X2.home || prematch1X2['1'] || '-';
    const prematchX = prematch1X2.draw || prematch1X2['X'] || '-';
    const prematch2 = prematch1X2.away || prematch1X2['2'] || '-';
    html += `<td class="px-4 py-3 text-center text-xs text-white border-l border-slate-700">
        <div class="flex flex-col gap-1">
            <div>${prematch1}</div>
            <div>${prematchX}</div>
            <div>${prematch2}</div>
        </div>
    </td>`;
    
    html += '</tr>';
    html += '</tbody>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    
    oddsContainer.innerHTML = html;
}

function renderMatchStats() {
    const statsContainer = document.getElementById('modal-tab-stats');
    if (!statsContainer) {
        return;
    }
    
    if (!matchStatsData || !matchStatsData.home || !matchStatsData.away) {
        statsContainer.innerHTML = '<div class="text-center text-gray-400 py-8">Không có dữ liệu thống kê</div>';
        return;
    }
    
    const homeStats = matchStatsData.home;
    const awayStats = matchStatsData.away;
    
    // Use cached team names (no API call needed)
    const homeTeamName = cachedTeamNames.home || homeStats.team_name || 'Đội nhà';
    const awayTeamName = cachedTeamNames.away || awayStats.team_name || 'Đội khách';
    
    // Render directly without API call
    renderStatsTable(statsContainer, homeStats, awayStats, homeTeamName, awayTeamName);
}

function renderStatsTable(container, homeStats, awayStats, homeTeamName, awayTeamName) {
    // Define stats to display
    const statsList = [
        { key: 'corners', label: 'Phạt góc', home: homeStats.corners || 0, away: awayStats.corners || 0 },
        { key: 'yellowcards', label: 'Thẻ vàng', home: homeStats.yellowcards || 0, away: awayStats.yellowcards || 0 },
        { key: 'redcards', label: 'Thẻ đỏ', home: homeStats.redcards || 0, away: awayStats.redcards || 0 },
        { key: 'shots_total', label: 'Tổng cú sút', home: homeStats.shots_total || 0, away: awayStats.shots_total || 0 },
        { key: 'shots_on_target', label: 'Sút trúng cầu môn', home: homeStats.shots_on_target || 0, away: awayStats.shots_on_target || 0 },
        { key: 'shots_off_target', label: 'Sút ra ngoài', home: homeStats.shots_off_target || 0, away: awayStats.shots_off_target || 0 },
        { key: 'possessionpercent', label: 'Kiểm soát bóng', home: homeStats.possessionpercent || 0, away: awayStats.possessionpercent || 0, is_percentage: true },
        { key: 'attacks', label: 'Pha tấn công', home: homeStats.attacks || 0, away: awayStats.attacks || 0 },
        { key: 'dangerous_attacks', label: 'Tấn công nguy hiểm', home: homeStats.dangerous_attacks || 0, away: awayStats.dangerous_attacks || 0 },
        { key: 'key_passes', label: 'Đường chuyền chủ chốt', home: homeStats.key_passes || 0, away: awayStats.key_passes || 0 },
    ];
    
    let html = '<div class="space-y-4">';
    html += '<div class="bg-slate-900 rounded-lg overflow-hidden">';
    html += '<table class="w-full border-collapse">';
    html += '<thead>';
    html += '<tr class="bg-slate-800 border-b border-slate-700">';
    html += '<th class="px-4 py-3 text-left text-xs font-semibold text-gray-300">Thống kê</th>';
    html += `<th class="px-4 py-3 text-center text-xs font-semibold text-white border-l border-slate-700">${homeTeamName}</th>`;
    html += `<th class="px-4 py-3 text-center text-xs font-semibold text-white border-l border-slate-700">${awayTeamName}</th>`;
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    statsList.forEach((stat, index) => {
        html += '<tr class="border-b border-slate-700">';
        html += `<td class="px-4 py-3 text-xs text-gray-300">${stat.label}</td>`;
        
        // Home value
        let homeValue = stat.home;
        if (stat.is_percentage) {
            homeValue = `${homeValue}%`;
        }
        html += `<td class="px-4 py-3 text-center text-xs text-white border-l border-slate-700">${homeValue}</td>`;
        
        // Away value
        let awayValue = stat.away;
        if (stat.is_percentage) {
            awayValue = `${awayValue}%`;
        }
        html += `<td class="px-4 py-3 text-center text-xs text-white border-l border-slate-700">${awayValue}</td>`;
        
        html += '</tr>';
    });
    
    html += '</tbody>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    
    container.innerHTML = html;
}

// Close modal on outside click
document.getElementById('match-detail-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMatchModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMatchModal();
    }
});

async function loadH2HData(matchId) {
    try {
        const response = await fetch(`/api/match-h2h/${matchId}`);
        const data = await response.json();
        
        if (data.success && data.data) {
            // Use cached team IDs instead of calling match-detail API again
            matchH2HData = {
                h2hData: data.data,
                currentHomeTeamId: matchTeamIds.home_team_id,
                currentAwayTeamId: matchTeamIds.away_team_id,
            };
            
            // Render H2H data
            renderH2HData();
        } else {
            const h2hContainer = document.getElementById('h2h-content');
            if (h2hContainer) {
                h2hContainer.innerHTML = '<div class="text-center text-gray-400 py-8">Không có dữ liệu H2H</div>';
            }
        }
    } catch (error) {
        console.error('Error loading H2H data:', error);
        const h2hContainer = document.getElementById('h2h-content');
        if (h2hContainer) {
            h2hContainer.innerHTML = '<div class="text-center text-red-400 py-8">Lỗi tải dữ liệu H2H</div>';
        }
    }
}

function renderH2HData() {
    const h2hContainer = document.getElementById('h2h-content');
    if (!h2hContainer || !matchH2HData) {
        if (h2hContainer) {
            h2hContainer.innerHTML = '<div class="text-center text-gray-400 py-8">Đang tải dữ liệu H2H...</div>';
        }
        return;
    }
    
    const h2hData = matchH2HData.h2hData;
    const currentHomeTeamId = matchH2HData.currentHomeTeamId;
    const currentAwayTeamId = matchH2HData.currentAwayTeamId;
    const homeTeam = h2hData.home || {};
    const awayTeam = h2hData.away || {};
    const h2hMatches = h2hData.h2h || [];
    
    let html = '';
            
            // Section 1: TRẬN ĐẤU GIỮA 2 ĐỘI (H2H matches)
            if (h2hMatches && h2hMatches.length > 0) {
                html += '<div class="mb-6">';
                html += '<h3 class="text-base font-bold text-white mb-3">TRẬN ĐẤU GIỮA 2 ĐỘI</h3>';
                html += '<div class="bg-slate-900 rounded-lg overflow-hidden">';
                
                const h2hLimited = h2hMatches.slice(0, 10);
                h2hLimited.forEach(match => {
                    const homeTeam = match.teams?.home || {};
                    const awayTeam = match.teams?.away || {};
                    const league = match.league || {};
                    const scores = match.scores || {};
                    // Use ft_score if available, otherwise use home_score/away_score
                    let homeScore = '';
                    let awayScore = '';
                    if (match.ft_score) {
                        const ftScoreParts = match.ft_score.split('-');
                        if (ftScoreParts.length === 2) {
                            homeScore = ftScoreParts[0].trim();
                            awayScore = ftScoreParts[1].trim();
                        }
                    }
                    if (!homeScore && !awayScore) {
                        homeScore = scores.home_score || '';
                        awayScore = scores.away_score || '';
                    }
                    const form = match.form || '';
                    const startDate = match.startdate ? new Date(match.startdate) : null;
                    const dateStr = startDate ? startDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: '2-digit' }) : '';
                    
                    // Determine result for current home team (the team we're viewing)
                    // form is from perspective of home team in h2h match
                    // We need to check if current home team is the home team in this match
                    const isCurrentHomeTeamHome = homeTeam.id === currentHomeTeamId;
                    let resultClass = 'bg-red-500';
                    let resultText = 'L';
                    if (form === 'W' && isCurrentHomeTeamHome) {
                        // Home team won and current home team is the home team
                        resultClass = 'bg-green-500';
                        resultText = 'W';
                    } else if (form === 'L' && isCurrentHomeTeamHome) {
                        // Home team lost and current home team is the home team
                        resultClass = 'bg-red-500';
                        resultText = 'L';
                    } else if (form === 'W' && !isCurrentHomeTeamHome) {
                        // Home team won, so current home team (away team) lost
                        resultClass = 'bg-red-500';
                        resultText = 'L';
                    } else if (form === 'L' && !isCurrentHomeTeamHome) {
                        // Home team lost, so current home team (away team) won
                        resultClass = 'bg-green-500';
                        resultText = 'W';
                    } else if (form === 'D') {
                        resultClass = 'bg-amber-500';
                        resultText = 'D';
                    }
                    
                    html += `
                        <div class="grid grid-cols-12 gap-2 px-4 py-3 border-b border-slate-700 hover:bg-slate-800 transition-colors">
                            <div class="col-span-2 text-xs text-gray-400">${dateStr}</div>
                            <div class="col-span-1 flex items-center">
                                ${league.img ? `<img src="${league.img}" alt="${league.name}" class="w-4 h-4 rounded-full object-contain" onerror="this.style.display='none'">` : ''}
                            </div>
                            <div class="col-span-1 text-xs text-gray-300 truncate">${league.name || ''}</div>
                            <div class="col-span-6 flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    ${homeTeam.img ? `<img src="${homeTeam.img}" alt="${homeTeam.name}" class="w-4 h-4 rounded-full object-contain flex-shrink-0" onerror="this.style.display='none'">` : ''}
                                    <span class="text-xs text-white ${homeTeam.id === currentHomeTeamId ? 'font-semibold' : 'text-gray-300'}">${homeTeam.name || ''}</span>
                                    <span class="text-xs text-gray-400 ml-auto">${homeScore}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    ${awayTeam.img ? `<img src="${awayTeam.img}" alt="${awayTeam.name}" class="w-4 h-4 rounded-full object-contain flex-shrink-0" onerror="this.style.display='none'">` : ''}
                                    <span class="text-xs text-white ${awayTeam.id === currentAwayTeamId ? 'font-semibold' : 'text-gray-300'}">${awayTeam.name || ''}</span>
                                    <span class="text-xs text-gray-400 ml-auto">${awayScore}</span>
                                </div>
                            </div>
                            <div class="col-span-2 flex items-center justify-end">
                                <div class="w-6 h-6 ${resultClass} rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">${resultText}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div></div>';
            }
            
            // Section 2: TRẬN ĐẤU: ĐỘI NHÀ (Home team matches)
            if (homeTeam.events && homeTeam.events.overall && homeTeam.events.overall.length > 0) {
                html += '<div class="mb-6">';
                html += '<h3 class="text-base font-bold text-white mb-3">TRẬN ĐẤU: ĐỘI NHÀ</h3>';
                html += '<div class="bg-slate-900 rounded-lg overflow-hidden">';
                
                const homeMatches = homeTeam.events.overall.slice(0, 10);
                homeMatches.forEach(match => {
                    const matchHomeTeam = match.teams?.home || {};
                    const matchAwayTeam = match.teams?.away || {};
                    const league = match.league || {};
                    const scores = match.scores || {};
                    // Use ft_score if available, otherwise use home_score/away_score
                    let homeScore = '';
                    let awayScore = '';
                    if (match.ft_score) {
                        const ftScoreParts = match.ft_score.split('-');
                        if (ftScoreParts.length === 2) {
                            homeScore = ftScoreParts[0].trim();
                            awayScore = ftScoreParts[1].trim();
                        }
                    }
                    if (!homeScore && !awayScore) {
                        homeScore = scores.home_score || '';
                        awayScore = scores.away_score || '';
                    }
                    const form = match.form || '';
                    const startDate = match.startdate ? new Date(match.startdate) : null;
                    const dateStr = startDate ? startDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: '2-digit' }) : '';
                    
                    // form is from perspective of home team in the match
                    // For home team section, we need to check if current home team is home or away in this match
                    const isCurrentHomeTeamHome = matchHomeTeam.id === currentHomeTeamId;
                    const isCurrentHomeTeamAway = matchAwayTeam.id === currentHomeTeamId;
                    let resultClass = 'bg-red-500';
                    let resultText = 'L';
                    if (form === 'W' && isCurrentHomeTeamHome) {
                        // Home team won and current home team is the home team
                        resultClass = 'bg-green-500';
                        resultText = 'W';
                    } else if (form === 'L' && isCurrentHomeTeamHome) {
                        // Home team lost and current home team is the home team
                        resultClass = 'bg-red-500';
                        resultText = 'L';
                    } else if (form === 'W' && isCurrentHomeTeamAway) {
                        // Home team won, so current home team (away team) lost
                        resultClass = 'bg-red-500';
                        resultText = 'L';
                    } else if (form === 'L' && isCurrentHomeTeamAway) {
                        // Home team lost, so current home team (away team) won
                        resultClass = 'bg-green-500';
                        resultText = 'W';
                    } else if (form === 'D') {
                        resultClass = 'bg-amber-500';
                        resultText = 'D';
                    }
                    
                    html += `
                        <div class="grid grid-cols-12 gap-2 px-4 py-3 border-b border-slate-700 hover:bg-slate-800 transition-colors">
                            <div class="col-span-2 text-xs text-gray-400">${dateStr}</div>
                            <div class="col-span-1 flex items-center">
                                ${league.img ? `<img src="${league.img}" alt="${league.name}" class="w-4 h-4 rounded-full object-contain" onerror="this.style.display='none'">` : ''}
                            </div>
                            <div class="col-span-1 text-xs text-gray-300 truncate">${league.name || ''}</div>
                            <div class="col-span-6 flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    ${matchHomeTeam.img ? `<img src="${matchHomeTeam.img}" alt="${matchHomeTeam.name}" class="w-4 h-4 rounded-full object-contain flex-shrink-0" onerror="this.style.display='none'">` : ''}
                                    <span class="text-xs ${matchHomeTeam.id === currentHomeTeamId ? 'text-white font-semibold' : 'text-gray-300'}">${matchHomeTeam.name || ''}</span>
                                    <span class="text-xs text-gray-400 ml-auto">${homeScore}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    ${matchAwayTeam.img ? `<img src="${matchAwayTeam.img}" alt="${matchAwayTeam.name}" class="w-4 h-4 rounded-full object-contain flex-shrink-0" onerror="this.style.display='none'">` : ''}
                                    <span class="text-xs ${matchAwayTeam.id === currentHomeTeamId ? 'text-white font-semibold' : 'text-gray-300'}">${matchAwayTeam.name || ''}</span>
                                    <span class="text-xs text-gray-400 ml-auto">${awayScore}</span>
                                </div>
                            </div>
                            <div class="col-span-2 flex items-center justify-end">
                                <div class="w-6 h-6 ${resultClass} rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">${resultText}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div></div>';
            }
            
            // Section 3: TRẬN ĐẤU: ĐỘI KHÁCH (Away team matches)
            if (awayTeam.events && awayTeam.events.overall && awayTeam.events.overall.length > 0) {
                html += '<div class="mb-6">';
                html += '<h3 class="text-base font-bold text-white mb-3">TRẬN ĐẤU: ĐỘI KHÁCH</h3>';
                html += '<div class="bg-slate-900 rounded-lg overflow-hidden">';
                
                const awayMatches = awayTeam.events.overall.slice(0, 10);
                awayMatches.forEach(match => {
                    const matchHomeTeam = match.teams?.home || {};
                    const matchAwayTeam = match.teams?.away || {};
                    const league = match.league || {};
                    const scores = match.scores || {};
                    // Use ft_score if available, otherwise use home_score/away_score
                    let homeScore = '';
                    let awayScore = '';
                    if (match.ft_score) {
                        const ftScoreParts = match.ft_score.split('-');
                        if (ftScoreParts.length === 2) {
                            homeScore = ftScoreParts[0].trim();
                            awayScore = ftScoreParts[1].trim();
                        }
                    }
                    if (!homeScore && !awayScore) {
                        homeScore = scores.home_score || '';
                        awayScore = scores.away_score || '';
                    }
                    const form = match.form || '';
                    const startDate = match.startdate ? new Date(match.startdate) : null;
                    const dateStr = startDate ? startDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: '2-digit' }) : '';
                    
                    // form is from perspective of home team in the match
                    // For away team section, we need to check if current away team is home or away in this match
                    const isAwayTeamHome = matchHomeTeam.id === currentAwayTeamId;
                    const isAwayTeamAway = matchAwayTeam.id === currentAwayTeamId;
                    let resultClass = 'bg-red-500';
                    let resultText = 'L';
                    if (form === 'W' && isAwayTeamHome) {
                        // Home team won and current away team is the home team
                        resultClass = 'bg-green-500';
                        resultText = 'W';
                    } else if (form === 'L' && isAwayTeamHome) {
                        // Home team lost and current away team is the home team
                        resultClass = 'bg-red-500';
                        resultText = 'L';
                    } else if (form === 'W' && isAwayTeamAway) {
                        // Home team won, so current away team (away team) lost
                        resultClass = 'bg-red-500';
                        resultText = 'L';
                    } else if (form === 'L' && isAwayTeamAway) {
                        // Home team lost, so current away team (away team) won
                        resultClass = 'bg-green-500';
                        resultText = 'W';
                    } else if (form === 'D') {
                        resultClass = 'bg-amber-500';
                        resultText = 'D';
                    }
                    
                    html += `
                        <div class="grid grid-cols-12 gap-2 px-4 py-3 border-b border-slate-700 hover:bg-slate-800 transition-colors">
                            <div class="col-span-2 text-xs text-gray-400">${dateStr}</div>
                            <div class="col-span-1 flex items-center">
                                ${league.img ? `<img src="${league.img}" alt="${league.name}" class="w-4 h-4 rounded-full object-contain" onerror="this.style.display='none'">` : ''}
                            </div>
                            <div class="col-span-1 text-xs text-gray-300 truncate">${league.name || ''}</div>
                            <div class="col-span-6 flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    ${matchHomeTeam.img ? `<img src="${matchHomeTeam.img}" alt="${matchHomeTeam.name}" class="w-4 h-4 rounded-full object-contain flex-shrink-0" onerror="this.style.display='none'">` : ''}
                                    <span class="text-xs ${matchHomeTeam.id === currentAwayTeamId ? 'text-white font-semibold' : 'text-gray-300'}">${matchHomeTeam.name || ''}</span>
                                    <span class="text-xs text-gray-400 ml-auto">${homeScore}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    ${matchAwayTeam.img ? `<img src="${matchAwayTeam.img}" alt="${matchAwayTeam.name}" class="w-4 h-4 rounded-full object-contain flex-shrink-0" onerror="this.style.display='none'">` : ''}
                                    <span class="text-xs ${matchAwayTeam.id === currentAwayTeamId ? 'text-white font-semibold' : 'text-gray-300'}">${matchAwayTeam.name || ''}</span>
                                    <span class="text-xs text-gray-400 ml-auto">${awayScore}</span>
                                </div>
                            </div>
                            <div class="col-span-2 flex items-center justify-end">
                                <div class="w-6 h-6 ${resultClass} rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">${resultText}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div></div>';
            }
            
    if (!html) {
        html = '<div class="text-center text-gray-400 py-8">Không có dữ liệu H2H</div>';
    }
    
    h2hContainer.innerHTML = html;
}
</script>

<style>
.modal-tab-content {
    min-height: 200px;
}
</style>

