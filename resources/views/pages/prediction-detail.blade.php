@extends('layouts.app')

@section('title', $prediction->title . ' - Nhận định bóng đá')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="mb-4 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-green-600">Trang chủ</a></li>
                <li>/</li>
                <li><a href="{{ route('predictions') }}" class="hover:text-green-600">Nhận định bóng đá</a></li>
                <li>/</li>
                <li class="text-gray-900">{{ Str::limit($prediction->title, 50) }}</li>
            </ol>
        </nav>

        {{-- Main Article --}}
        <article class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-6">
                @php
                    // Get league name from API or database
                    $leagueName = $matchInfo['league']['name'] ?? $prediction->league_name ?? 'Giao hữu';
                @endphp
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm bg-green-800 px-3 py-1 rounded">{{ $leagueName }}</span>
                    <span class="text-sm">{{ $prediction->published_at ? $prediction->published_at->format('d/m/Y H:i') : '' }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2">{{ $prediction->title }}</h1>
                @php
                    // Get match time from API or database
                    $matchTimeDisplay = null;
                    if ($matchInfo && isset($matchInfo['time']['datetime'])) {
                        $matchTimeDisplay = \Carbon\Carbon::parse($matchInfo['time']['datetime'])->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i');
                    } elseif ($prediction->match_time) {
                        $matchTimeDisplay = \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i');
                    }
                @endphp
                <div class="flex items-center space-x-4 text-sm">
                    <span>{{ $prediction->author->name ?? 'Admin' }}</span>
                    <span>•</span>
                    <span>{{ $matchTimeDisplay ?? 'N/A' }}</span>
                </div>
            </div>

            {{-- Thumbnail --}}
            @if($prediction->thumbnail)
                <div class="w-full h-64 md:h-96 overflow-hidden">
                    <img src="{{ Storage::url($prediction->thumbnail) }}" 
                         alt="{{ $prediction->title }}" 
                         class="w-full h-full object-cover">
                </div>
            @endif

            {{-- Match Info Widget --}}
            <div class="bg-gray-50 border-b border-gray-200 p-4">
                @php
                    // Use match info from API if available, otherwise fallback to database
                    $homeTeam = $matchInfo['teams']['home']['name'] ?? $prediction->home_team ?? 'N/A';
                    $awayTeam = $matchInfo['teams']['away']['name'] ?? $prediction->away_team ?? 'N/A';
                    
                    // Get match time from API or database
                    $matchTime = null;
                    if ($matchInfo && isset($matchInfo['time']['datetime'])) {
                        $matchTime = \Carbon\Carbon::parse($matchInfo['time']['datetime'])->setTimezone('Asia/Ho_Chi_Minh');
                    } elseif ($prediction->match_time) {
                        $matchTime = \Carbon\Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh');
                    }
                    
                    $matchId = $prediction->match_id ?? $prediction->match_api_id;
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="text-center flex-1">
                            <div class="text-sm font-bold text-gray-900">{{ $homeTeam }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-600 mb-1">VS</div>
                            <div class="text-xs text-gray-600">
                                {{ $matchTime ? $matchTime->format('H:i d/m') : 'N/A' }}
                            </div>
                        </div>
                        <div class="text-center flex-1">
                            <div class="text-sm font-bold text-gray-900">{{ $awayTeam }}</div>
                        </div>
                    </div>
                    <div class="ml-4 flex space-x-2">
                        @if($matchId)
                            <a href="{{ route('match.detail', $matchId) }}" 
                               class="bg-green-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-green-700">
                                Kết quả
                            </a>
                        @endif
                        <button class="bg-red-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-red-700">
                            Cược Ngay
                        </button>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-6">
                {{-- Introduction --}}
                @if($prediction->content)
                    <div class="prose max-w-none mb-6">
                        {!! $prediction->content !!}
                    </div>
                @endif

                {{-- Analysis Section --}}
                @if($prediction->analysis)
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-green-600 mb-4">nhận định, DỰ ĐOÁN TỶ LỆ BÓNG ĐÁ {{ strtoupper($prediction->home_team) }} VS {{ strtoupper($prediction->away_team) }}</h2>
                        <div class="prose max-w-none">
                            {!! $prediction->analysis !!}
                        </div>
                    </div>
                @endif

                {{-- Conclusion --}}
                <div class="bg-gray-50 rounded-lg p-4 mt-6">
                    <p class="text-sm text-gray-600">
                        Thống kê phong độ, lịch sử đối đầu của {{ $prediction->home_team }} với {{ $prediction->away_team }}
                    </p>
                </div>
            </div>
        </article>

        {{-- Related Predictions --}}
        @if($relatedPredictions->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">XEM THÊM</h2>
                <ul class="space-y-3">
                    @foreach($relatedPredictions as $related)
                        <li>
                            <a href="{{ route('prediction.detail', $related->id) }}" 
                               class="text-gray-700 hover:text-green-600 flex items-start space-x-2">
                                <span class="text-green-600 mt-1">•</span>
                                <span>{{ $related->title }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Additional Links --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">XEM THÊM</h2>
            <ul class="space-y-3">
                <li>
                    <a href="{{ route('predictions') }}" class="text-gray-700 hover:text-green-600 flex items-start space-x-2">
                        <span class="text-green-600 mt-1">•</span>
                        <span>Nhận định bóng đá - nhận định nhà cái chuẩn xác Hot!</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="text-gray-700 hover:text-green-600 flex items-start space-x-2">
                        <span class="text-green-600 mt-1">•</span>
                        <span>TOP nhà cái uy tín nhất hiện nay 2025 Hot!</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

@php
    use Illuminate\Support\Facades\Storage;
@endphp
@endsection

