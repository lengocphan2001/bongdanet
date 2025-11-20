@extends('layouts.app')

@section('title', 'keobongda.co - Trang chủ')

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

