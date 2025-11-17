@extends('layouts.app')

@section('title', 'Components Demo')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Component Demo</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
            {{-- Component 1: Football Results Menu --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Component 1: KẾT QUẢ BÓNG ĐÁ</h2>
                <x-football-results-menu />
            </div>
            
            {{-- Component 2: Betting Analysis Menu --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Component 2: SOI KÈO BÓNG ĐÁ</h2>
                <x-betting-analysis-menu />
            </div>
        </div>
    </div>
</div>
@endsection

