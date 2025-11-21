@extends('layouts.app')

    @section('title', 'Keobongda.co - Lịch thi đấu')

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobongda.co', 'url' => route('home')],
        ['label' => 'Lịch thi đấu', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Main Container --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm">
                    {{-- Page Title --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Lịch Thi Đấu Bóng Đá</span>
                        </h1>
                    </div>

                    {{-- Date Selection Tabs --}}
                    <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-2.5 mb-4 backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide pb-1 -mx-1 px-1">
                            @foreach ($dateOptions ?? [] as $option)
                                @php
                                    $isActive = ($option['value'] == ($date ?? $today ?? date('Y-m-d')));
                                @endphp
                                <a href="{{ route('schedule', ['date' => $option['value']]) }}" 
                                   class="px-2.5 py-1.5 text-xs font-semibold rounded-md whitespace-nowrap flex-shrink-0 transition-all duration-200 {{ $isActive ? 'text-white bg-gradient-to-r from-blue-600 to-blue-700 shadow-md shadow-blue-500/20' : 'text-gray-300 bg-slate-700/50 hover:bg-slate-700 hover:text-white' }}">
                                    {{ $option['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- League Navigation Tabs --}}
                    @php
                        $leagueFilters = [
                            ['name' => 'Cúp C1', 'id' => 539, 'icon' => '🏆'],
                            ['name' => 'Ngoại Hạng Anh', 'id' => 583, 'icon' => '⚽'],
                            ['name' => 'La Liga', 'id' => 637, 'icon' => '⚽'],
                            ['name' => 'VĐQG Ý', 'id' => 719, 'icon' => '⚽'],
                            ['name' => 'VĐQG Pháp', 'id' => 764, 'icon' => '⚽'],
                            ['name' => 'Cúp C2', 'id' => 541, 'icon' => '🏆'],
                            ['name' => 'Cúp C3', 'id' => 4569, 'icon' => '🏆'],
                            ['name' => 'V League', 'id' => 3748, 'icon' => '⚽'],
                            ['name' => 'VĐQG Đức', 'id' => 594, 'icon' => '⚽'],
                            ['name' => 'VĐQG Úc', 'id' => 974, 'icon' => '⚽'],
                            ['name' => 'Cúp C1 Châu Á', 'id' => 511, 'icon' => '🏆'],
                        ];
                    @endphp
                    <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 rounded-lg border border-slate-700/50 p-2.5 mb-4 backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide pb-1 -mx-1 px-1">
                            @foreach($leagueFilters as $filter)
                                <a href="{{ route('schedule.league', $filter['id']) }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 whitespace-nowrap flex-shrink-0 hover:scale-105 active:scale-95
                                          {{ $loop->index % 4 === 0 ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white shadow-md shadow-blue-500/20' : 
                                             ($loop->index % 4 === 1 ? 'bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-white shadow-md shadow-emerald-500/20' :
                                             ($loop->index % 4 === 2 ? 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white shadow-md shadow-purple-500/20' :
                                             'bg-gradient-to-r from-amber-600 to-orange-700 hover:from-amber-500 hover:to-orange-600 text-white shadow-md shadow-amber-500/20')) }}">
                                    <span class="text-[10px]">{{ $filter['icon'] }}</span>
                                    <span>{{ $filter['name'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Schedule Table --}}
                    <x-schedule-table :scheduleMatches="$scheduleMatches ?? []" :date="$date ?? $today ?? date('Y-m-d')" />
                </div>
            </main>

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-4 order-2">
                <x-football-schedule-menu activeItem="Ngoại Hạng Anh" />
                <x-fifa-ranking />
                <x-match-schedule activeDate="H.nay" />
                <x-football-predictions-today />
            </aside>
        </div>
    </div>
</div>
@endsection

