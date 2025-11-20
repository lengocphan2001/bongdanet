@extends('layouts.app')

@section('title', 'keobongda.co - Nhận định bóng đá ' . ($leagueName ?? ''))

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Nhận định bóng đá', 'url' => route('predictions')],
        ['label' => $leagueName ?? 'Giải đấu', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0">
                {{-- Page Title --}}
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Nhận định bóng đá {{ $leagueName ?? '' }} - Dự đoán nhận định {{ $leagueName ?? '' }} 2025
                </h1>

                {{-- League Navigation Tabs --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
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
                               class="px-4 py-2 text-sm font-medium text-white rounded transition-colors {{ $leagueSlug === $slug ? 'bg-gray-700' : 'bg-gray-600 hover:bg-gray-700' }}">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Information Box --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                    <div class="space-y-4 text-sm text-gray-700 leading-relaxed">
                        <p>
                            <strong>Nhận định bóng đá {{ $leagueName ?? '' }} hôm nay (tối và đêm nay) và ngày mai CHÍNH XÁC.</strong>
                        </p>
                        <p>
                            Phân tích nhận định của chuyên gia và dự đoán tỷ lệ giải bóng đá {{ $leagueName ?? '' }} mới nhất.
                        </p>
                        <p>
                            Nhận định và dự đoán bóng đá {{ $leagueName ?? '' }} thi đấu đêm nay, rạng sáng mai bởi các chuyên gia bóng đá Việt nam nổi tiếng và những bài dịch nhận định bóng đá của chuyên gia nỗi tiếng {{ $leagueName ?? '' }} của Sky Sport, BBC, ...
                        </p>
                    </div>
                </div>

                {{-- Featured Article --}}
                @if($featuredPrediction)
                    @php
                        $matchTime = $featuredPrediction->match_time ? \Carbon\Carbon::parse($featuredPrediction->match_time)->setTimezone('Asia/Ho_Chi_Minh') : null;
                        $timeDisplay = $matchTime ? $matchTime->format('d/m/Y H:i') : 'N/A';
                        $teams = ($featuredPrediction->home_team ?? 'N/A') . ' vs ' . ($featuredPrediction->away_team ?? 'N/A');
                        $matchUrl = route('prediction.detail', $featuredPrediction->id);
                    @endphp
                    <div class="mb-6">
                        <a href="{{ $matchUrl }}" class="block group">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                @if($featuredPrediction->thumbnail)
                                    <div class="w-full h-64 md:h-96 overflow-hidden">
                                        <img src="{{ Storage::url($featuredPrediction->thumbnail) }}" 
                                             alt="{{ $featuredPrediction->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                @endif
                                <div class="p-6">
                                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">
                                        {{ $featuredPrediction->title }}
                                    </h2>
                                    <p class="text-sm text-gray-500 mb-4">{{ $timeDisplay }}</p>
                                    <p class="text-gray-700 leading-relaxed">
                                        {{ Str::limit(strip_tags($featuredPrediction->content), 200) }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                {{-- News Grid --}}
                @if($otherPredictions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        @foreach($otherPredictions->take(3) as $prediction)
                            @php
                                $matchTime = $prediction->match_time ? \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh') : null;
                                $timeDisplay = $matchTime ? $matchTime->format('d/m/Y H:i') : 'N/A';
                                $teams = ($prediction->home_team ?? 'N/A') . ' vs ' . ($prediction->away_team ?? 'N/A');
                                $matchUrl = route('prediction.detail', $prediction->id);
                            @endphp
                            <a href="{{ $matchUrl }}" class="block group">
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden h-full">
                                    @if($prediction->thumbnail)
                                        <div class="w-full h-48 overflow-hidden">
                                            <img src="{{ Storage::url($prediction->thumbnail) }}" 
                                                 alt="{{ $prediction->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-base font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors line-clamp-2">
                                            {{ $prediction->title }}
                                        </h3>
                                        <p class="text-xs text-gray-500">{{ $timeDisplay }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Full-width Articles --}}
                    @foreach($otherPredictions->skip(3) as $prediction)
                        @php
                            $matchTime = $prediction->match_time ? \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh') : null;
                            $timeDisplay = $matchTime ? $matchTime->format('d/m/Y H:i') : 'N/A';
                            $teams = ($prediction->home_team ?? 'N/A') . ' vs ' . ($prediction->away_team ?? 'N/A');
                            $matchUrl = route('prediction.detail', $prediction->id);
                        @endphp
                        <a href="{{ $matchUrl }}" class="block mb-6 group">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="flex flex-col md:flex-row">
                                    @if($prediction->thumbnail)
                                        <div class="w-full md:w-64 h-48 md:h-auto overflow-hidden flex-shrink-0">
                                            <img src="{{ Storage::url($prediction->thumbnail) }}" 
                                                 alt="{{ $prediction->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                    @endif
                                    <div class="flex-1 p-6">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">
                                            {{ $prediction->title }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mb-3">{{ $timeDisplay }}</p>
                                        <p class="text-gray-700 leading-relaxed">
                                            {{ Str::limit(strip_tags($prediction->content), 150) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                        <p class="text-gray-500">Chưa có nhận định nào cho giải đấu này.</p>
                    </div>
                @endif
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-fit lg:w-80 flex-shrink-0 space-y-4">
                @if($recentPredictions->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <h3 class="text-base font-bold text-gray-900 mb-4">Bài viết mới nhất</h3>
                        <div class="space-y-4">
                            @foreach($recentPredictions as $prediction)
                                @php
                                    $matchTime = $prediction->match_time ? \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh') : null;
                                    $timeDisplay = $matchTime ? $matchTime->format('d/m/Y H:i') : 'N/A';
                                    $matchUrl = route('prediction.detail', $prediction->id);
                                @endphp
                                <a href="{{ $matchUrl }}" class="block group">
                                    <div class="flex space-x-3">
                                        @if($prediction->thumbnail)
                                            <div class="w-20 h-16 flex-shrink-0 overflow-hidden rounded">
                                                <img src="{{ Storage::url($prediction->thumbnail) }}" 
                                                     alt="{{ $prediction->title }}" 
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition-colors line-clamp-2">
                                                {{ $prediction->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1">{{ $timeDisplay }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                <x-soi-keo-mini activeItem="Nhận định bóng đá Anh" />
                <x-match-schedule activeDate="H.nay" />
                <x-fifa-ranking />
                <x-football-predictions-today />
            </aside>
        </div>
    </div>
</div>
@endsection

