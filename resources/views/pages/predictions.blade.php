@extends('layouts.app')

@section('title', 'keobongda.co - Nhận định bóng đá')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => '#'],
        ['label' => 'Nhận định bóng đá', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Page Title --}}
                <h1 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">
                    Nhận định bóng đá hôm nay - nhận định dự đoán bóng đá NET tối đêm nay
                </h1>

                {{-- League Navigation Tabs --}}
                <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="flex flex-wrap gap-2">
                        @php
                            $leagueSlugs = [
                                'ngoai-hang-anh' => 'Ngoại Hạng Anh',
                                'cup-c1' => 'Cúp C1',
                                'vdqg-duc' => 'VĐQG Đức',
                                'la-liga' => 'La Liga',
                                'vdqg-y' => 'VĐQG Ý',
                                'vdqg-phap' => 'VĐQG Pháp',
                                'v-league' => 'V League',
                                'vdqg-uc' => 'VĐQG Úc',
                                'cup-c2' => 'Cúp C2',
                                'cup-c3' => 'Cúp C3',
                                'c2-chau-a' => 'C2 Châu Á',
                                'cup-c1-chau-a' => 'Cúp C1 Châu Á',
                            ];
                        @endphp
                        @foreach($leagueSlugs as $slug => $name)
                            <a href="{{ route('predictions.league', $slug) }}" 
                               class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-gray-600 rounded hover:bg-gray-700 transition-colors">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Information Box --}}
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="space-y-4 text-sm text-gray-300 leading-relaxed">
                        <p>
                            <strong>Nhận định bóng đá Net hôm nay:</strong> nhận định và dự đoán tỷ số bóng đá ngày mai chính xác. NHẬN ĐỊNH BÓNG ĐÁ cùng chuyên gia ở các giải đấu hàng đầu hiện nay như: Ngoại hạng Anh, Bundesliga, Ý, Tây Ban Nha, Cúp C1, Cúp C2 và V-league......Tab nhận định và dự đoán bao gồm:
                        </p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Dự đoán - Nhận định hôm nay</li>
                            <li>NHẬN ĐỊNH BÓNG ĐÁ ngày mai</li>
                        </ul>
                        <p>
                            Ngoài ra, bạn đọc cũng có thể xem nhận định kèo nhà cái chắc thắng đêm nay ngay trên Keobongda.co với nhiều dự đoán bóng đá chắc thắng như: nhận định tài xỉu, nhận định góc, kèo xiên, kèo thơm bóng đá và siêu máy tính dự đoán bóng đá.
                        </p>
                        <div class="pt-2 border-t border-slate-700">
                            <p class="text-blue-400 text-xs">
                                <strong>Tag:</strong> Tỷ lệ kèo nhà cái 5 | Kết quả bóng đá hôm nay | Máy tính dự đoán bóng đá | Top nhà cái uy tín
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Section Title --}}
                <div class="mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-1 h-6 bg-green-600"></div>
                        <h2 class="text-lg font-bold text-blue-400 uppercase">NHẬN ĐỊNH KÈO NHÀ CÁI TRỰC TUYẾN</h2>
                    </div>
                </div>

                {{-- Predictions List --}}
                <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 divide-y divide-slate-700 mb-6">
                    @forelse($recentPredictions as $prediction)
                        @php
                            $matchTime = $prediction->match_time ? \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh') : null;
                            $isUpcoming = $matchTime && $matchTime->isFuture();
                            $timeDisplay = $matchTime ? $matchTime->format('H:i d/m/Y') : 'N/A';
                            $teams = ($prediction->home_team ?? 'N/A') . ' vs ' . ($prediction->away_team ?? 'N/A');
                            $matchUrl = route('prediction.detail', $prediction->id);
                        @endphp
                        <a href="{{ $matchUrl }}" class="block p-3 sm:p-4 hover:bg-slate-700 transition-colors group">
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                <svg class="w-4 h-4 text-green-600 flex-shrink-0 hidden sm:block" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1 flex-wrap">
                                        @if($isUpcoming)
                                            <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">Sắp đá</span>
                                        @endif
                                        @if($matchTime)
                                            <span class="bg-green-600 text-white text-xs font-bold px-2 py-0.5 rounded">{{ $matchTime->format('H:i') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-100 group-hover:text-blue-400 break-words">
                                        <span class="font-medium">Nhận định, nhận định</span> 
                                        <span class="text-gray-300 group-hover:text-blue-400">{{ $teams }}, {{ $timeDisplay }}: {{ Str::limit($prediction->title, 50) }}</span>
                                    </p>
                                    @if($prediction->league_name)
                                        <p class="text-xs text-gray-400 mt-1">{{ $prediction->league_name }}</p>
                                    @endif
                                </div>
                                <div class="w-full sm:w-20 h-24 sm:h-16 bg-slate-700 rounded flex-shrink-0 overflow-hidden">
                                    @if($prediction->thumbnail)
                                        <img src="{{ Storage::url($prediction->thumbnail) }}" alt="{{ $teams }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-700 text-gray-500 text-xs text-center px-1">
                                            {{ Str::limit($prediction->home_team ?? '', 8) }}<br>vs<br>{{ Str::limit($prediction->away_team ?? '', 8) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-gray-400">
                            <p>Chưa có nhận định nào.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Section Title: Predictions by League --}}
                <div class="mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-1 h-6 bg-green-600"></div>
                        <h2 class="text-lg font-bold text-blue-400 uppercase">NHẬN ĐỊNH BÓNG ĐÁ HÔM NAY THEO GIẢI ĐẤU</h2>
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
                            <h3 class="text-base font-bold text-white">NHẬN ĐỊNH {{ strtoupper($leagueName) }}</h3>
                        </div>
                        <div class="bg-slate-800 rounded-lg shadow-sm border border-slate-700 divide-y divide-slate-700">
                            @foreach($leaguePredictions as $prediction)
                    @php
                        $matchTime = $prediction->match_time ? \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh') : null;
                        $timeDisplay = $matchTime ? $matchTime->format('H:i d/m/Y') : 'N/A';
                                    $teams = ($prediction->home_team ?? 'N/A') . ' vs ' . ($prediction->away_team ?? 'N/A');
                                    $matchUrl = route('prediction.detail', $prediction->id);
                                @endphp
                                <a href="{{ $matchUrl }}" class="block p-3 sm:p-4 hover:bg-slate-700 transition-colors group">
                                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                        <svg class="w-4 h-4 text-green-600 flex-shrink-0 hidden sm:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1">
                                                @if($matchTime)
                                                    <span class="bg-green-600 text-white text-xs font-bold px-2 py-0.5 rounded">{{ $matchTime->format('H:i') }}</span>
                                                @endif
                                            </div>
                                            <p class="text-xs sm:text-sm text-gray-100 group-hover:text-blue-400 break-words">
                                                <span class="font-medium">Nhận định, nhận định</span> 
                                                <span class="text-gray-300 group-hover:text-blue-400">{{ $teams }}, {{ $timeDisplay }}: {{ Str::limit($prediction->title, 50) }}</span>
                                            </p>
                                        </div>
                                        <div class="w-full sm:w-20 h-24 sm:h-16 bg-slate-700 rounded flex-shrink-0 overflow-hidden">
                                            @if($prediction->thumbnail)
                                                <img src="{{ Storage::url($prediction->thumbnail) }}" alt="{{ $teams }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-slate-700 text-gray-500 text-xs text-center px-1">
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

