@extends('layouts.app')

@section('title', 'keobongda.co - Kết quả bóng đá')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Kết quả bóng đá', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Page Title --}}
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">
                    Kqbd hôm nay nhanh nhất - Kết quả bóng đá trực tuyến đêm qua
                </h1>

                {{-- Date Navigation Tabs --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 mb-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('results', ['type' => 'live']) }}" 
                           class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white {{ ($isLive ?? false) ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-600 hover:bg-gray-700' }} rounded transition-colors">
                            Kết quả trực truyền
                        </a>
                        @foreach ($dateOptions ?? [] as $option)
                            @php
                                // Skip 'live' option as it's already shown separately
                                if ($option['value'] === 'live') continue;
                                $isActive = !($isLive ?? false) && ($option['value'] == ($date ?? $today ?? date('Y-m-d')));
                            @endphp
                            <a href="{{ route('results', ['date' => $option['value']]) }}" 
                               class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white {{ $isActive ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-600 hover:bg-gray-700' }} rounded transition-colors">
                                {{ $option['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- League Navigation Tabs --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="flex flex-wrap gap-2">
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
                        @foreach($leagueFilters as $filter)
                            <a href="{{ route('results.league', $filter['id']) }}" 
                               class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-gray-600 rounded hover:bg-gray-700 transition-colors">
                                {{ $filter['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Results Table --}}
                <x-simple-results-table :finishedMatches="$finishedMatches ?? []" />
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4 order-2">
                <x-football-results-menu activeItem="Ngoại Hạng Anh" />
                <x-fifa-ranking />
            </aside>
        </div>
    </div>
</div>
@endsection


