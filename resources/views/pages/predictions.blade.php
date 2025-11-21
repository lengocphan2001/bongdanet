@extends('layouts.app')

@section('title', 'keobong88 - Nhận định bóng đá')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-slate-900">

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Main Container --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">
                    {{-- Page Title --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Tin Tức Bóng Đá</span>
                        </h1>
                    </div>

                    {{-- League Navigation Tabs --}}
                    <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-2.5 mb-4 backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide pb-1 -mx-1 px-1">
                            @php
                                $leagueSlugs = [
                                    'ngoai-hang-anh' => ['name' => 'Ngoại Hạng Anh', 'icon' => '⚽'],
                                    'cup-c1' => ['name' => 'Cúp C1', 'icon' => '🏆'],
                                    'vdqg-duc' => ['name' => 'VĐQG Đức', 'icon' => '⚽'],
                                    'la-liga' => ['name' => 'La Liga', 'icon' => '⚽'],
                                    'vdqg-y' => ['name' => 'VĐQG Ý', 'icon' => '⚽'],
                                    'vdqg-phap' => ['name' => 'VĐQG Pháp', 'icon' => '⚽'],
                                    'v-league' => ['name' => 'V League', 'icon' => '⚽'],
                                    'vdqg-uc' => ['name' => 'VĐQG Úc', 'icon' => '⚽'],
                                    'cup-c2' => ['name' => 'Cúp C2', 'icon' => '🏆'],
                                    'cup-c3' => ['name' => 'Cúp C3', 'icon' => '🏆'],
                                    'c2-chau-a' => ['name' => 'C2 Châu Á', 'icon' => '🏆'],
                                    'cup-c1-chau-a' => ['name' => 'Cúp C1 Châu Á', 'icon' => '🏆'],
                                ];
                            @endphp
                            @foreach($leagueSlugs as $slug => $data)
                                <a href="{{ route('predictions.league', $slug) }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 whitespace-nowrap flex-shrink-0 hover:scale-105 active:scale-95
                                          {{ $loop->index % 4 === 0 ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white shadow-md shadow-blue-500/20' : 
                                             ($loop->index % 4 === 1 ? 'bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-white shadow-md shadow-emerald-500/20' :
                                             ($loop->index % 4 === 2 ? 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white shadow-md shadow-purple-500/20' :
                                             'bg-gradient-to-r from-amber-600 to-orange-700 hover:from-amber-500 hover:to-orange-600 text-white shadow-md shadow-amber-500/20')) }}">
                                    <span class="text-[10px]">{{ $data['icon'] }}</span>
                                    <span>{{ $data['name'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Information Box --}}
                    <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 border border-slate-700/50 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6 backdrop-blur-sm">
                        <div class="space-y-4 text-sm text-gray-300 leading-relaxed">
                            <p>
                                <strong class="text-amber-400">Nhận định bóng đá Net hôm nay:</strong> nhận định và dự đoán tỷ số bóng đá ngày mai chính xác. NHẬN ĐỊNH BÓNG ĐÁ cùng chuyên gia ở các giải đấu hàng đầu hiện nay như: Ngoại hạng Anh, Bundesliga, Ý, Tây Ban Nha, Cúp C1, Cúp C2 và V-league......Tab nhận định và dự đoán bao gồm:
                            </p>
                            <ul class="list-disc list-inside space-y-1 ml-4 text-gray-300">
                                <li>Dự đoán - Nhận định hôm nay</li>
                                <li>NHẬN ĐỊNH BÓNG ĐÁ ngày mai</li>
                            </ul>
                            <p>
                                Ngoài ra, bạn đọc cũng có thể xem nhận định kèo nhà cái chắc thắng đêm nay ngay trên Keobongda.co với nhiều dự đoán bóng đá chắc thắng như: nhận định tài xỉu, nhận định góc, kèo xiên, kèo thơm bóng đá và siêu máy tính dự đoán bóng đá.
                            </p>
                        </div>
                    </div>

                    {{-- Section Title --}}
                    <div class="mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-6 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full"></div>
                            <h2 class="text-lg sm:text-xl font-bold text-white uppercase tracking-wide">
                                <span class="bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent">NHẬN ĐỊNH KÈO NHÀ CÁI TRỰC TUYẾN</span>
                            </h2>
                        </div>
                    </div>

                    {{-- Predictions List --}}
                    <div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 rounded-xl shadow-xl border border-slate-700/50 divide-y divide-slate-700/50 mb-6 overflow-hidden backdrop-blur-sm">
                    @forelse($recentPredictions as $prediction)
                        @php
                            $matchTime = $prediction->match_time ? \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh') : null;
                            $isUpcoming = $matchTime && $matchTime->isFuture();
                            $timeDisplay = $matchTime ? $matchTime->format('H:i d/m/Y') : 'N/A';
                            $teams = ($prediction->home_team ?? 'N/A') . ' vs ' . ($prediction->away_team ?? 'N/A');
                            $matchUrl = route('prediction.detail', $prediction->id);
                        @endphp
                        <a href="{{ $matchUrl }}" class="block p-3 sm:p-4 hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 group">
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                <div class="flex-shrink-0 hidden sm:block">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-amber-500 to-orange-600 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1 flex-wrap">
                                        @if($isUpcoming)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-500/20 text-red-400 text-xs font-semibold animate-pulse">Sắp đá</span>
                                        @endif
                                        @if($matchTime)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400 text-xs font-semibold">{{ $matchTime->format('H:i') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs sm:text-sm text-white group-hover:text-amber-400 break-words transition-colors">
                                        <span class="font-semibold text-amber-400">Nhận định, nhận định</span> 
                                        <span class="text-gray-300 group-hover:text-amber-300">{{ $teams }}, {{ $timeDisplay }}: {{ Str::limit($prediction->title, 50) }}</span>
                                    </p>
                                    @if($prediction->league_name)
                                        <p class="text-xs text-gray-400 mt-1">{{ $prediction->league_name }}</p>
                                    @endif
                                </div>
                                <div class="w-full sm:w-20 h-24 sm:h-16 bg-gradient-to-br from-slate-700 to-slate-800 rounded-lg border border-slate-600/50 flex-shrink-0 overflow-hidden group-hover:border-amber-500/50 transition-colors">
                                    @if($prediction->thumbnail)
                                        <img src="{{ Storage::url($prediction->thumbnail) }}" alt="{{ $teams }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-700 to-slate-800 text-gray-400 text-xs text-center px-1">
                                            {{ Str::limit($prediction->home_team ?? '', 8) }}<br>vs<br>{{ Str::limit($prediction->away_team ?? '', 8) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-12 sm:p-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-400 text-sm sm:text-base font-medium">Chưa có nhận định nào.</p>
                        </div>
                    @endforelse
                </div>

                    {{-- Section Title: Predictions by League --}}
                    <div class="mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-6 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full"></div>
                            <h2 class="text-lg sm:text-xl font-bold text-white uppercase tracking-wide">
                                <span class="bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent">NHẬN ĐỊNH BÓNG ĐÁ HÔM NAY THEO GIẢI ĐẤU</span>
                            </h2>
                        </div>
                    </div>

                    {{-- Predictions by League --}}
                    @foreach($predictionsByLeague as $leagueId => $leaguePredictions)
                        @php
                            $firstPrediction = $leaguePredictions->first();
                            $leagueName = $firstPrediction->league_name ?? 'Giải đấu khác';
                        @endphp
                        <div class="mb-6">
                            <div class="mb-3">
                                <h3 class="text-base sm:text-lg font-bold text-white">
                                    <span class="bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent">NHẬN ĐỊNH {{ strtoupper($leagueName) }}</span>
                                </h3>
                            </div>
                            <div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 rounded-xl shadow-xl border border-slate-700/50 divide-y divide-slate-700/50 overflow-hidden backdrop-blur-sm">
                            @foreach($leaguePredictions as $prediction)
                    @php
                        $matchTime = $prediction->match_time ? \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh') : null;
                        $timeDisplay = $matchTime ? $matchTime->format('H:i d/m/Y') : 'N/A';
                                    $teams = ($prediction->home_team ?? 'N/A') . ' vs ' . ($prediction->away_team ?? 'N/A');
                                    $matchUrl = route('prediction.detail', $prediction->id);
                                @endphp
                                <a href="{{ $matchUrl }}" class="block p-3 sm:p-4 hover:bg-gradient-to-r hover:from-slate-800/60 hover:to-slate-900/60 transition-all duration-200 group">
                                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                        <div class="flex-shrink-0 hidden sm:block">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-amber-500 to-orange-600 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1">
                                                @if($matchTime)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400 text-xs font-semibold">{{ $matchTime->format('H:i') }}</span>
                                                @endif
                                            </div>
                                            <p class="text-xs sm:text-sm text-white group-hover:text-amber-400 break-words transition-colors">
                                                <span class="font-semibold text-amber-400">Nhận định, nhận định</span> 
                                                <span class="text-gray-300 group-hover:text-amber-300">{{ $teams }}, {{ $timeDisplay }}: {{ Str::limit($prediction->title, 50) }}</span>
                                            </p>
                                        </div>
                                        <div class="w-full sm:w-20 h-24 sm:h-16 bg-gradient-to-br from-slate-700 to-slate-800 rounded-lg border border-slate-600/50 flex-shrink-0 overflow-hidden group-hover:border-amber-500/50 transition-colors">
                                            @if($prediction->thumbnail)
                                                <img src="{{ Storage::url($prediction->thumbnail) }}" alt="{{ $teams }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-700 to-slate-800 text-gray-400 text-xs text-center px-1">
                                                    {{ Str::limit($prediction->home_team ?? '', 8) }}<br>vs<br>{{ Str::limit($prediction->away_team ?? '', 8) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4 order-2">
                <x-soi-keo-mini />
                <x-match-schedule activeDate="H.nay" />
                <x-fifa-ranking />
                <x-football-predictions-today />
            </aside>
        </div>
    </div>
</div>
@endsection

