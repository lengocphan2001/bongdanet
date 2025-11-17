@extends('layouts.app')

@section('title', 'Trang chủ - Bongdanet')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Main Content Area --}}
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Sidebar --}}
            <aside class="w-fit flex-shrink-0">
                <x-football-results-menu activeItem="Ngoại Hạng Anh" />
            </aside>
            
            {{-- Main Content --}}
            <main class="flex-1 min-w-0">
                <x-match-list-table 
                    :liveMatches="$liveMatches ?? []" 
                    :upcomingMatches="$upcomingMatches ?? []"
                    :bookmakers="$bookmakers ?? []"
                    :currentDate="\Carbon\Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d')"
                />
            </main>
            
            {{-- Right Sidebar --}}
            <aside class="w-fit flex-shrink-0">
                <x-betting-analysis-menu activeItem="Soi kèo bóng đá TBN" />
            </aside>
        </div>
    </div>
</div>
@endsection

