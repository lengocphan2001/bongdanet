@extends('layouts.app')

@section('title', 'keobongda.co - Kết quả trận ' . ($homeTeam['name'] ?? '') . ' vs ' . ($awayTeam['name'] ?? ''))

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Kết quả bóng đá', 'url' => route('results')],
        ['label' => ($homeTeam['name'] ?? '') . ' vs ' . ($awayTeam['name'] ?? ''), 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        {{-- Main Container --}}
        <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">
            {{-- Page Title --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                    <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">
                        Kết quả trận {{ $homeTeam['name'] ?? '' }} vs {{ $awayTeam['name'] ?? '' }}
                    </span>
                </h1>
            </div>

            {{-- Match Summary Box --}}
            <div class="bg-gradient-to-br from-slate-900/95 to-slate-950/95 rounded-xl shadow-xl border border-slate-700/50 mb-6 p-4 sm:p-6 backdrop-blur-sm">
                {{-- League/Stage Info --}}
                <div class="text-center mb-6">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 text-xs font-semibold">
                        KẾT QUẢ
                    </span>
                    <p class="text-xs sm:text-sm font-medium text-gray-300 mt-2 break-words">
                        {{ $league['name'] ?? '' }} {{ $match['stage_name'] ?? '' }} • {{ $displayTime ? str_replace(':', 'h', $displayTime) : '' }} Ngày {{ $displayDate ?? '' }}/{{ !empty($matchDate) ? date('Y', strtotime($matchDate)) : date('Y') }}
                    </p>
                </div>

                {{-- Teams and Score --}}
                <div class="flex flex-col sm:flex-row items-center justify-between mb-6 space-y-6 sm:space-y-0">
                    {{-- Home Team --}}
                    <div class="flex-1 flex flex-col items-center w-full sm:w-auto">
                        @if (!empty($homeTeam['img']))
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-slate-800/50 border-2 border-slate-700/50 p-2 mb-3 flex items-center justify-center hover:border-emerald-500/50 transition-all duration-200">
                                <img src="{{ $homeTeam['img'] }}" alt="{{ $homeTeam['name'] ?? '' }}" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 border-2 border-slate-700/50 mb-3 flex items-center justify-center text-white text-xl font-bold">
                                {{ substr($homeTeam['name'] ?? 'H', 0, 1) }}
                            </div>
                        @endif
                        <span class="text-white font-semibold text-center text-sm sm:text-base break-words px-2">{{ $homeTeam['name'] ?? '' }}</span>
                    </div>

                    {{-- Score --}}
                    <div class="flex-1 flex flex-col items-center mx-0 sm:mx-4 w-full sm:w-auto">
                        <div class="text-3xl sm:text-5xl md:text-6xl font-black mb-3 bg-gradient-to-r from-emerald-400 via-green-400 to-emerald-500 bg-clip-text text-transparent">
                            {{ ($scores['home_score'] ?? '') !== '' ? ($scores['home_score'] ?? '?') : '?' }} - {{ ($scores['away_score'] ?? '') !== '' ? ($scores['away_score'] ?? '?') : '?' }}
                        </div>
                        @if ($match['status'] == 1 || $match['status_name'] == 'Inplay')
                            <a href="{{ route('livescore') }}" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-200 shadow-lg shadow-red-500/25 hover:shadow-red-500/40 hover:scale-105 mb-2">
                                🔴 Xem Live
                            </a>
                        @endif
                        @if (!empty($scores['ht_score']))
                            <p class="text-gray-400 text-sm font-medium">(HT: {{ $scores['ht_score'] }})</p>
                        @else
                            <p class="text-gray-400 text-sm font-medium">(HT: 0-0)</p>
                        @endif
                    </div>

                    {{-- Away Team --}}
                    <div class="flex-1 flex flex-col items-center w-full sm:w-auto">
                        @if (!empty($awayTeam['img']))
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-slate-800/50 border-2 border-slate-700/50 p-2 mb-3 flex items-center justify-center hover:border-emerald-500/50 transition-all duration-200">
                                <img src="{{ $awayTeam['img'] }}" alt="{{ $awayTeam['name'] ?? '' }}" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 border-2 border-slate-700/50 mb-3 flex items-center justify-center text-white text-xl font-bold">
                                {{ substr($awayTeam['name'] ?? 'A', 0, 1) }}
                            </div>
                        @endif
                        <span class="text-white font-semibold text-center text-sm sm:text-base break-words px-2">{{ $awayTeam['name'] ?? '' }}</span>
                    </div>
                </div>

                {{-- Match Details --}}
                <div class="flex flex-wrap justify-center gap-4 sm:gap-6 text-gray-300 text-xs sm:text-sm bg-slate-800/50 rounded-lg p-3 border border-slate-700/50">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="font-medium text-emerald-400">Địa điểm:</span>
                        <span>
                            @if ($venue && isset($venue['name']))
                                {{ $venue['name'] }}
                                @if (isset($venue['city']))
                                    , {{ $venue['city'] }}
                                @endif
                                @if (isset($venue['country']['name']))
                                    , {{ $venue['country']['name'] }}
                                @endif
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                        </svg>
                        <span class="font-medium text-emerald-400">Thời tiết:</span>
                        <span>{{ $match['weather_report']['condition'] ?? 'Ít Mây' }}, {{ $match['weather_report']['temp'] ?? '12' }}°C</span>
                    </div>
                </div>
            </div>

            {{-- Match Events Section --}}
            <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 rounded-xl shadow-xl border border-slate-700/50 p-4 sm:p-6 mb-6 backdrop-blur-sm" data-section="match-events">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                    <h2 class="text-base sm:text-lg font-bold text-white">Diễn biến - Kết quả {{ $homeTeam['name'] ?? '' }} vs {{ $awayTeam['name'] ?? '' }}</h2>
                </div>
            
            @php
                // Use matchEvents from API match_events endpoint
                $matchEvents = $matchEvents ?? [];
                $homeTeamId = $homeTeam['id'] ?? null;
                $awayTeamId = $awayTeam['id'] ?? null;
                
                // Filter and sort events
                $filteredEvents = [];
                if (is_array($matchEvents)) {
                    foreach ($matchEvents as $event) {
                        $eventType = $event['type'] ?? '';
                        // Only show relevant events: goals, cards, substitutions
                        if (in_array($eventType, ['goal', 'yellowcard', 'redcard', 'yellowredcard', 'substitution'])) {
                            $filteredEvents[] = $event;
                        }
                    }
                    
                    // Sort by minute
                    usort($filteredEvents, function($a, $b) {
                        $minuteA = (int)($a['minute'] ?? 0);
                        $minuteB = (int)($b['minute'] ?? 0);
                        if ($minuteA === $minuteB) {
                            $extraA = (int)($a['extra_minute'] ?? 0);
                            $extraB = (int)($b['extra_minute'] ?? 0);
                            return $extraA <=> $extraB;
                        }
                        return $minuteA <=> $minuteB;
                    });
                }
            @endphp
            
                @if(empty($filteredEvents))
                    <div class="bg-gradient-to-r from-slate-700/50 to-slate-800/50 border border-red-500/50 rounded-lg p-6 text-center backdrop-blur-sm">
                        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-400 font-medium">Trận đấu chưa có dữ liệu !</p>
                    </div>
                @else
                    <div class="border border-slate-700/50 rounded-xl overflow-hidden bg-slate-900/50 backdrop-blur-sm">
                        <table class="w-full text-xs sm:text-sm">
                            <thead class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b border-slate-600/50 backdrop-blur-sm">
                            <tr>
                                <th class="px-3 sm:px-4 py-3 text-left">
                                    <div class="flex items-center space-x-2">
                                        @if (!empty($homeTeam['img']))
                                            <div class="w-6 h-6 rounded-full bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center">
                                                <img src="{{ $homeTeam['img'] }}" alt="{{ $homeTeam['name'] ?? '' }}" class="w-full h-full object-contain">
                                            </div>
                                        @endif
                                        <span class="font-semibold text-gray-200">{{ $homeTeam['name'] ?? '' }}</span>
                                    </div>
                                </th>
                                <th class="px-3 sm:px-4 py-3 text-center w-16 font-semibold text-gray-200">Phút</th>
                                <th class="px-3 sm:px-4 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <span class="font-semibold text-gray-200">{{ $awayTeam['name'] ?? '' }}</span>
                                        @if (!empty($awayTeam['img']))
                                            <div class="w-6 h-6 rounded-full bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center">
                                                <img src="{{ $awayTeam['img'] }}" alt="{{ $awayTeam['name'] ?? '' }}" class="w-full h-full object-contain">
                                            </div>
                                        @endif
                                    </div>
                                </th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-slate-700/50">
                                @foreach($filteredEvents as $event)
                                @php
                                    $eventType = $event['type'] ?? '';
                                    $eventTeamId = $event['team_id'] ?? null;
                                    $playerName = $event['player_name'] ?? '';
                                    $relatedPlayerName = $event['related_player_name'] ?? '';
                                    $minute = $event['minute'] ?? '';
                                    $extraMinute = $event['extra_minute'] ?? '';
                                    $minuteDisplay = $minute . ($extraMinute ? '+' . $extraMinute : '');
                                    
                                    $isHomeEvent = ($eventTeamId == $homeTeamId);
                                    $isAwayEvent = ($eventTeamId == $awayTeamId);
                                    
                                    // Determine event icon and color
                                    $eventIcon = '';
                                    $eventColor = '';
                                    $eventText = $playerName ?: 'N/A';
                                    
                                    if ($eventType === 'goal') {
                                        $eventIcon = '⚽';
                                        $eventColor = 'text-blue-600';
                                        if ($relatedPlayerName) {
                                            $eventText .= ' (Assist: ' . $relatedPlayerName . ')';
                                        }
                                    } elseif ($eventType === 'yellowcard') {
                                        $eventIcon = '🟨';
                                        $eventColor = 'text-yellow-600';
                                    } elseif ($eventType === 'redcard') {
                                        $eventIcon = '🟥';
                                        $eventColor = 'text-red-400';
                                    } elseif ($eventType === 'yellowredcard') {
                                        $eventIcon = '🟨🟥';
                                        $eventColor = 'text-orange-600';
                                    } elseif ($eventType === 'substitution') {
                                        $eventIcon = '🔄';
                                        $eventColor = 'text-green-600';
                                        if ($relatedPlayerName) {
                                            $eventText = $relatedPlayerName . ' → ' . $playerName;
                                        }
                                    }
                                @endphp
                                    <tr class="hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200">
                                        @if($isHomeEvent)
                                            <td class="px-3 sm:px-4 py-3">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-base sm:text-lg">{{ $eventIcon }}</span>
                                                    <span class="{{ $eventColor }} font-medium">{{ $eventText }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 sm:px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-500/20 text-blue-400 text-xs font-semibold">{{ $minuteDisplay }}</span>
                                            </td>
                                            <td class="px-3 sm:px-4 py-3"></td>
                                        @elseif($isAwayEvent)
                                            <td class="px-3 sm:px-4 py-3"></td>
                                            <td class="px-3 sm:px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-500/20 text-blue-400 text-xs font-semibold">{{ $minuteDisplay }}</span>
                                            </td>
                                            <td class="px-3 sm:px-4 py-3 text-right">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <span class="{{ $eventColor }} font-medium">{{ $eventText }}</span>
                                                    <span class="text-base sm:text-lg">{{ $eventIcon }}</span>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
                {{-- Event Legend --}}
                <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-400 bg-slate-800/50 rounded-lg p-3 border border-slate-700/50">
                    <div class="flex items-center space-x-1.5">
                        <span class="text-base">⚽</span>
                        <span>Bàn thắng</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="text-base">🟨</span>
                        <span>Thẻ vàng</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="text-base">🟥</span>
                        <span>Thẻ đỏ</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="text-base">🔄</span>
                        <span>Thay người</span>
                    </div>
                </div>
            </div>

            {{-- Statistics Section --}}
            <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 rounded-xl shadow-xl border border-slate-700/50 p-4 sm:p-6 mb-6 backdrop-blur-sm" data-section="match-stats">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                    <h2 class="text-base sm:text-lg font-bold text-white">Thống kê kỹ thuật {{ $homeTeam['name'] ?? '' }} VS {{ $awayTeam['name'] ?? '' }}</h2>
                </div>
            
                @if(empty($homeMatchStats) && empty($awayMatchStats))
                    <div class="bg-gradient-to-r from-slate-700/50 to-slate-800/50 border border-red-500/50 rounded-lg p-6 text-center backdrop-blur-sm">
                        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-400 font-medium">Chưa có bảng thống kê số liệu trận đấu !</p>
                    </div>
                @else
                    {{-- Header with team names and logos --}}
                    <div class="bg-gradient-to-r from-slate-800/90 to-slate-700/90 text-white px-4 py-3 flex items-center justify-between mb-3 rounded-lg border border-slate-700/50 backdrop-blur-sm">
                        <div class="flex items-center space-x-2 flex-1 min-w-0">
                            @if (!empty($homeTeam['img']))
                                <div class="w-8 h-8 rounded-full bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0">
                                    <img src="{{ $homeTeam['img'] }}" alt="{{ $homeTeam['name'] ?? '' }}" class="w-full h-full object-contain rounded-full">
                                </div>
                            @endif
                            <span class="font-semibold text-gray-200 break-words break-all">{{ $homeTeam['name'] ?? '' }}</span>
                        </div>
                        <div class="flex items-center space-x-2 flex-1 min-w-0 justify-end">
                            @if (!empty($awayTeam['img']))
                                <div class="w-8 h-8 rounded-full bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0">
                                    <img src="{{ $awayTeam['img'] }}" alt="{{ $awayTeam['name'] ?? '' }}" class="w-full h-full object-contain rounded-full">
                                </div>
                            @endif
                            <span class="font-semibold text-gray-200 break-words break-all text-right">{{ $awayTeam['name'] ?? '' }}</span>
                        </div>
                    </div>

                    {{-- Statistics Rows --}}
                    <div class="space-y-2">
                    @php
                        // Helper function to calculate percentage for bar
                        function calculatePercentage($home, $away) {
                            $total = $home + $away;
                            if ($total == 0) return ['home' => 50, 'away' => 50];
                            return [
                                'home' => ($home / $total) * 100,
                                'away' => ($away / $total) * 100
                            ];
                        }
                        
                        // Helper function to format stat value
                        function formatStatValue($value) {
                            if ($value === null || $value === '') return '0';
                            if (is_numeric($value)) {
                                // If it's a percentage, show as percentage
                                if ($value <= 100 && strpos((string)$value, '.') !== false) {
                                    return number_format((float)$value, 0) . '%';
                                }
                                return number_format((float)$value, 0);
                            }
                            return $value;
                        }
                        
                        $stats = [
                            ['key' => 'corners', 'label' => 'Phạt góc', 'home' => $homeMatchStats['corners'] ?? 0, 'away' => $awayMatchStats['corners'] ?? 0],
                            ['key' => 'corners_ht', 'label' => 'Phạt góc (Hiệp 1)', 'home' => 0, 'away' => 0], // Will calculate from events
                            ['key' => 'yellowcards', 'label' => 'Thẻ vàng', 'home' => $homeMatchStats['yellowcards'] ?? 0, 'away' => $awayMatchStats['yellowcards'] ?? 0],
                            ['key' => 'shots_total', 'label' => 'Tổng cú sút', 'home' => $homeMatchStats['shots_total'] ?? 0, 'away' => $awayMatchStats['shots_total'] ?? 0],
                            ['key' => 'shots_on_target', 'label' => 'Sút trúng cầu môn', 'home' => $homeMatchStats['shots_on_target'] ?? 0, 'away' => $awayMatchStats['shots_on_target'] ?? 0],
                            ['key' => 'shots_off_target', 'label' => 'Sút ra ngoài', 'home' => $homeMatchStats['shots_off_target'] ?? 0, 'away' => $awayMatchStats['shots_off_target'] ?? 0],
                            ['key' => 'possessionpercent', 'label' => 'Kiểm soát bóng', 'home' => $homeMatchStats['possessionpercent'] ?? 0, 'away' => $awayMatchStats['possessionpercent'] ?? 0, 'is_percentage' => true],
                            // Note: Possession for first half is not available in stats API, will show 0 or skip if not available
                            ['key' => 'attacks', 'label' => 'Pha tấn công', 'home' => $homeMatchStats['attacks'] ?? 0, 'away' => $awayMatchStats['attacks'] ?? 0],
                            ['key' => 'dangerous_attacks', 'label' => 'Tấn công nguy hiểm', 'home' => $homeMatchStats['dangerous_attacks'] ?? 0, 'away' => $awayMatchStats['dangerous_attacks'] ?? 0],
                            ['key' => 'key_passes', 'label' => 'Đường chuyền chủ chốt', 'home' => $homeMatchStats['key_passes'] ?? 0, 'away' => $awayMatchStats['key_passes'] ?? 0],
                            ['key' => 'passing_accuracy', 'label' => 'Độ chính xác chuyền bóng', 'home' => $homeMatchStats['passing_accuracy'] ?? 0, 'away' => $awayMatchStats['passing_accuracy'] ?? 0, 'is_percentage' => true],
                            ['key' => 'fouls', 'label' => 'Phạm lỗi', 'home' => $homeMatchStats['fouls'] ?? 0, 'away' => $awayMatchStats['fouls'] ?? 0],
                            ['key' => 'offsides', 'label' => 'Việt vị', 'home' => $homeMatchStats['offsides'] ?? 0, 'away' => $awayMatchStats['offsides'] ?? 0],
                            ['key' => 'crosses', 'label' => 'Tạt bóng', 'home' => $homeMatchStats['crosses'] ?? 0, 'away' => $awayMatchStats['crosses'] ?? 0],
                            ['key' => 'crossing_accuracy', 'label' => 'Độ chính xác tạt bóng', 'home' => $homeMatchStats['crossing_accuracy'] ?? 0, 'away' => $awayMatchStats['crossing_accuracy'] ?? 0, 'is_percentage' => true],
                            ['key' => 'shots_blocked', 'label' => 'Cú sút bị chặn', 'home' => $homeMatchStats['shots_blocked'] ?? 0, 'away' => $awayMatchStats['shots_blocked'] ?? 0],
                            ['key' => 'yellowredcards', 'label' => 'Thẻ vàng đỏ', 'home' => $homeMatchStats['yellowredcards'] ?? 0, 'away' => $awayMatchStats['yellowredcards'] ?? 0],
                            ['key' => 'redcards', 'label' => 'Thẻ đỏ', 'home' => $homeMatchStats['redcards'] ?? 0, 'away' => $awayMatchStats['redcards'] ?? 0],
                            ['key' => 'substitutions', 'label' => 'Thay người', 'home' => $homeMatchStats['substitutions'] ?? 0, 'away' => $awayMatchStats['substitutions'] ?? 0],
                            ['key' => 'ball_safe', 'label' => 'Bóng an toàn', 'home' => $homeMatchStats['ball_safe'] ?? 0, 'away' => $awayMatchStats['ball_safe'] ?? 0],
                            ['key' => 'goals', 'label' => 'Bàn thắng', 'home' => $homeMatchStats['goals'] ?? 0, 'away' => $awayMatchStats['goals'] ?? 0],
                            ['key' => 'penalties', 'label' => 'Phạt đền', 'home' => $homeMatchStats['penalties'] ?? 0, 'away' => $awayMatchStats['penalties'] ?? 0],
                            ['key' => 'xg', 'label' => 'xG', 'home' => $homeMatchStats['xg'] ?? 0, 'away' => $awayMatchStats['xg'] ?? 0],
                        ];
                        
                        // Calculate corners for first half from events
                        $homeCornersHt = 0;
                        $awayCornersHt = 0;
                        $matchEventsForStats = $matchEvents ?? [];
                        if (is_array($matchEventsForStats)) {
                            foreach ($matchEventsForStats as $event) {
                                if (($event['type'] ?? '') === 'corner' && strpos(strtolower($event['period'] ?? ''), '1st half') !== false) {
                                    $eventTeamId = $event['team_id'] ?? null;
                                    if ($eventTeamId == ($homeTeam['id'] ?? null)) {
                                        $homeCornersHt++;
                                    } elseif ($eventTeamId == ($awayTeam['id'] ?? null)) {
                                        $awayCornersHt++;
                                    }
                                }
                            }
                        }
                        $stats[1]['home'] = $homeCornersHt;
                        $stats[1]['away'] = $awayCornersHt;
                    @endphp
                    
                    @foreach($stats as $stat)
                        @php
                            $homeValue = (float)($stat['home'] ?? 0);
                            $awayValue = (float)($stat['away'] ?? 0);
                            $isPercentage = $stat['is_percentage'] ?? false;
                            
                            // Skip if both values are 0 and it's not a percentage stat
                            if (!$isPercentage && $homeValue == 0 && $awayValue == 0) {
                                continue;
                            }
                            
                            // For percentage stats, use the values directly
                            if ($isPercentage) {
                                $homePercent = $homeValue;
                                $awayPercent = $awayValue;
                            } else {
                                // For other stats, calculate percentage
                                $total = $homeValue + $awayValue;
                                if ($total > 0) {
                                    $homePercent = ($homeValue / $total) * 100;
                                    $awayPercent = ($awayValue / $total) * 100;
                                } else {
                                    $homePercent = 50;
                                    $awayPercent = 50;
                                }
                            }
                            
                            // Format display values
                            if ($isPercentage) {
                                $homeDisplay = number_format($homeValue, 0) . '%';
                                $awayDisplay = number_format($awayValue, 0) . '%';
                            } else {
                                $homeDisplay = number_format($homeValue, 0);
                                $awayDisplay = number_format($awayValue, 0);
                            }
                        @endphp
                            <div class="flex items-center py-2 border-b border-slate-700/50 hover:bg-slate-800/30 transition-colors rounded px-2">
                                {{-- Home value --}}
                                <div class="w-16 sm:w-20 text-right text-xs sm:text-sm font-semibold text-gray-200 pr-2">
                                    {{ $homeDisplay }}
                                </div>
                                
                                {{-- Home bar --}}
                                <div class="flex-1 relative h-5 mx-2 bg-slate-700/50 rounded-full overflow-hidden">
                                    <div class="absolute left-0 top-0 h-full bg-gradient-to-r from-emerald-500 to-green-600 rounded-full shadow-lg shadow-emerald-500/25" style="width: {{ $homePercent }}%"></div>
                                </div>
                                
                                {{-- Stat label --}}
                                <div class="w-32 sm:w-40 text-center text-xs sm:text-sm font-medium text-gray-300 px-2">
                                    {{ $stat['label'] }}
                                </div>
                                
                                {{-- Away bar --}}
                                <div class="flex-1 relative h-5 mx-2 bg-slate-700/50 rounded-full overflow-hidden">
                                    <div class="absolute right-0 top-0 h-full bg-gradient-to-l from-amber-500 to-orange-600 rounded-full shadow-lg shadow-amber-500/25" style="width: {{ $awayPercent }}%"></div>
                                </div>
                                
                                {{-- Away value --}}
                                <div class="w-16 sm:w-20 text-left text-xs sm:text-sm font-semibold text-gray-200 pl-2">
                                    {{ $awayDisplay }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Lineup Section --}}
            <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 rounded-xl shadow-xl border border-slate-700/50 p-4 sm:p-6 mb-6 backdrop-blur-sm" data-section="match-lineups">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-green-600 rounded-full"></div>
                    <h2 class="text-base sm:text-lg font-bold text-white">Đội hình xuất phát</h2>
                </div>
            
                @if(empty($matchLineups))
                    <div class="bg-gradient-to-r from-slate-700/50 to-slate-800/50 border border-red-500/50 rounded-lg p-6 text-center backdrop-blur-sm">
                        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-400 font-medium">Chưa có thông tin đội hình ra sân !</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        {{-- Home Team Lineup --}}
                        @if(isset($matchLineups['home']))
                            @php
                                $homeLineup = $matchLineups['home'];
                                $homeFormation = $homeLineup['formation'] ?? '';
                                $homeCoach = $homeLineup['coach'] ?? null;
                                $homeSquad = $homeLineup['squad'] ?? [];
                            @endphp
                            <div class="border border-slate-700/50 rounded-xl p-4 bg-slate-900/50 backdrop-blur-sm">
                                <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-700/50">
                                    <div class="flex items-center space-x-2">
                                        @if (!empty($homeTeam['img']))
                                            <div class="w-8 h-8 rounded-full bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center">
                                                <img src="{{ $homeTeam['img'] }}" alt="{{ $homeTeam['name'] ?? '' }}" class="w-full h-full object-contain rounded-full">
                                            </div>
                                        @endif
                                        <h3 class="text-sm sm:text-base font-bold text-white">{{ $homeTeam['name'] ?? '' }}</h3>
                                    </div>
                                    @if($homeFormation)
                                        <span class="bg-gradient-to-r from-emerald-600/20 to-green-600/20 border border-emerald-500/30 px-3 py-1 rounded-lg text-xs font-semibold text-emerald-400">{{ $homeFormation }}</span>
                                    @endif
                                </div>
                                @if($homeCoach)
                                    <p class="text-xs text-gray-400 mb-3 flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                        </svg>
                                        <span class="font-medium">HLV:</span> {{ $homeCoach['name'] ?? '' }}
                                    </p>
                                @endif
                                <div class="space-y-1.5">
                                    @foreach($homeSquad as $player)
                                        @php
                                            $playerInfo = $player['player'] ?? [];
                                            $playerName = $playerInfo['name'] ?? $playerInfo['common_name'] ?? '';
                                            $playerNumber = $player['number'] ?? '';
                                            $position = $player['position'] ?? '';
                                            $positionName = $player['position_name'] ?? '';
                                            $isCaptain = $player['captain'] ?? false;
                                        @endphp
                                        <div class="flex items-center space-x-2 p-2 hover:bg-slate-800/50 rounded-lg transition-colors border border-transparent hover:border-slate-700/50">
                                            <span class="w-8 text-center font-bold text-emerald-400 text-xs sm:text-sm bg-emerald-500/10 px-1.5 py-0.5 rounded">{{ $playerNumber }}</span>
                                            @if (!empty($playerInfo['img']))
                                                <div class="w-8 h-8 rounded-full bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0">
                                                    <img src="{{ $playerInfo['img'] }}" alt="{{ $playerName }}" class="w-full h-full object-contain rounded-full">
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ substr($playerName ?: 'P', 0, 1) }}</div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-1.5">
                                                    <span class="text-xs sm:text-sm font-medium text-gray-200 truncate">{{ $playerName ?: 'N/A' }}</span>
                                                    @if($isCaptain)
                                                        <span class="text-[10px] bg-gradient-to-r from-yellow-500/20 to-amber-500/20 border border-yellow-500/30 text-yellow-400 px-1.5 py-0.5 rounded font-semibold flex-shrink-0">C</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-gray-400">{{ $positionName }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Away Team Lineup --}}
                        @if(isset($matchLineups['away']))
                            @php
                                $awayLineup = $matchLineups['away'];
                                $awayFormation = $awayLineup['formation'] ?? '';
                                $awayCoach = $awayLineup['coach'] ?? null;
                                $awaySquad = $awayLineup['squad'] ?? [];
                            @endphp
                            <div class="border border-slate-700/50 rounded-xl p-4 bg-slate-900/50 backdrop-blur-sm">
                                <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-700/50">
                                    <div class="flex items-center space-x-2">
                                        @if (!empty($awayTeam['img']))
                                            <div class="w-8 h-8 rounded-full bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center">
                                                <img src="{{ $awayTeam['img'] }}" alt="{{ $awayTeam['name'] ?? '' }}" class="w-full h-full object-contain rounded-full">
                                            </div>
                                        @endif
                                        <h3 class="text-sm sm:text-base font-bold text-white">{{ $awayTeam['name'] ?? '' }}</h3>
                                    </div>
                                    @if($awayFormation)
                                        <span class="bg-gradient-to-r from-amber-600/20 to-orange-600/20 border border-amber-500/30 px-3 py-1 rounded-lg text-xs font-semibold text-amber-400">{{ $awayFormation }}</span>
                                    @endif
                                </div>
                                @if($awayCoach)
                                    <p class="text-xs text-gray-400 mb-3 flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                        </svg>
                                        <span class="font-medium">HLV:</span> {{ $awayCoach['name'] ?? '' }}
                                    </p>
                                @endif
                                <div class="space-y-1.5">
                                    @foreach($awaySquad as $player)
                                        @php
                                            $playerInfo = $player['player'] ?? [];
                                            $playerName = $playerInfo['name'] ?? $playerInfo['common_name'] ?? '';
                                            $playerNumber = $player['number'] ?? '';
                                            $position = $player['position'] ?? '';
                                            $positionName = $player['position_name'] ?? '';
                                            $isCaptain = $player['captain'] ?? false;
                                        @endphp
                                        <div class="flex items-center space-x-2 p-2 hover:bg-slate-800/50 rounded-lg transition-colors border border-transparent hover:border-slate-700/50">
                                            <span class="w-8 text-center font-bold text-amber-400 text-xs sm:text-sm bg-amber-500/10 px-1.5 py-0.5 rounded">{{ $playerNumber }}</span>
                                            @if (!empty($playerInfo['img']))
                                                <div class="w-8 h-8 rounded-full bg-slate-800/50 border border-slate-700/50 p-0.5 flex items-center justify-center flex-shrink-0">
                                                    <img src="{{ $playerInfo['img'] }}" alt="{{ $playerName }}" class="w-full h-full object-contain rounded-full">
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 border border-slate-700/50 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ substr($playerName ?: 'P', 0, 1) }}</div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-1.5">
                                                    <span class="text-xs sm:text-sm font-medium text-gray-200 truncate">{{ $playerName ?: 'N/A' }}</span>
                                                    @if($isCaptain)
                                                        <span class="text-[10px] bg-gradient-to-r from-yellow-500/20 to-amber-500/20 border border-yellow-500/30 text-yellow-400 px-1.5 py-0.5 rounded font-semibold flex-shrink-0">C</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-gray-400">{{ $positionName }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';
    
    const matchId = {{ $matchId ?? $id ?? 'null' }};
    if (!matchId) return;
    
    // Check if match is live or upcoming (status 0 or 1)
    const matchStatus = {{ $match['status'] ?? 0 }};
    const isLive = matchStatus === 1;
    const isUpcoming = matchStatus === 0;
    
    // Only auto-refresh if match is live or upcoming
    if (!isLive && !isUpcoming) return;
    
    // Container IDs for updating
    const containers = {
        matchSummary: document.querySelector('.bg-\\[\\#1a5f2f\\]'),
        matchEvents: document.querySelector('[data-section="match-events"]') || document.querySelector('.bg-slate-800.rounded-lg.shadow-sm.border.border-slate-700.p-6.mb-6:nth-of-type(2)'),
        matchStats: document.querySelector('[data-section="match-stats"]') || document.querySelector('.bg-slate-800.rounded-lg.shadow-sm.border.border-slate-700.p-6.mb-6:nth-of-type(3)'),
        matchLineups: document.querySelector('[data-section="match-lineups"]') || document.querySelector('.bg-slate-800.rounded-lg.shadow-sm.border.border-slate-700.p-6.mb-6:last-of-type'),
    };
    
    // Add data attributes to sections for easier targeting
    if (containers.matchEvents && !containers.matchEvents.hasAttribute('data-section')) {
        containers.matchEvents.setAttribute('data-section', 'match-events');
    }
    if (containers.matchStats && !containers.matchStats.hasAttribute('data-section')) {
        containers.matchStats.setAttribute('data-section', 'match-stats');
    }
    if (containers.matchLineups && !containers.matchLineups.hasAttribute('data-section')) {
        containers.matchLineups.setAttribute('data-section', 'match-lineups');
    }
    
    /**
     * Format date and time for display
     */
    function formatDateTime(datetime, time, date) {
        let displayDate = '';
        let displayTime = '';
        
        if (datetime) {
            try {
                const dt = new Date(datetime);
                displayDate = String(dt.getDate()).padStart(2, '0') + '/' + String(dt.getMonth() + 1).padStart(2, '0');
                displayTime = String(dt.getHours()).padStart(2, '0') + ':' + String(dt.getMinutes()).padStart(2, '0');
            } catch (e) {
                if (time) {
                    const t = new Date('2000-01-01 ' + time);
                    displayTime = String(t.getHours()).padStart(2, '0') + ':' + String(t.getMinutes()).padStart(2, '0');
                }
                if (date) {
                    const d = new Date(date);
                    displayDate = String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0');
                }
            }
        } else if (time) {
            const t = new Date('2000-01-01 ' + time);
            displayTime = String(t.getHours()).padStart(2, '0') + ':' + String(t.getMinutes()).padStart(2, '0');
            if (date) {
                const d = new Date(date);
                displayDate = String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0');
            }
        }
        
        return { displayDate, displayTime };
    }
    
    /**
     * Render match summary section
     */
    function renderMatchSummary(data) {
        if (!containers.matchSummary) return;
        
        const { match, homeTeam, awayTeam, league, scores, displayDate, displayTime, matchDate, venue } = data;
        const matchYear = matchDate ? new Date(matchDate).getFullYear() : new Date().getFullYear();
        
        // Update title
        const titleEl = containers.matchSummary.querySelector('p.text-sm');
        if (titleEl) {
            titleEl.textContent = `Kqbd ${league.name || ''} ${match.stage_name || ''} ${displayTime ? displayTime.replace(':', 'h') : ''} Ngày ${displayDate || ''}/${matchYear}`;
        }
        
        // Update scores
        const scoreEl = containers.matchSummary.querySelector('.text-4xl');
        if (scoreEl) {
            const homeScore = (scores.home_score !== '' && scores.home_score !== null) ? scores.home_score : '?';
            const awayScore = (scores.away_score !== '' && scores.away_score !== null) ? scores.away_score : '?';
            scoreEl.textContent = `${homeScore} - ${awayScore}`;
        }
        
        // Update HT score
        const htScoreEl = containers.matchSummary.querySelector('.text-white.text-sm.mt-2');
        if (htScoreEl) {
            htScoreEl.textContent = scores.ht_score ? `(${scores.ht_score})` : '(0-0)';
        }
        
        // Update venue
        const venueEl = containers.matchSummary.querySelector('.flex.justify-center.space-x-6 span:first-child span:last-child');
        if (venueEl && venue) {
            let venueText = venue.name || '-';
            if (venue.city) venueText += ', ' + venue.city;
            if (venue.country && venue.country.name) venueText += ', ' + venue.country.name;
            venueEl.textContent = venueText;
        }
    }
    
    /**
     * Render match events section
     */
    function renderMatchEvents(data) {
        if (!containers.matchEvents) return;
        
        const { matchEvents, homeTeam, awayTeam } = data;
        const homeTeamId = homeTeam.id;
        const awayTeamId = awayTeam.id;
        
        if (!matchEvents || !Array.isArray(matchEvents) || matchEvents.length === 0) {
            const tbody = containers.matchEvents.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Trận đấu chưa có dữ liệu !</td></tr>';
            }
            return;
        }
        
        // Filter and sort events
        const filteredEvents = matchEvents
            .filter(event => ['goal', 'yellowcard', 'redcard', 'yellowredcard', 'substitution'].includes(event.type))
            .sort((a, b) => {
                const minuteA = parseInt(a.minute || 0);
                const minuteB = parseInt(b.minute || 0);
                if (minuteA === minuteB) {
                    return (parseInt(a.extra_minute || 0) - parseInt(b.extra_minute || 0));
                }
                return minuteA - minuteB;
            });
        
        if (filteredEvents.length === 0) {
            const tbody = containers.matchEvents.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Trận đấu chưa có dữ liệu !</td></tr>';
            }
            return;
        }
        
        // Render events
        const tbody = containers.matchEvents.querySelector('tbody');
        if (!tbody) return;
        
        tbody.innerHTML = filteredEvents.map(event => {
            const eventType = event.type || '';
            const eventTeamId = event.team_id;
            const playerName = event.player_name || '';
            const relatedPlayerName = event.related_player_name || '';
            const minute = event.minute || '';
            const extraMinute = event.extra_minute || '';
            const minuteDisplay = minute + (extraMinute ? '+' + extraMinute : '');
            
            const isHomeEvent = (eventTeamId == homeTeamId);
            const isAwayEvent = (eventTeamId == awayTeamId);
            
            let eventIcon = '';
            let eventColor = '';
            let eventText = playerName || 'N/A';
            
            if (eventType === 'goal') {
                eventIcon = '⚽';
                eventColor = 'text-blue-600';
                if (relatedPlayerName) {
                    eventText += ' (Assist: ' + relatedPlayerName + ')';
                }
            } else if (eventType === 'yellowcard') {
                eventIcon = '🟨';
                eventColor = 'text-yellow-600';
            } else if (eventType === 'redcard') {
                eventIcon = '🟥';
                eventColor = 'text-red-400';
            } else if (eventType === 'yellowredcard') {
                eventIcon = '🟨🟥';
                eventColor = 'text-orange-600';
            } else if (eventType === 'substitution') {
                eventIcon = '🔄';
                eventColor = 'text-green-600';
                if (relatedPlayerName) {
                    eventText = relatedPlayerName + ' → ' + playerName;
                }
            }
            
            if (isHomeEvent) {
                return `
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">${eventIcon}</span>
                                <span class="${eventColor}">${eventText}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-medium">${minuteDisplay}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                `;
            } else if (isAwayEvent) {
                return `
                    <tr>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3 text-center font-medium">${minuteDisplay}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <span class="${eventColor}">${eventText}</span>
                                <span class="text-lg">${eventIcon}</span>
                            </div>
                        </td>
                    </tr>
                `;
            }
            return '';
        }).join('');
    }
    
    /**
     * Render match statistics section
     */
    function renderMatchStats(data) {
        if (!containers.matchStats) return;
        
        const { homeMatchStats, awayMatchStats, homeTeam, awayTeam, matchEvents } = data;
        
        if (!homeMatchStats && !awayMatchStats) {
            const content = containers.matchStats.querySelector('.space-y-3');
            if (content) {
                content.innerHTML = '<div class="bg-slate-700 border border-red-500 rounded-lg p-4 text-center"><p class="text-red-400 font-medium">Chưa có bảng thống kê số liệu trận đấu !</p></div>';
            }
            return;
        }
        
        // Calculate corners for first half
        let homeCornersHt = 0;
        let awayCornersHt = 0;
        if (matchEvents && Array.isArray(matchEvents)) {
            matchEvents.forEach(event => {
                if (event.type === 'corner' && event.period && event.period.toLowerCase().includes('1st half')) {
                    if (event.team_id == homeTeam.id) homeCornersHt++;
                    else if (event.team_id == awayTeam.id) awayCornersHt++;
                }
            });
        }
        
        const stats = [
            { key: 'corners', label: 'Phạt góc', home: homeMatchStats?.corners || 0, away: awayMatchStats?.corners || 0 },
            { key: 'corners_ht', label: 'Phạt góc (Hiệp 1)', home: homeCornersHt, away: awayCornersHt },
            { key: 'yellowcards', label: 'Thẻ vàng', home: homeMatchStats?.yellowcards || 0, away: awayMatchStats?.yellowcards || 0 },
            { key: 'shots_total', label: 'Tổng cú sút', home: homeMatchStats?.shots_total || 0, away: awayMatchStats?.shots_total || 0 },
            { key: 'shots_on_target', label: 'Sút trúng cầu môn', home: homeMatchStats?.shots_on_target || 0, away: awayMatchStats?.shots_on_target || 0 },
            { key: 'shots_off_target', label: 'Sút ra ngoài', home: homeMatchStats?.shots_off_target || 0, away: awayMatchStats?.shots_off_target || 0 },
            { key: 'possessionpercent', label: 'Kiểm soát bóng', home: homeMatchStats?.possessionpercent || 0, away: awayMatchStats?.possessionpercent || 0, isPercentage: true },
            { key: 'attacks', label: 'Pha tấn công', home: homeMatchStats?.attacks || 0, away: awayMatchStats?.attacks || 0 },
            { key: 'dangerous_attacks', label: 'Tấn công nguy hiểm', home: homeMatchStats?.dangerous_attacks || 0, away: awayMatchStats?.dangerous_attacks || 0 },
            { key: 'key_passes', label: 'Đường chuyền chủ chốt', home: homeMatchStats?.key_passes || 0, away: awayMatchStats?.key_passes || 0 },
            { key: 'passing_accuracy', label: 'Độ chính xác chuyền bóng', home: homeMatchStats?.passing_accuracy || 0, away: awayMatchStats?.passing_accuracy || 0, isPercentage: true },
            { key: 'fouls', label: 'Phạm lỗi', home: homeMatchStats?.fouls || 0, away: awayMatchStats?.fouls || 0 },
            { key: 'offsides', label: 'Việt vị', home: homeMatchStats?.offsides || 0, away: awayMatchStats?.offsides || 0 },
            { key: 'crosses', label: 'Tạt bóng', home: homeMatchStats?.crosses || 0, away: awayMatchStats?.crosses || 0 },
            { key: 'crossing_accuracy', label: 'Độ chính xác tạt bóng', home: homeMatchStats?.crossing_accuracy || 0, away: awayMatchStats?.crossing_accuracy || 0, isPercentage: true },
            { key: 'shots_blocked', label: 'Cú sút bị chặn', home: homeMatchStats?.shots_blocked || 0, away: awayMatchStats?.shots_blocked || 0 },
            { key: 'yellowredcards', label: 'Thẻ vàng đỏ', home: homeMatchStats?.yellowredcards || 0, away: awayMatchStats?.yellowredcards || 0 },
            { key: 'redcards', label: 'Thẻ đỏ', home: homeMatchStats?.redcards || 0, away: awayMatchStats?.redcards || 0 },
            { key: 'substitutions', label: 'Thay người', home: homeMatchStats?.substitutions || 0, away: awayMatchStats?.substitutions || 0 },
            { key: 'ball_safe', label: 'Bóng an toàn', home: homeMatchStats?.ball_safe || 0, away: awayMatchStats?.ball_safe || 0 },
            { key: 'goals', label: 'Bàn thắng', home: homeMatchStats?.goals || 0, away: awayMatchStats?.goals || 0 },
            { key: 'penalties', label: 'Phạt đền', home: homeMatchStats?.penalties || 0, away: awayMatchStats?.penalties || 0 },
            { key: 'xg', label: 'xG', home: homeMatchStats?.xg || 0, away: awayMatchStats?.xg || 0 },
        ];
        
        const content = containers.matchStats.querySelector('.space-y-3');
        if (!content) return;
        
        content.innerHTML = stats
            .filter(stat => {
                if (stat.isPercentage) return true;
                return stat.home > 0 || stat.away > 0;
            })
            .map(stat => {
                const homeValue = parseFloat(stat.home || 0);
                const awayValue = parseFloat(stat.away || 0);
                const isPercentage = stat.isPercentage || false;
                
                let homePercent, awayPercent;
                if (isPercentage) {
                    homePercent = homeValue;
                    awayPercent = awayValue;
                } else {
                    const total = homeValue + awayValue;
                    if (total > 0) {
                        homePercent = (homeValue / total) * 100;
                        awayPercent = (awayValue / total) * 100;
                    } else {
                        homePercent = 50;
                        awayPercent = 50;
                    }
                }
                
                const homeDisplay = isPercentage ? Math.round(homeValue) + '%' : Math.round(homeValue);
                const awayDisplay = isPercentage ? Math.round(awayValue) + '%' : Math.round(awayValue);
                
                return `
                    <div class="flex items-center py-2 border-b border-gray-100">
                        <div class="w-20 text-right text-sm font-medium text-gray-100 pr-2">${homeDisplay}</div>
                        <div class="flex-1 relative h-6 mx-2">
                            <div class="absolute left-0 top-0 h-full bg-green-500 rounded-l" style="width: ${homePercent}%"></div>
                        </div>
                        <div class="w-48 text-center text-sm font-medium text-gray-300 px-2">${stat.label}</div>
                        <div class="flex-1 relative h-6 mx-2">
                            <div class="absolute right-0 top-0 h-full bg-orange-500 rounded-r" style="width: ${awayPercent}%"></div>
                        </div>
                        <div class="w-20 text-left text-sm font-medium text-gray-100 pl-2">${awayDisplay}</div>
                    </div>
                `;
            }).join('');
    }
    
    /**
     * Load and update match detail data
     */
    function refreshMatchDetail() {
        fetch(`{{ route('api.match.detail.data') }}?id=${matchId}`)
            .then(response => response.json())
            .then(result => {
                if (result.success && result.data) {
                    const data = result.data;
                    
                    // Update all sections
                    renderMatchSummary(data);
                    renderMatchEvents(data);
                    renderMatchStats(data);
                }
            })
            .catch(error => {
                console.error('Error refreshing match detail:', error);
            });
    }
    
    // Auto-refresh every 30 seconds if match is live, every 60 seconds if upcoming
    // Only refresh if page is visible (not in background tab)
    function checkAndRefreshMatchDetail() {
        if (document.hidden) {
            return;
        }
        refreshMatchDetail();
    }
    
    const refreshInterval = isLive ? 30000 : 60000; // 30s for live, 60s for upcoming
    setInterval(checkAndRefreshMatchDetail, refreshInterval);
})();
</script>
@endpush
@endsection

