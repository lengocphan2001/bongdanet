@extends('layouts.app')

@section('title', 'Bảng xếp hạng bóng đá - Bongdanet')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'BONGDANET', 'url' => route('home')],
        ['label' => 'Bảng xếp hạng bóng đá', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Page Title --}}
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">
                    Bảng xếp hạng bóng đá - Danh sách các giải đấu
                </h1>

                {{-- Search Box --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="league-search-input"
                            placeholder="Tìm kiếm giải đấu, quốc gia, châu lục..." 
                            class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a5f2f] focus:border-[#1a5f2f] outline-none"
                            autocomplete="off"
                        />
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button 
                            id="clear-search-btn"
                            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 hidden"
                            style="display: none;"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div id="search-results-count" class="mt-2 text-sm text-gray-600 hidden"></div>
                </div>

                {{-- Leagues Grid --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    @if(empty($leagues))
                        <div class="text-center py-8 text-gray-500">
                            Không có dữ liệu giải đấu
                        </div>
                    @else
                        <div id="leagues-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                            @foreach($leagues as $league)
                                <a href="{{ route('standings.show', $league['id']) }}" 
                                   class="league-item block p-4 border border-gray-200 rounded-lg hover:border-[#1a5f2f] hover:bg-green-50 transition-all"
                                   data-league-name="{{ strtolower($league['name'] ?? '') }}"
                                   data-country-name="{{ strtolower($league['country_name'] ?? '') }}"
                                   data-continent-name="{{ strtolower($league['continent_name'] ?? '') }}"
                                   data-search-text="{{ strtolower(($league['name'] ?? '') . ' ' . ($league['country_name'] ?? '') . ' ' . ($league['continent_name'] ?? '')) }}">
                                    <div class="font-semibold text-gray-900 mb-1">{{ $league['name'] ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-600">
                                        @if(isset($league['country_name']))
                                            {{ $league['country_name'] }}
                                        @endif
                                        @if(isset($league['continent_name']))
                                            - {{ $league['continent_name'] }}
                                        @endif
                                    </div>
                                    @if(isset($league['is_cup']) && $league['is_cup'] == '1')
                                        <span class="inline-block mt-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Cup</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                        
                        {{-- Empty search results message --}}
                        <div id="no-results-message" class="hidden text-center py-8 text-gray-500">
                            Không tìm thấy giải đấu nào phù hợp với từ khóa tìm kiếm
                        </div>
                        
                        {{-- Pagination --}}
                        @if($totalPages > 1)
                            <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 pt-4 space-y-3 sm:space-y-0">
                                <div class="text-xs sm:text-sm text-gray-700 text-center sm:text-left">
                                    Hiển thị <span class="font-medium">{{ (($currentPage - 1) * $perPage) + 1 }}</span> đến 
                                    <span class="font-medium">{{ min($currentPage * $perPage, $total) }}</span> trong tổng số 
                                    <span class="font-medium">{{ $total }}</span> giải đấu
                                </div>
                                
                                <div class="flex items-center space-x-1 sm:space-x-2 flex-wrap justify-center">
                                    {{-- Previous Button --}}
                                    @if($currentPage > 1)
                                        <a href="{{ route('standings.index', ['page' => $currentPage - 1]) }}" 
                                           class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Trước
                                        </a>
                                    @else
                                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                                            Trước
                                        </span>
                                    @endif
                                    
                                    {{-- Page Numbers --}}
                                    <div class="flex items-center space-x-1">
                                        @php
                                            $startPage = max(1, $currentPage - 2);
                                            $endPage = min($totalPages, $currentPage + 2);
                                            
                                            // Show first page if not in range
                                            if ($startPage > 1) {
                                                echo '<a href="' . route('standings.index', ['page' => 1]) . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">1</a>';
                                                if ($startPage > 2) {
                                                    echo '<span class="px-3 py-2 text-sm font-medium text-gray-500">...</span>';
                                                }
                                            }
                                            
                                            // Show pages in range
                                            for ($i = $startPage; $i <= $endPage; $i++) {
                                                if ($i == $currentPage) {
                                                    echo '<span class="px-3 py-2 text-sm font-medium text-white bg-[#1a5f2f] border border-[#1a5f2f] rounded-md">' . $i . '</span>';
                                                } else {
                                                    echo '<a href="' . route('standings.index', ['page' => $i]) . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">' . $i . '</a>';
                                                }
                                            }
                                            
                                            // Show last page if not in range
                                            if ($endPage < $totalPages) {
                                                if ($endPage < $totalPages - 1) {
                                                    echo '<span class="px-3 py-2 text-sm font-medium text-gray-500">...</span>';
                                                }
                                                echo '<a href="' . route('standings.index', ['page' => $totalPages]) . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">' . $totalPages . '</a>';
                                            }
                                        @endphp
                                    </div>
                                    
                                    {{-- Next Button --}}
                                    @if($currentPage < $totalPages)
                                        <a href="{{ route('standings.index', ['page' => $currentPage + 1]) }}" 
                                           class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Sau
                                        </a>
                                    @else
                                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                                            Sau
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4 order-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
                    <h3 class="font-bold text-gray-900 mb-3 text-sm sm:text-base">BẢNG XẾP HẠNG BÓNG ĐÁ</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('standings.index') }}" class="text-xs sm:text-sm text-gray-700 hover:text-[#1a5f2f]">Bảng xếp hạng bóng đá</a></li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('league-search-input');
    const clearBtn = document.getElementById('clear-search-btn');
    const resultsCount = document.getElementById('search-results-count');
    const leaguesContainer = document.getElementById('leagues-container');
    const noResultsMessage = document.getElementById('no-results-message');
    const paginationSection = document.querySelector('.mt-6.flex.items-center.justify-between');
    
    // Check if elements exist
    if (!searchInput || !leaguesContainer) {
        console.error('Search elements not found', { searchInput, leaguesContainer });
        return;
    }
    
    // Store all leagues data from server
    const allLeaguesData = @json($allLeagues ?? []);
    const currentPageLeagues = @json($leagues ?? []);
    const standingsBaseUrl = '{{ url("/bang-xep-hang-bong-da") }}';
    
    // Store original HTML to restore later
    const originalLeaguesHTML = leaguesContainer.innerHTML;
    
    // Function to render league item HTML
    function renderLeagueItem(league) {
        const leagueName = league.name || 'N/A';
        const countryName = league.country_name || '';
        const continentName = league.continent_name || '';
        const isCup = league.is_cup == '1';
        const leagueId = league.id;
        const searchText = (leagueName + ' ' + countryName + ' ' + continentName).toLowerCase();
        const standingsUrl = `${standingsBaseUrl}/${leagueId}`;
        
        return `
            <a href="${standingsUrl}" 
               class="league-item block p-4 border border-gray-200 rounded-lg hover:border-[#1a5f2f] hover:bg-green-50 transition-all"
               data-league-name="${leagueName.toLowerCase()}"
               data-country-name="${countryName.toLowerCase()}"
               data-continent-name="${continentName.toLowerCase()}"
               data-search-text="${searchText}">
                <div class="font-semibold text-gray-900 mb-1">${escapeHtml(leagueName)}</div>
                <div class="text-sm text-gray-600">
                    ${escapeHtml(countryName)}${continentName ? ' - ' + escapeHtml(continentName) : ''}
                </div>
                ${isCup ? '<span class="inline-block mt-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Cup</span>' : ''}
            </a>
        `;
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Function to render leagues in container
    function renderLeagues(leagues) {
        if (leagues.length === 0) {
            leaguesContainer.innerHTML = '';
            if (noResultsMessage) {
                noResultsMessage.classList.remove('hidden');
            }
        } else {
            if (noResultsMessage) {
                noResultsMessage.classList.add('hidden');
            }
            leaguesContainer.innerHTML = leagues.map(league => renderLeagueItem(league)).join('');
        }
    }
    
    function filterLeagues(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        
        if (term === '') {
            // Restore original HTML (current page leagues)
            leaguesContainer.innerHTML = originalLeaguesHTML;
            
            if (clearBtn) {
                clearBtn.style.display = 'none';
            }
            if (resultsCount) {
                resultsCount.classList.add('hidden');
            }
            
            // Show pagination if it exists
            if (paginationSection) {
                paginationSection.style.display = '';
            }
        } else {
            // Filter all leagues
            const filteredLeagues = allLeaguesData.filter(league => {
                const leagueName = (league.name || '').toLowerCase();
                const countryName = (league.country_name || '').toLowerCase();
                const continentName = (league.continent_name || '').toLowerCase();
                const searchText = leagueName + ' ' + countryName + ' ' + continentName;
                return searchText.includes(term);
            });
            
            renderLeagues(filteredLeagues);
            
            if (clearBtn) {
                clearBtn.style.display = 'block';
            }
            if (resultsCount) {
                resultsCount.classList.remove('hidden');
                resultsCount.textContent = `Tìm thấy ${filteredLeagues.length} giải đấu`;
            }
            
            // Hide pagination when searching
            if (paginationSection) {
                paginationSection.style.display = 'none';
            }
        }
    }
    
    // Search input event
    searchInput.addEventListener('input', function(e) {
        filterLeagues(e.target.value);
    });
    
    // Clear button event
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterLeagues('');
        searchInput.focus();
    });
    
    // Handle Enter key to prevent form submission
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
    
    // Get search term from URL parameter if exists
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam) {
        searchInput.value = searchParam;
        filterLeagues(searchParam);
    }
});
</script>
@endsection

