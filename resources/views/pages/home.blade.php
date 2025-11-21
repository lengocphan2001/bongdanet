@extends('layouts.app')

@section('title', 'keobong88 - Trang chủ')

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Sidebar --}}
            <aside class="w-full lg:w-fit flex-shrink-0 order-2 lg:order-1 space-y-4">
                {{-- Banner Quảng Cáo Left Sidebar (load từ database) --}}
                <x-banner-list position="sidebar-left" />
            </aside>
            
            {{-- Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-2">
                {{-- Header --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 overflow-hidden backdrop-blur-sm mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Trực Tiếp & Lịch Thi Đấu</span>
                        </h1>
                    </div>
                </div>
                
                <x-home-matches-table 
                    :liveMatches="$liveMatches ?? []" 
                    :upcomingMatches="$upcomingMatches ?? []"
                />
            </main>
            
            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-fit flex-shrink-0 order-3 space-y-4">
                {{-- Banner Quảng Cáo Right Sidebar (load từ database) --}}
                <x-banner-list position="sidebar-right" />
                
                <x-football-results-menu activeItem="Ngoại Hạng Anh" />
                
                <x-soi-keo-mini />
            </aside>
        </div>
    </div>
</div>
@endsection

