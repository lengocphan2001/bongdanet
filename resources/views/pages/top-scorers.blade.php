@extends('layouts.app')

@section('title', 'keobong88 - Top Ghi Bàn')

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobong88', 'url' => route('home')],
        ['label' => 'Top Ghi Bàn', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Main Container --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 backdrop-blur-sm" style="overflow: visible;">
                    {{-- Page Title --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Top Ghi Bàn</span>
                        </h1>
                    </div>

                    {{-- Search Box --}}
                    <div class="mb-4">
                        <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-3 sm:p-4 backdrop-blur-sm">
                            <div class="relative mb-3">
                                <input 
                                    type="text" 
                                    id="league-search-input"
                                    placeholder="Tìm kiếm giải đấu, quốc gia, châu lục..." 
                                    class="w-full px-4 py-2.5 pl-10 bg-slate-900/50 border border-slate-600/50 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all duration-200"
                                    autocomplete="off"
                                />
                                <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <button 
                                    id="clear-search-btn"
                                    class="absolute right-3 top-3 text-gray-400 hover:text-white transition-colors hidden"
                                    style="display: none;"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div id="search-results-count" class="mt-2 text-xs text-gray-400 hidden"></div>
                            
                            {{-- League Selection - Custom Dropdown --}}
                            <form method="GET" action="{{ route('top-scorers') }}" class="mt-3" id="league-form" style="position: relative; z-index: 100;">
                                <label class="block text-xs font-semibold text-gray-300 mb-2">Chọn giải đấu:</label>
                                <div class="relative" style="z-index: 100;">
                                    <button 
                                        type="button"
                                        id="league-dropdown-button"
                                        class="w-full px-4 py-2.5 bg-gradient-to-r from-slate-900/80 to-slate-800/80 border border-slate-600/50 rounded-lg text-white hover:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all duration-200 flex items-center justify-between group"
                                    >
                                        <span id="league-selected-text" class="text-sm font-medium truncate flex-1 text-left">
                                            @if($selectedLeagueId && $selectedLeague)
                                                {{ $selectedLeague['country_name'] ? $selectedLeague['country_name'] . ': ' : '' }}{{ $selectedLeague['name'] ?? 'N/A' }}
                                            @else
                                                -- Chọn giải đấu --
                                            @endif
                                        </span>
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-400 transition-colors flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <input type="hidden" name="league_id" id="league-id-input" value="{{ $selectedLeagueId ?? '' }}">
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- Top Scorers Table Container - Will be populated via AJAX --}}
                <div id="top-scorers-table-container" class="mt-4">
                    @if($selectedLeagueId && !empty($topScorers))
                        @include('pages.partials.top-scorers-table', ['selectedLeague' => $selectedLeague, 'topScorers' => $topScorers])
                    @elseif($selectedLeagueId && empty($topScorers))
                        <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm p-8">
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-400 text-sm sm:text-base font-medium">Không có dữ liệu top ghi bàn cho giải đấu này</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm p-8">
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-400 text-sm sm:text-base font-medium">Vui lòng chọn giải đấu để xem top ghi bàn</p>
                            </div>
                        </div>
                    @endif
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
    const searchInput = document.getElementById('league-search-input');
    const clearBtn = document.getElementById('clear-search-btn');
    const resultsCount = document.getElementById('search-results-count');
    const dropdownButton = document.getElementById('league-dropdown-button');
    const leagueIdInput = document.getElementById('league-id-input');
    const selectedText = document.getElementById('league-selected-text');
    const leagueForm = document.getElementById('league-form');
    
    // Check if elements exist
    if (!searchInput || !dropdownButton) {
        return;
    }
    
    // League data from server
    const allLeaguesData = @json($allLeagues ?? []);
    const selectedLeagueId = @json($selectedLeagueId ?? null);
    
    // Create dropdown menu element (will be appended to body)
    let dropdownMenu = document.getElementById('league-dropdown-menu');
    if (!dropdownMenu) {
        dropdownMenu = document.createElement('div');
        dropdownMenu.id = 'league-dropdown-menu';
        dropdownMenu.className = 'hidden fixed bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 border border-slate-700/50 rounded-lg shadow-2xl backdrop-blur-sm max-h-96 overflow-hidden';
        dropdownMenu.style.zIndex = '99999';
        dropdownMenu.style.display = 'none';
        document.body.appendChild(dropdownMenu);
    }
    
    // Function to render dropdown content
    function renderDropdownContent() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let filteredLeagues = allLeaguesData;
        
        if (searchTerm !== '') {
            filteredLeagues = allLeaguesData.filter(league => {
                const leagueName = (league.name || '').toLowerCase();
                const countryName = (league.country_name || '').toLowerCase();
                const continentName = (league.continent_name || '').toLowerCase();
                const searchText = leagueName + ' ' + countryName + ' ' + continentName;
                return searchText.includes(searchTerm);
            });
        }
        
        let html = '<div class="overflow-y-auto max-h-96 scrollbar-hide"><div class="p-2">';
        
        // Default option
        html += `<div class="league-option px-4 py-2.5 rounded-lg cursor-pointer hover:bg-gradient-to-r hover:from-emerald-500/10 hover:to-green-600/10 transition-all duration-200 ${!selectedLeagueId ? 'bg-emerald-500/10 border border-emerald-500/30' : ''}" data-league-id="" data-league-name="-- Chọn giải đấu --">`;
        html += '<div class="text-sm font-medium text-gray-300">-- Chọn giải đấu --</div>';
        html += '</div>';
        
        // League options
        filteredLeagues.forEach(league => {
            const leagueId = league.id || null;
            const leagueName = league.name || 'N/A';
            const countryName = league.country_name || '';
            const continentName = league.continent_name || '';
            const displayName = countryName ? countryName + ': ' + leagueName : leagueName;
            const isSelected = selectedLeagueId == leagueId;
            
            html += `<div class="league-option px-4 py-2.5 rounded-lg cursor-pointer hover:bg-gradient-to-r hover:from-emerald-500/10 hover:to-green-600/10 transition-all duration-200 ${isSelected ? 'bg-emerald-500/10 border border-emerald-500/30' : ''}" data-league-id="${leagueId}" data-league-name="${displayName.replace(/"/g, '&quot;')}">`;
            html += `<div class="text-sm font-medium text-white">${displayName.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>`;
            if (countryName || continentName) {
                html += `<div class="text-xs text-gray-400 mt-0.5">${(countryName || '') + (countryName && continentName ? ' - ' : '') + (continentName || '')}</div>`;
            }
            html += '</div>';
        });
        
        html += '</div></div>';
        dropdownMenu.innerHTML = html;
        
        // Update results count
        if (resultsCount) {
            if (searchTerm !== '') {
                resultsCount.classList.remove('hidden');
                resultsCount.textContent = `Tìm thấy ${filteredLeagues.length} giải đấu`;
            } else {
                resultsCount.classList.add('hidden');
            }
        }
        
        // Attach click handlers to options
        const leagueOptions = dropdownMenu.querySelectorAll('.league-option');
        leagueOptions.forEach(option => {
            option.addEventListener('click', function() {
                const leagueId = this.getAttribute('data-league-id');
                const leagueName = this.getAttribute('data-league-name');
                
                leagueIdInput.value = leagueId;
                selectedText.textContent = leagueName;
                
                // Close dropdown
                dropdownMenu.classList.add('hidden');
                dropdownMenu.style.display = 'none';
                
                // Load top scorers via AJAX
                if (leagueId) {
                    loadTopScorers(leagueId);
                } else {
                    // Clear table if no league selected
                    const container = document.getElementById('top-scorers-table-container');
                    if (container) {
                        container.innerHTML = `
                            <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm p-8">
                                <div class="text-center py-12">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-400 text-sm sm:text-base font-medium">Vui lòng chọn giải đấu để xem top ghi bàn</p>
                                </div>
                            </div>
                        `;
                    }
                }
            });
        });
    }
    
    // Function to position dropdown
    function positionDropdown() {
        const buttonRect = dropdownButton.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
        
        dropdownMenu.style.width = buttonRect.width + 'px';
        dropdownMenu.style.left = (buttonRect.left + scrollLeft) + 'px';
        dropdownMenu.style.top = (buttonRect.bottom + scrollTop + 8) + 'px';
        dropdownMenu.style.display = 'block';
        dropdownMenu.style.zIndex = '99999';
    }
    
    // Initial render
    renderDropdownContent();
    
    // Toggle dropdown
    dropdownButton.addEventListener('click', function(e) {
        e.stopPropagation();
        const isHidden = dropdownMenu.classList.contains('hidden') || dropdownMenu.style.display === 'none';
        if (isHidden) {
            renderDropdownContent();
            dropdownMenu.classList.remove('hidden');
            positionDropdown();
        } else {
            dropdownMenu.classList.add('hidden');
            dropdownMenu.style.display = 'none';
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.add('hidden');
            dropdownMenu.style.display = 'none';
        }
    });
    
    // Reposition on scroll and resize
    window.addEventListener('scroll', function() {
        if (!dropdownMenu.classList.contains('hidden') && dropdownMenu.style.display !== 'none') {
            positionDropdown();
        }
    }, true);
    
    window.addEventListener('resize', function() {
        if (!dropdownMenu.classList.contains('hidden') && dropdownMenu.style.display !== 'none') {
            positionDropdown();
        }
    });
    
    // Search input event
    searchInput.addEventListener('input', function(e) {
        renderDropdownContent();
        // Open dropdown when searching
        if (e.target.value.trim() !== '') {
            if (dropdownMenu.classList.contains('hidden')) {
                dropdownMenu.classList.remove('hidden');
            }
            positionDropdown();
        }
    });
    
    // Clear button event
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            renderDropdownContent();
            searchInput.focus();
        });
    }
    
    // Handle Enter key to prevent form submission
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
    
    // Function to load top scorers via AJAX
    function loadTopScorers(leagueId) {
        const container = document.getElementById('top-scorers-table-container');
        if (!container) return;
        
        // Show loading state
        container.innerHTML = `
            <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm p-8">
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4 animate-spin">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400 text-sm sm:text-base font-medium">Đang tải dữ liệu...</p>
                </div>
            </div>
        `;
        
        // Fetch data
        fetch(`{{ route('api.top-scorers.data') }}?league_id=${leagueId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const { league, topScorers } = result.data;
                renderTopScorersTable(league, topScorers);
            } else {
                container.innerHTML = `
                    <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm p-8">
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-400 text-sm sm:text-base font-medium">${result.message || 'Không có dữ liệu top ghi bàn cho giải đấu này'}</p>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading top scorers:', error);
            container.innerHTML = `
                <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm p-8">
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-400 text-sm sm:text-base font-medium">Đã xảy ra lỗi khi tải dữ liệu</p>
                    </div>
                </div>
            `;
        });
    }
    
    // Function to render top scorers table
    function renderTopScorersTable(league, topScorers) {
        const container = document.getElementById('top-scorers-table-container');
        if (!container) return;
        
        if (!topScorers || topScorers.length === 0) {
            container.innerHTML = `
                <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm p-8">
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-400 text-sm sm:text-base font-medium">Không có dữ liệu top ghi bàn cho giải đấu này</p>
                    </div>
                </div>
            `;
            return;
        }
        
        const leagueName = (league.country_name ? league.country_name + ': ' : '') + (league.name || 'N/A');
        
        let tableHTML = `
            <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm relative">
                <div class="mb-6 pb-4 border-b border-slate-700/50">
                    <h3 class="text-xl sm:text-2xl font-bold text-white">${leagueName.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</h3>
                </div>
                <div class="overflow-x-auto scrollbar-hide">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-emerald-500/20 to-green-600/20 border-b border-emerald-500/30">
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-emerald-400 uppercase tracking-wider">#</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-emerald-400 uppercase tracking-wider">Cầu thủ</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-emerald-400 uppercase tracking-wider">Đội bóng</th>
                                <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Trận</th>
                                <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Bàn thắng</th>
                                <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Sân nhà</th>
                                <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Sân khách</th>
                                <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-emerald-400 uppercase tracking-wider">Phạt đền</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        topScorers.forEach((scorer, index) => {
            const pos = index + 1;
            // Handle both API response formats
            const playerName = scorer.player_name || (scorer.player && (scorer.player.name || scorer.player.name_short)) || 'N/A';
            const teamName = scorer.team_name || (scorer.team && (scorer.team.name || scorer.team.name_short)) || 'N/A';
            const matchesPlayed = scorer.matches_played || 0;
            const goals = scorer.goals || {};
            const overallGoals = goals.overall || 0;
            const homeGoals = goals.home || 0;
            const awayGoals = goals.away || 0;
            const penalties = scorer.penalties !== null && scorer.penalties !== undefined ? scorer.penalties : null;
            
            const posClass = pos <= 3 
                ? 'bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold'
                : (pos <= 10 
                    ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold'
                    : 'bg-slate-700 text-gray-300 font-medium');
            
            tableHTML += `
                <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200">
                    <td class="px-3 sm:px-4 py-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full ${posClass}">${pos}</div>
                    </td>
                    <td class="px-3 sm:px-4 py-3">
                        <div class="text-sm font-semibold text-white">${playerName.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                    </td>
                    <td class="px-3 sm:px-4 py-3">
                        <div class="text-sm text-gray-300">${teamName.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                    </td>
                    <td class="px-3 sm:px-4 py-3 text-center">
                        <div class="text-sm text-gray-300">${matchesPlayed}</div>
                    </td>
                    <td class="px-3 sm:px-4 py-3 text-center">
                        <div class="inline-flex items-center justify-center px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-black rounded-lg shadow-lg shadow-emerald-500/25 min-w-[50px]">${overallGoals}</div>
                    </td>
                    <td class="px-3 sm:px-4 py-3 text-center">
                        <div class="text-sm text-gray-300">${homeGoals}</div>
                    </td>
                    <td class="px-3 sm:px-4 py-3 text-center">
                        <div class="text-sm text-gray-300">${awayGoals}</div>
                    </td>
                    <td class="px-3 sm:px-4 py-3 text-center">
                        <div class="text-sm text-gray-300">${penalties !== null ? penalties : '<span class="text-gray-500">-</span>'}</div>
                    </td>
                </tr>
            `;
        });
        
        tableHTML += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        container.innerHTML = tableHTML;
    }
});
</script>
@endsection

