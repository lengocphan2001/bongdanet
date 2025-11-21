@extends('layouts.app')

@section('title', 'keobong88 - Bảng xếp hạng ' . ($league['name'] ?? '') . ' - Keobongda')  

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobong88', 'url' => route('home')],
        ['label' => 'Bảng xếp hạng bóng đá', 'url' => route('standings.index')],
        ['label' => $league['name'] ?? 'N/A', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Main Container --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">
                    {{-- Page Title --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Bảng Xếp Hạng {{ $league['name'] ?? 'N/A' }}</span>
                        </h1>
                    </div>

                    {{-- Standings View Tabs -- Only show for non-CUP leagues --}}
                    @php
                        $hasGroups = ($leagueInfo['has_groups'] ?? 0) == 1 || count($groupedStandings ?? []) > 1;
                    @endphp
                    
                    @if(!($isCupFormat ?? false))
                        <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-2.5 mb-4 backdrop-blur-sm">
                            <div class="flex items-center gap-1.5">
                                <button onclick="showStandings('all')" 
                                        id="tab-all"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 text-white bg-gradient-to-r from-purple-600 to-purple-700 shadow-md shadow-purple-500/20">
                                    Tất cả
                                </button>
                                <button onclick="showStandings('home')" 
                                        id="tab-home"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 text-gray-300 bg-slate-700/50 hover:bg-slate-700 hover:text-white">
                                    Sân nhà
                                </button>
                                <button onclick="showStandings('away')" 
                                        id="tab-away"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 text-gray-300 bg-slate-700/50 hover:bg-slate-700 hover:text-white">
                                    Sân khách
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Standings Table --}}
                    <div class="space-y-6" id="standings-container">
                        @if(empty($standings) && empty($groupedStandings))
                            <div class="text-center py-12 sm:py-16">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-400 text-sm sm:text-base font-medium">Không có dữ liệu bảng xếp hạng</p>
                            </div>
                        @else
                            @php
                                $hasGroups = ($leagueInfo['has_groups'] ?? 0) == 1 || count($groupedStandings ?? []) > 1;
                                $standingsToRender = $hasGroups ? ($groupedStandings ?? []) : ['default' => ($standings ?? [])];
                            @endphp
                            
                            @foreach($standingsToRender as $groupName => $groupTeams)
                                <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl border border-slate-700/50 shadow-xl backdrop-blur-sm overflow-hidden group-table" data-group="{{ $groupName }}">
                                    {{-- Group Header --}}
                                    @if($hasGroups && $groupName !== 'default')
                                        <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 px-4 py-3 border-b border-slate-700/50">
                                            <div class="flex items-center gap-2">
                                                <div class="w-1 h-5 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div>
                                                <h3 class="text-sm font-bold text-purple-400 uppercase">{{ $groupName }}</h3>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- Table --}}
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b border-slate-600/50 backdrop-blur-sm">
                                                <tr>
                                                    <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">XH</th>
                                                    <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Đội bóng</th>
                                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">Trận</th>
                                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">Thắng</th>
                                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">Hòa</th>
                                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">Thua</th>
                                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">BT</th>
                                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">BB</th>
                                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">H/s</th>
                                                    <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">Điểm</th>
                                                    @if(!($isCupFormat ?? false))
                                                        <th class="px-3 sm:px-4 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider form-column">Phong độ</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-700/50 standings-body" data-group="{{ $groupName }}">
                                            
                                            @foreach($groupTeams as $index => $team)
                                                @php
                                                    $overall = $team['overall'] ?? [];
                                                    $home = $team['home'] ?? [];
                                                    $away = $team['away'] ?? [];
                                                    $position = $overall['position'] ?? ($index + 1);
                                                    $points = $overall['points'] ?? ($team['points'] ?? 0);
                                                    $recentForm = $team['recent_form'] ?? '';
                                                    $status = $team['status'] ?? null;
                                                    $result = $team['result'] ?? null;
                                                @endphp
                                                <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 standings-row {{ ($index % 2 === 0) ? '' : 'bg-slate-800/30' }}" data-type="all">
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm font-bold text-purple-400">{{ $position }}</td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-white font-medium">
                                                        {{ $team['team_name'] ?? 'N/A' }}
                                                        @if($status)
                                                            <span class="ml-2 text-xs text-gray-400">({{ $status }})</span>
                                                        @endif
                                                        @if($result)
                                                            <span class="ml-1 text-xs text-purple-400 font-semibold">{{ $result }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['games_played'] ?? 0 }}</td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-emerald-400 text-center font-semibold">{{ $overall['won'] ?? 0 }}</td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-amber-400 text-center font-semibold">{{ $overall['draw'] ?? 0 }}</td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-red-400 text-center font-semibold">{{ $overall['lost'] ?? 0 }}</td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['goals_scored'] ?? 0 }}</td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['goals_against'] ?? 0 }}</td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center font-semibold">{{ $overall['goals_diff'] ?? 0 }}</td>
                                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm font-bold text-center text-white bg-gradient-to-r from-purple-600/20 to-purple-700/20 border border-purple-500/30 rounded">{{ $points }}</td>
                                                    @if(!($isCupFormat ?? false))
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-center form-column">
                                                            <div class="flex items-center justify-center space-x-1">
                                                                @if($recentForm)
                                                                    @foreach(str_split($recentForm) as $formResult)
                                                                        @if($formResult == 'W')
                                                                            <span class="w-5 h-5 bg-gradient-to-r from-emerald-500 to-green-600 text-white text-[10px] font-bold rounded flex items-center justify-center shadow-md shadow-emerald-500/25">T</span>
                                                                        @elseif($formResult == 'D')
                                                                            <span class="w-5 h-5 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-[10px] font-bold rounded flex items-center justify-center shadow-md shadow-amber-500/25">H</span>
                                                                        @elseif($formResult == 'L')
                                                                            <span class="w-5 h-5 bg-gradient-to-r from-red-500 to-red-600 text-white text-[10px] font-bold rounded flex items-center justify-center shadow-md shadow-red-500/25">B</span>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-gray-500">-</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                                @if(!($isCupFormat ?? false))
                                                    {{-- Home row (hidden by default) --}}
                                                    <tr class="hidden hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 standings-row {{ ($index % 2 === 0) ? '' : 'bg-slate-800/30' }}" data-type="home">
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm font-bold text-purple-400">{{ $home['position'] ?? $position }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-white font-medium">{{ $team['team_name'] ?? 'N/A' }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['games_played'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-emerald-400 text-center font-semibold">{{ $home['won'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-amber-400 text-center font-semibold">{{ $home['draw'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-red-400 text-center font-semibold">{{ $home['lost'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['goals_scored'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['goals_against'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center font-semibold">{{ $home['goals_diff'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm font-bold text-center text-white bg-gradient-to-r from-purple-600/20 to-purple-700/20 border border-purple-500/30 rounded">{{ $home['points'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-center form-column">
                                                            <span class="text-gray-500">-</span>
                                                        </td>
                                                    </tr>
                                                    {{-- Away row (hidden by default) --}}
                                                    <tr class="hidden hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 standings-row {{ ($index % 2 === 0) ? '' : 'bg-slate-800/30' }}" data-type="away">
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm font-bold text-purple-400">{{ $away['position'] ?? $position }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-white font-medium">{{ $team['team_name'] ?? 'N/A' }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['games_played'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-emerald-400 text-center font-semibold">{{ $away['won'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-amber-400 text-center font-semibold">{{ $away['draw'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-red-400 text-center font-semibold">{{ $away['lost'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['goals_scored'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['goals_against'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-300 text-center font-semibold">{{ $away['goals_diff'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm font-bold text-center text-white bg-gradient-to-r from-purple-600/20 to-purple-700/20 border border-purple-500/30 rounded">{{ $away['points'] ?? 0 }}</td>
                                                        <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-center form-column">
                                                            <span class="text-gray-500">-</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4 order-2">
                <x-football-standings-menu :activeLeagueId="$league['id'] ?? null" />
            </aside>
        </div>
    </div>
</div>

<script>
function showStandings(type) {
    // Hide all rows in all groups
    document.querySelectorAll('.standings-row').forEach(row => {
        row.classList.add('hidden');
    });
    
    // Show rows of selected type in all groups
    document.querySelectorAll(`.standings-row[data-type="${type}"]`).forEach(row => {
        row.classList.remove('hidden');
    });
    
    // Show/hide form column based on tab type
    const formColumns = document.querySelectorAll('.form-column');
    if (type === 'all') {
        // Show form column for "all" tab
        formColumns.forEach(col => {
            col.style.display = '';
        });
    } else {
        // Hide form column for "home" and "away" tabs
        formColumns.forEach(col => {
            col.style.display = 'none';
        });
    }
    
    // Update tab styles
    ['all', 'home', 'away'].forEach(tabType => {
        const tab = document.getElementById(`tab-${tabType}`);
        if (tab) {
            if (tabType === type) {
                tab.classList.remove('text-gray-300', 'bg-slate-700/50', 'hover:bg-slate-700', 'hover:text-white');
                tab.classList.add('text-white', 'bg-gradient-to-r', 'from-purple-600', 'to-purple-700', 'shadow-md', 'shadow-purple-500/20');
            } else {
                tab.classList.remove('text-white', 'bg-gradient-to-r', 'from-purple-600', 'to-purple-700', 'shadow-md', 'shadow-purple-500/20');
                tab.classList.add('text-gray-300', 'bg-slate-700/50', 'hover:bg-slate-700', 'hover:text-white');
            }
        }
    });
}
</script>
@endsection

