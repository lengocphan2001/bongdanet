@extends('layouts.app')

@section('title', 'Bảng xếp hạng ' . ($league['name'] ?? '') . ' - Keobongda')

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Bảng xếp hạng bóng đá', 'url' => route('standings.index')],
        ['label' => $league['name'] ?? 'N/A', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">

                {{-- Page Title --}}
                <h1 class="text-xl sm:text-2xl font-bold text-white mb-4 break-words">
                    Bảng xếp hạng {{ $league['name'] ?? 'N/A' }} - BXH bóng đá mới nhất
                </h1>

                {{-- League Selection Tabs --}}
                <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 p-3 sm:p-4 mb-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('standings.show', $league['id']) }}" 
                           class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm">
                            {{ $league['name'] ?? 'N/A' }}
                        </a>
                        {{-- Add more league tabs here if needed --}}
                    </div>
                </div>


                {{-- Standings View Tabs -- Only show for non-CUP leagues --}}
                @php
                    $hasGroups = ($leagueInfo['has_groups'] ?? 0) == 1 || count($groupedStandings ?? []) > 1;
                @endphp
                
                @if(!($isCupFormat ?? false))
                    <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 p-4 mb-4">
                        <div class="flex space-x-2">
                            <button onclick="showStandings('all')" 
                                    id="tab-all"
                                    class="px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded hover:bg-gray-700 transition-colors">
                                Tất cả
                            </button>
                            <button onclick="showStandings('home')" 
                                    id="tab-home"
                                    class="px-4 py-2 text-sm font-medium text-gray-300 bg-slate-700 border border-slate-600 rounded hover:bg-slate-600 transition-colors">
                                Sân nhà
                            </button>
                            <button onclick="showStandings('away')" 
                                    id="tab-away"
                                    class="px-4 py-2 text-sm font-medium text-gray-300 bg-slate-700 border border-slate-600 rounded hover:bg-slate-600 transition-colors">
                                Sân khách
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Standings Table --}}
                <div class="space-y-6" id="standings-container">
                    @if(empty($standings) && empty($groupedStandings))
                        <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 p-8 text-center text-gray-400">
                            Không có dữ liệu bảng xếp hạng
                        </div>
                    @else
                        @php
                            $hasGroups = ($leagueInfo['has_groups'] ?? 0) == 1 || count($groupedStandings ?? []) > 1;
                            $standingsToRender = $hasGroups ? ($groupedStandings ?? []) : ['default' => ($standings ?? [])];
                        @endphp
                        
                        @foreach($standingsToRender as $groupName => $groupTeams)
                            <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 overflow-hidden group-table" data-group="{{ $groupName }}">
                                {{-- Group Header --}}
                                @if($hasGroups && $groupName !== 'default')
                                    <div class="bg-slate-900 px-4 py-3">
                                        <h3 class="text-sm font-bold text-white uppercase">{{ $groupName }}</h3>
                                    </div>
                                @endif
                                
                                {{-- Table --}}
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-slate-700 border-b border-slate-600">
                                            <tr>
                                                <th class="px-2 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">XH</th>
                                                <th class="px-2 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Đội bóng</th>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Trận</th>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Thắng</th>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Hòa</th>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Thua</th>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">BT</th>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">BB</th>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">H/s</th>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Điểm</th>
                                                @if(!($isCupFormat ?? false))
                                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider form-column">Phong độ gần nhất</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="bg-slate-800 divide-y divide-slate-700 standings-body" data-group="{{ $groupName }}">
                                            
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
                                                <tr class="hover:bg-slate-700 standings-row {{ ($index % 2 === 0) ? 'bg-slate-800' : 'bg-slate-700' }}" data-type="all">
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm font-medium text-gray-100">{{ $position }}</td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-100">
                                                        {{ $team['team_name'] ?? 'N/A' }}
                                                        @if($status)
                                                            <span class="ml-2 text-xs text-gray-400">({{ $status }})</span>
                                                        @endif
                                                        @if($result)
                                                            <span class="ml-1 text-xs text-blue-600">{{ $result }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['games_played'] ?? 0 }}</td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['won'] ?? 0 }}</td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['draw'] ?? 0 }}</td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['lost'] ?? 0 }}</td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['goals_scored'] ?? 0 }}</td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['goals_against'] ?? 0 }}</td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $overall['goals_diff'] ?? 0 }}</td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-sm font-semibold text-center text-white">{{ $points }}</td>
                                                    @if(!($isCupFormat ?? false))
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-center form-column">
                                                            <div class="flex items-center justify-center space-x-1">
                                                                @if($recentForm)
                                                                    @foreach(str_split($recentForm) as $formResult)
                                                                        @if($formResult == 'W')
                                                                            <span class="w-6 h-6 bg-green-500 text-white text-xs font-bold rounded flex items-center justify-center">T</span>
                                                                        @elseif($formResult == 'D')
                                                                            <span class="w-6 h-6 bg-orange-500 text-white text-xs font-bold rounded flex items-center justify-center">H</span>
                                                                        @elseif($formResult == 'L')
                                                                            <span class="w-6 h-6 bg-red-500 text-white text-xs font-bold rounded flex items-center justify-center">B</span>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-gray-400">-</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                                @if(!($isCupFormat ?? false))
                                                    {{-- Home row (hidden by default) --}}
                                                    <tr class="hidden hover:bg-slate-700 standings-row {{ ($index % 2 === 0) ? 'bg-slate-800' : 'bg-slate-700' }}" data-type="home">
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm font-medium text-gray-100">{{ $home['position'] ?? $position }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-100">{{ $team['team_name'] ?? 'N/A' }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['games_played'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['won'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['draw'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['lost'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['goals_scored'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['goals_against'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $home['goals_diff'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm font-semibold text-center text-white">{{ $home['points'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-center form-column">
                                                            <span class="text-gray-400">-</span>
                                                        </td>
                                                    </tr>
                                                    {{-- Away row (hidden by default) --}}
                                                    <tr class="hidden hover:bg-slate-700 standings-row {{ ($index % 2 === 0) ? 'bg-slate-800' : 'bg-slate-700' }}" data-type="away">
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm font-medium text-gray-100">{{ $away['position'] ?? $position }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-100">{{ $team['team_name'] ?? 'N/A' }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['games_played'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['won'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['draw'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['lost'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['goals_scored'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['goals_against'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-300 text-center">{{ $away['goals_diff'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm font-semibold text-center text-white">{{ $away['points'] ?? 0 }}</td>
                                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-center form-column">
                                                            <span class="text-gray-400">-</span>
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
                tab.classList.remove('text-gray-300', 'bg-slate-700', 'border-slate-600');
                tab.classList.add('text-white', 'bg-gray-600');
            } else {
                tab.classList.remove('text-white', 'bg-gray-600');
                tab.classList.add('text-gray-300', 'bg-slate-700', 'border', 'border-slate-600');
            }
        }
    });
}
</script>
@endsection

