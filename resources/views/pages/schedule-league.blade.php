@extends('layouts.app')

@section('title', 'Lịch thi đấu ' . ($league['name'] ?? '') . ' - Bongdanet')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'BONGDANET', 'url' => route('home')],
        ['label' => 'Lịch thi đấu', 'url' => route('schedule')],
        ['label' => $league['name'] ?? 'N/A', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0">

                {{-- Page Title --}}
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Lịch thi đấu {{ $league['name'] ?? 'N/A' }} {{ date('Y') }} - Lịch bóng đá {{ $league['country_name'] ?? '' }} mới nhất
                </h1>

                {{-- League Selection Tabs --}}
                @php
                    $leagueFilters = [
                        ['name' => 'Cúp C1', 'id' => 539],
                        ['name' => 'Ngoại Hạng Anh', 'id' => 583],
                        ['name' => 'La Liga', 'id' => 637],
                        ['name' => 'VĐQG Ý', 'id' => 719],
                        ['name' => 'VĐQG Pháp', 'id' => 764],
                        ['name' => 'Cúp C2', 'id' => 541],
                        ['name' => 'Cúp C3', 'id' => 4569],
                        ['name' => 'V League', 'id' => 3748],
                        ['name' => 'VĐQG Đức', 'id' => 594],
                        ['name' => 'VĐQG Úc', 'id' => 974],
                        ['name' => 'Cúp C1 Châu Á', 'id' => 511],
                    ];
                @endphp
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="flex flex-wrap gap-2">
                        @foreach($leagueFilters as $filter)
                            <a href="{{ route('schedule.league', $filter['id']) }}" 
                               class="px-4 py-2 text-sm font-medium {{ ($filter['id'] == $leagueId) ? 'text-white bg-[#1a5f2f]' : 'text-gray-700 bg-white border border-gray-300' }} rounded hover:bg-[#155027] hover:text-white transition-colors">
                                {{ $filter['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Matchday Selector (Vòng đấu) - Only show for non-CUP leagues --}}
                @if(!empty($roundIds) && !($isCup ?? false))
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                        <div class="mb-2">
                            <span class="text-sm font-medium text-gray-700">Vòng đấu:</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($roundIds as $index => $roundId)
                                @php
                                    $roundNumber = $index + 1;
                                @endphp
                                <a href="{{ route('schedule.league', ['leagueId' => $leagueId, 'round' => $roundId]) }}" 
                                   class="px-3 py-2 text-sm font-medium {{ ($round == $roundId) ? 'text-white bg-[#1a5f2f]' : 'text-gray-900 bg-yellow-400 border border-yellow-500' }} rounded hover:bg-yellow-500 hover:text-gray-900 transition-colors">
                                    {{ $roundNumber }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Schedule Table --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-[#1a5f2f]">
                                <tr>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Thời gian</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">{{ ($isCup ?? false) ? 'Bảng' : 'Vòng' }}</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">FT</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if(empty($matchesByDate))
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                            Không có lịch thi đấu nào
                                        </td>
                                    </tr>
                                @else
                                    @foreach($matchesByDate as $matchDate => $matches)
                                        @php
                                            // Format date for display
                                            try {
                                                $dateObj = Carbon\Carbon::parse($matchDate);
                                                $dayName = $dateObj->locale('vi')->isoFormat('dddd');
                                                $formattedDate = $dateObj->format('d/m/Y');
                                            } catch (\Exception $e) {
                                                $dayName = '';
                                                $formattedDate = $matchDate;
                                            }
                                        @endphp
                                        {{-- Date Header --}}
                                        <tr class="">
                                            <td colspan="3" class="px-4 py-2">
                                                <span class="text-sm font-bold text-red-600">
                                                    {{ $dayName }}, Ngày {{ $formattedDate }}
                                                </span>
                                            </td>
                                        </tr>
                                        
                                        @foreach($matches as $match)
                                            @php
                                                $matchId = $match['match_id'] ?? null;
                                                // Format time display: "HH:mm"
                                                $timeDisplay = $match['time'] ?? '-';
                                                if (isset($match['starting_datetime']) && $match['starting_datetime']) {
                                                    try {
                                                        $dt = Carbon\Carbon::parse($match['starting_datetime']);
                                                        $timeDisplay = $dt->format('H:i');
                                                    } catch (\Exception $e) {
                                                        // Keep original timeDisplay
                                                    }
                                                }
                                                // For CUP: use round_name, for regular leagues: use round number
                                                if ($isCup ?? false) {
                                                    $roundName = $match['round_name'] ?? $match['round'] ?? '-';
                                                    if (empty($roundName) || is_numeric($roundName)) {
                                                        $roundName = $match['round_name'] ?? '-';
                                                    }
                                                } else {
                                                    $roundId = $match['round_id'] ?? null;
                                                    $roundNumber = '-';
                                                    if ($roundId && isset($roundIds)) {
                                                        $roundIndex = array_search($roundId, $roundIds);
                                                        if ($roundIndex !== false) {
                                                            $roundNumber = $roundIndex + 1;
                                                        }
                                                    }
                                                    $roundName = $roundNumber;
                                                }
                                            @endphp
                                            <tr class="hover:bg-gray-50 {{ ($loop->index % 2 === 0) ? 'bg-white' : 'bg-gray-50' }} {{ $matchId ? 'cursor-pointer' : '' }}" 
                                                @if($matchId) onclick="window.location.href='{{ route('match.detail', $matchId) }}'" @endif>
                                                <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900 text-left">
                                                    {{ $timeDisplay }}
                                                </td>
                                                <td>
                                                    <div class="text-xs text-gray-600 font-medium">{{ $roundName }}</div>
                                                </td>
                                                <td class="px-2 py-3 text-sm text-gray-900 text-center text-left">
                                                    <div class="space-y-1">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <span class="text-right flex-1">{{ $match['home_team'] ?? '-' }}</span>
                                                            @if($matchId)
                                                                <a href="{{ route('match.detail', $matchId) }}" 
                                                                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-2 py-1 rounded min-w-[50px] text-center transition-colors flex-shrink-0">
                                                                    ? - ?
                                                                </a>
                                                            @else
                                                                <span class="bg-green-600 text-white text-sm font-bold px-2 py-1 rounded min-w-[50px] text-center flex-shrink-0">
                                                                    ? - ?
                                                                </span>
                                                            @endif
                                                            <span class="text-left flex-1">{{ $match['away_team'] ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-fit lg:w-80 flex-shrink-0 space-y-4">
                {{-- LỊCH THI ĐẤU Menu --}}
                <x-football-schedule-menu :activeLeagueId="$leagueId" />

                {{-- Condensed Schedule by Date --}}
                @if(!empty($matchesByDate))
                    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden w-full">
                        {{-- Header --}}
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-1 h-5 bg-green-600"></div>
                                <h2 class="text-sm font-bold text-black uppercase">Lịch thi đấu {{ $league['name'] ?? '' }}</h2>
                            </div>
                        </div>
                        
                        {{-- Schedule List --}}
                        <div class="bg-white">
                            <table class="w-full table-fixed">
                                <colgroup>
                                    <col style="width: auto;">
                                    <col style="width: 60px;">
                                    <col style="width: auto;">
                                </colgroup>
                                <tbody>
                                    @foreach(array_slice($matchesByDate, 0, 2) as $matchDate => $matches)
                                        @php
                                            try {
                                                $dateObj = Carbon\Carbon::parse($matchDate);
                                                $formattedDate = $dateObj->format('d/m/Y');
                                            } catch (\Exception $e) {
                                                $formattedDate = $matchDate;
                                            }
                                        @endphp
                                        
                                        {{-- Date Header Row --}}
                                        <tr>
                                            <td colspan="3" class="bg-gray-400 px-4 py-2">
                                                <h4 class="text-sm font-medium text-white">Ngày {{ $formattedDate }}</h4>
                                            </td>
                                        </tr>
                                        
                                        {{-- Match Schedule Rows --}}
                                        @foreach(array_slice($matches, 0, 5) as $match)
                                            @php
                                                $matchId = $match['match_id'] ?? null;
                                                $timeDisplay = $match['time'] ?? '-';
                                                if (isset($match['starting_datetime']) && $match['starting_datetime']) {
                                                    try {
                                                        $dt = Carbon\Carbon::parse($match['starting_datetime']);
                                                        $timeDisplay = $dt->format('H:i');
                                                    } catch (\Exception $e) {
                                                    }
                                                }
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                                <td class="text-xs text-gray-900 text-right">
                                                    @if($matchId)
                                                        <a href="{{ route('match.detail', $matchId) }}" class="hover:text-[#1a5f2f]">
                                                            {{ $match['home_team'] ?? '-' }}
                                                        </a>
                                                    @else
                                                        {{ $match['home_team'] ?? '-' }}
                                                    @endif
                                                </td>
                                                <td class="px-2 py-2 text-xs font-medium text-green-600 text-center whitespace-nowrap">
                                                    {{ $timeDisplay }}
                                                </td>
                                                <td class="text-xs text-gray-900 text-left">
                                                    @if($matchId)
                                                        <a href="{{ route('match.detail', $matchId) }}" class="hover:text-[#1a5f2f]">
                                                            {{ $match['away_team'] ?? '-' }}
                                                        </a>
                                                    @else
                                                        {{ $match['away_team'] ?? '-' }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>
@endsection

