@extends('layouts.app')

@section('title', 'Kết quả trận ' . ($homeTeam['name'] ?? '') . ' vs ' . ($awayTeam['name'] ?? '') . ' - Bongdanet')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'BONGDANET', 'url' => route('home')],
        ['label' => 'Kết quả bóng đá', 'url' => route('results')],
        ['label' => ($homeTeam['name'] ?? '') . ' vs ' . ($awayTeam['name'] ?? ''), 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        {{-- Page Title --}}
        <h1 class="text-lg sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 break-words">
            Kết quả trận {{ $homeTeam['name'] ?? '' }} vs {{ $awayTeam['name'] ?? '' }}, {{ $displayTime ? str_replace(':', 'h', $displayTime) : '' }} ngày {{ $displayDate ?? '' }}
        </h1>

        {{-- Match Summary Box (Green) --}}
        <div class="bg-[#1a5f2f] rounded-lg shadow-lg mb-4 sm:mb-6 p-4 sm:p-6">
            {{-- League/Stage Info --}}
            <div class="text-center text-white mb-4">
                <p class="text-xs sm:text-sm font-medium break-words">
                    Kqbd {{ $league['name'] ?? '' }} {{ $match['stage_name'] ?? '' }} {{ $displayTime ? str_replace(':', 'h', $displayTime) : '' }} Ngày {{ $displayDate ?? '' }}/{{ !empty($matchDate) ? date('Y', strtotime($matchDate)) : date('Y') }}
                </p>
            </div>

            {{-- Teams and Score --}}
            <div class="flex flex-col sm:flex-row items-center justify-between mb-4 space-y-4 sm:space-y-0">
                {{-- Home Team --}}
                <div class="flex-1 flex flex-col items-center w-full sm:w-auto">
                    @if (!empty($homeTeam['img']))
                        <img src="{{ $homeTeam['img'] }}" alt="{{ $homeTeam['name'] ?? '' }}" class="w-12 h-12 sm:w-16 sm:h-16 mb-2">
                    @endif
                    <span class="text-white font-medium text-center text-sm sm:text-base break-words px-2">{{ $homeTeam['name'] ?? '' }}</span>
                </div>

                {{-- Score --}}
                <div class="flex-1 flex flex-col items-center mx-0 sm:mx-4 w-full sm:w-auto">
                    <div class="text-2xl sm:text-4xl font-bold text-white mb-2">
                        {{ ($scores['home_score'] ?? '') !== '' ? ($scores['home_score'] ?? '?') : '?' }} - {{ ($scores['away_score'] ?? '') !== '' ? ($scores['away_score'] ?? '?') : '?' }}
                    </div>
                    @if ($match['status'] == 1 || $match['status_name'] == 'Inplay')
                        <a href="#" class="bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-1 sm:py-2 rounded text-xs sm:text-sm font-medium transition-colors">
                            Xem Live
                        </a>
                    @endif
                    @if (!empty($scores['ht_score']))
                        <p class="text-white text-sm mt-2">({{ $scores['ht_score'] }})</p>
                    @else
                        <p class="text-white text-sm mt-2">(0-0)</p>
                    @endif
                </div>

                {{-- Away Team --}}
                <div class="flex-1 flex flex-col items-center w-full sm:w-auto">
                    @if (!empty($awayTeam['img']))
                        <img src="{{ $awayTeam['img'] }}" alt="{{ $awayTeam['name'] ?? '' }}" class="w-12 h-12 sm:w-16 sm:h-16 mb-2">
                    @endif
                    <span class="text-white font-medium text-center text-sm sm:text-base break-words px-2">{{ $awayTeam['name'] ?? '' }}</span>
                </div>
            </div>

            {{-- Match Details --}}
            <div class="flex flex-wrap justify-center gap-3 sm:gap-6 text-white text-xs sm:text-sm">
                <div>
                    <span class="font-medium">Địa Điểm:</span>
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
                <div>
                    <span class="font-medium">Thời Tiết:</span>
                    <span>{{ $match['weather_report']['condition'] ?? 'Ít Mây' }}, {{ $match['weather_report']['temp'] ?? '12' }}°C ~ {{ $match['weather_report']['temp'] ?? '13' }}°C</span>
                </div>
            </div>
        </div>


        {{-- Navigation Tabs --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex border-b border-gray-200">
                <a href="#" class="px-6 py-3 text-sm font-medium text-gray-700 hover:text-[#1a5f2f] border-b-2 border-transparent hover:border-[#1a5f2f] transition-colors">
                    NHẬN ĐỊNH
                </a>
                <a href="#" class="px-6 py-3 text-sm font-medium text-[#1a5f2f] border-b-2 border-[#1a5f2f]">
                    CHI TIẾT
                </a>
                <a href="#" class="px-6 py-3 text-sm font-medium text-gray-700 hover:text-[#1a5f2f] border-b-2 border-transparent hover:border-[#1a5f2f] transition-colors">
                    PHÂN TÍCH
                </a>
                <a href="#" class="px-6 py-3 text-sm font-medium text-gray-700 hover:text-[#1a5f2f] border-b-2 border-transparent hover:border-[#1a5f2f] transition-colors">
                    SO SÁNH TL
                </a>
                <a href="#" class="px-6 py-3 text-sm font-medium text-gray-700 hover:text-[#1a5f2f] border-b-2 border-transparent hover:border-[#1a5f2f] transition-colors">
                    TK CẦU THỦ
                </a>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ strtoupper($league['name'] ?? '') }}</h2>
            <p class="text-sm text-gray-700 mb-4">
                Bóng đá net - Bóng đá số cập nhật tỷ số Kết quả bóng đá {{ $homeTeam['name'] ?? '' }} vs {{ $awayTeam['name'] ?? '' }} hôm nay ngày {{ $displayDate ?? '' }}/{{ date('Y') }} lúc {{ $displayTime ?? '' }} chuẩn xác mới nhất. Xem diễn biến trực tiếp lịch thi đấu - Bảng xếp hạng - Tỷ lệ bóng đá - Kqbd {{ $homeTeam['name'] ?? '' }} vs {{ $awayTeam['name'] ?? '' }} tại {{ $league['name'] ?? '' }} {{ date('Y') }}.
            </p>
            <p class="text-sm text-gray-700">
                Cập nhật nhanh chóng kqbd trực tiếp của hơn 1000+++ giải đấu HOT trên thế giới. Xem ngay diễn biến kết quả {{ $homeTeam['name'] ?? '' }} vs {{ $awayTeam['name'] ?? '' }} hôm nay chính xác nhất tại đây.
            </p>
        </div>

        {{-- Match Events Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4" data-section="match-events">
            <h2 class="text-sm font-bold text-gray-900 mb-2">Diễn biến - Kết quả {{ $homeTeam['name'] ?? '' }} vs {{ $awayTeam['name'] ?? '' }}</h2>
            
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
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <p class="text-red-600 font-medium">Trận đấu chưa có dữ liệu !</p>
                </div>
            @else
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-2 py-2 text-left">
                                    <div class="flex items-center space-x-2">
                                        @if (!empty($homeTeam['img']))
                                            <img src="{{ $homeTeam['img'] }}" alt="{{ $homeTeam['name'] ?? '' }}" class="w-5 h-5">
                                        @endif
                                        <span class="font-medium">{{ $homeTeam['name'] ?? '' }}</span>
                                    </div>
                                </th>
                                <th class="px-2 py-2 text-center w-16 font-medium">Phút</th>
                                <th class="px-2 py-2 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <span class="font-medium">{{ $awayTeam['name'] ?? '' }}</span>
                                        @if (!empty($awayTeam['img']))
                                            <img src="{{ $awayTeam['img'] }}" alt="{{ $awayTeam['name'] ?? '' }}" class="w-5 h-5">
                                        @endif
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
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
                                        $eventColor = 'text-red-600';
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
                                <tr>
                                    @if($isHomeEvent)
                                        <td class="px-2 py-2">
                                            <div class="flex items-center space-x-1">
                                                <span class="text-sm">{{ $eventIcon }}</span>
                                                <span class="{{ $eventColor }}">{{ $eventText }}</span>
                                            </div>
                                        </td>
                                        <td class="px-2 py-2 text-center font-medium">{{ $minuteDisplay }}</td>
                                        <td class="px-2 py-2"></td>
                                    @elseif($isAwayEvent)
                                        <td class="px-2 py-2"></td>
                                        <td class="px-2 py-2 text-center font-medium">{{ $minuteDisplay }}</td>
                                        <td class="px-2 py-2 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <span class="{{ $eventColor }}">{{ $eventText }}</span>
                                                <span class="text-lg">{{ $eventIcon }}</span>
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
            <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-600">
                <div class="flex items-center space-x-1">
                    <span class="text-lg">⚽</span>
                    <span>Bàn thắng</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="text-lg">🟨</span>
                    <span>Thẻ vàng</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="text-lg">🟥</span>
                    <span>Thẻ đỏ</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="text-lg">🔄</span>
                    <span>Thay người</span>
                </div>
            </div>
        </div>

        {{-- Statistics Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4" data-section="match-stats">
            <h2 class="text-sm font-bold text-gray-900 mb-2">Thống kê kỹ thuật {{ $homeTeam['name'] ?? '' }} VS {{ $awayTeam['name'] ?? '' }}</h2>
            
            @if(empty($homeMatchStats) && empty($awayMatchStats))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <p class="text-red-600 font-medium">Chưa có bảng thống kê số liệu trận đấu !</p>
                </div>
            @else
                {{-- Header with team names and logos --}}
                <div class="bg-gray-700 text-white px-3 py-2 flex items-center justify-between mb-2 rounded-t">
                    <div class="flex items-center space-x-2 flex-1 min-w-0">
                        @if (!empty($homeTeam['img']))
                            <img src="{{ $homeTeam['img'] }}" alt="{{ $homeTeam['name'] ?? '' }}" class="w-6 h-6 rounded-full flex-shrink-0">
                        @endif
                        <span class="font-medium break-words break-all">{{ $homeTeam['name'] ?? '' }}</span>
                    </div>
                    <div class="flex items-center space-x-2 flex-1 min-w-0 justify-end">
                        @if (!empty($awayTeam['img']))
                            <img src="{{ $awayTeam['img'] }}" alt="{{ $awayTeam['name'] ?? '' }}" class="w-6 h-6 rounded-full flex-shrink-0">
                        @endif
                        <span class="font-medium break-words break-all text-right">{{ $awayTeam['name'] ?? '' }}</span>
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
                        <div class="flex items-center py-1 border-b border-gray-100">
                            {{-- Home value --}}
                            <div class="w-16 text-right text-xs font-medium text-gray-900 pr-1">
                                {{ $homeDisplay }}
                            </div>
                            
                            {{-- Home bar --}}
                            <div class="flex-1 relative h-4 mx-1">
                                <div class="absolute left-0 top-0 h-full bg-green-500 rounded-l" style="width: {{ $homePercent }}%"></div>
                            </div>
                            
                            {{-- Stat label --}}
                            <div class="w-40 text-center text-xs font-medium text-gray-700 px-1">
                                {{ $stat['label'] }}
                            </div>
                            
                            {{-- Away bar --}}
                            <div class="flex-1 relative h-4 mx-1">
                                <div class="absolute right-0 top-0 h-full bg-orange-500 rounded-r" style="width: {{ $awayPercent }}%"></div>
                            </div>
                            
                            {{-- Away value --}}
                            <div class="w-16 text-left text-xs font-medium text-gray-900 pl-1">
                                {{ $awayDisplay }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Lineup Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4" data-section="match-lineups">
            <h2 class="text-sm font-bold text-gray-900 mb-2">Đội hình xuất phát</h2>
            
            @if(empty($matchLineups))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <p class="text-red-600 font-medium">Chưa có thông tin đội hình ra sân !</p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Home Team Lineup --}}
                    @if(isset($matchLineups['home']))
                        @php
                            $homeLineup = $matchLineups['home'];
                            $homeFormation = $homeLineup['formation'] ?? '';
                            $homeCoach = $homeLineup['coach'] ?? null;
                            $homeSquad = $homeLineup['squad'] ?? [];
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-2">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-1">
                                    @if (!empty($homeTeam['img']))
                                        <img src="{{ $homeTeam['img'] }}" alt="{{ $homeTeam['name'] ?? '' }}" class="w-6 h-6">
                                    @endif
                                    <h3 class="text-sm font-bold text-gray-900">{{ $homeTeam['name'] ?? '' }}</h3>
                                </div>
                                @if($homeFormation)
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-medium">{{ $homeFormation }}</span>
                                @endif
                            </div>
                            @if($homeCoach)
                                <p class="text-xs text-gray-600 mb-2">HLV: {{ $homeCoach['name'] ?? '' }}</p>
                            @endif
                            <div class="space-y-1">
                                @foreach($homeSquad as $player)
                                    @php
                                        $playerInfo = $player['player'] ?? [];
                                        $playerName = $playerInfo['name'] ?? $playerInfo['common_name'] ?? '';
                                        $playerNumber = $player['number'] ?? '';
                                        $position = $player['position'] ?? '';
                                        $positionName = $player['position_name'] ?? '';
                                        $isCaptain = $player['captain'] ?? false;
                                    @endphp
                                    <div class="flex items-center space-x-2 p-1 hover:bg-gray-50 rounded">
                                        <span class="w-6 text-center font-bold text-gray-700 text-xs">{{ $playerNumber }}</span>
                                        @if (!empty($playerInfo['img']))
                                            <img src="{{ $playerInfo['img'] }}" alt="{{ $playerName }}" class="w-6 h-6 rounded-full">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-gray-200"></div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-1">
                                                <span class="text-xs font-medium text-gray-900">{{ $playerName ?: 'N/A' }}</span>
                                                @if($isCaptain)
                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-1 py-0.5 rounded">C</span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $positionName }}</span>
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
                        <div class="border border-gray-200 rounded-lg p-2">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-1">
                                    @if (!empty($awayTeam['img']))
                                        <img src="{{ $awayTeam['img'] }}" alt="{{ $awayTeam['name'] ?? '' }}" class="w-6 h-6">
                                    @endif
                                    <h3 class="text-sm font-bold text-gray-900">{{ $awayTeam['name'] ?? '' }}</h3>
                                </div>
                                @if($awayFormation)
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-medium">{{ $awayFormation }}</span>
                                @endif
                            </div>
                            @if($awayCoach)
                                <p class="text-xs text-gray-600 mb-2">HLV: {{ $awayCoach['name'] ?? '' }}</p>
                            @endif
                            <div class="space-y-1">
                                @foreach($awaySquad as $player)
                                    @php
                                        $playerInfo = $player['player'] ?? [];
                                        $playerName = $playerInfo['name'] ?? $playerInfo['common_name'] ?? '';
                                        $playerNumber = $player['number'] ?? '';
                                        $position = $player['position'] ?? '';
                                        $positionName = $player['position_name'] ?? '';
                                        $isCaptain = $player['captain'] ?? false;
                                    @endphp
                                    <div class="flex items-center space-x-2 p-1 hover:bg-gray-50 rounded">
                                        <span class="w-6 text-center font-bold text-gray-700 text-xs">{{ $playerNumber }}</span>
                                        @if (!empty($playerInfo['img']))
                                            <img src="{{ $playerInfo['img'] }}" alt="{{ $playerName }}" class="w-6 h-6 rounded-full">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-gray-200"></div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-1">
                                                <span class="text-xs font-medium text-gray-900">{{ $playerName ?: 'N/A' }}</span>
                                                @if($isCaptain)
                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-1 py-0.5 rounded">C</span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $positionName }}</span>
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
        matchEvents: document.querySelector('[data-section="match-events"]') || document.querySelector('.bg-white.rounded-lg.shadow-sm.border.border-gray-200.p-6.mb-6:nth-of-type(2)'),
        matchStats: document.querySelector('[data-section="match-stats"]') || document.querySelector('.bg-white.rounded-lg.shadow-sm.border.border-gray-200.p-6.mb-6:nth-of-type(3)'),
        matchLineups: document.querySelector('[data-section="match-lineups"]') || document.querySelector('.bg-white.rounded-lg.shadow-sm.border.border-gray-200.p-6.mb-6:last-of-type'),
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
                eventColor = 'text-red-600';
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
                content.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600 font-medium">Chưa có bảng thống kê số liệu trận đấu !</p></div>';
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
                        <div class="w-20 text-right text-sm font-medium text-gray-900 pr-2">${homeDisplay}</div>
                        <div class="flex-1 relative h-6 mx-2">
                            <div class="absolute left-0 top-0 h-full bg-green-500 rounded-l" style="width: ${homePercent}%"></div>
                        </div>
                        <div class="w-48 text-center text-sm font-medium text-gray-700 px-2">${stat.label}</div>
                        <div class="flex-1 relative h-6 mx-2">
                            <div class="absolute right-0 top-0 h-full bg-orange-500 rounded-r" style="width: ${awayPercent}%"></div>
                        </div>
                        <div class="w-20 text-left text-sm font-medium text-gray-900 pl-2">${awayDisplay}</div>
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
    
    // Auto-refresh every 10 seconds if match is live, every 30 seconds if upcoming
    const refreshInterval = isLive ? 10000 : 30000;
    setInterval(refreshMatchDetail, refreshInterval);
})();
</script>
@endpush
@endsection

