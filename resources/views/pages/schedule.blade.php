@extends('layouts.app')

@section('title', 'Lịch thi đấu - Bongdanet')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'BONGDANET', 'url' => route('home')],
        ['label' => 'Lịch thi đấu', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0">
                {{-- Page Title --}}
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    Lịch thi đấu bóng đá hôm nay - Lịch bóng đá trực tuyến
                </h1>

                {{-- Date Selection Dropdown --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <label for="date-select" class="text-sm font-medium text-gray-700 whitespace-nowrap">Chọn ngày:</label>
                        <select id="date-select" onchange="window.location.href='{{ route('schedule') }}?date=' + this.value" 
                                class="px-4 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                            @foreach ($dateOptions ?? [] as $option)
                                <option value="{{ $option['value'] }}" {{ $option['value'] == ($date ?? $today ?? date('Y-m-d')) ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- League Navigation Tabs --}}
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
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($leagueFilters as $filter)
                            <a href="{{ route('schedule.league', $filter['id']) }}" 
                               class="px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded hover:bg-gray-700 transition-colors">
                                {{ $filter['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Schedule Table --}}
                <x-schedule-table :scheduleMatches="$scheduleMatches ?? []" :date="$date ?? $today ?? date('Y-m-d')" />
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-fit lg:w-80 flex-shrink-0 space-y-4">
                <x-football-schedule-menu activeItem="Ngoại Hạng Anh" />
                <x-fifa-ranking />
                <x-match-schedule activeDate="H.nay" />
                <x-football-predictions-today />
            </aside>
        </div>
    </div>
</div>
@endsection

