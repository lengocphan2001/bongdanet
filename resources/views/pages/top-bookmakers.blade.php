@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('title', 'keobong88 - Top Nhà Cái Uy Tín')

@section('content')
<div class="min-h-screen bg-slate-900">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="[
        ['label' => 'keobong88', 'url' => route('home')],
        ['label' => 'Top Nhà Cái', 'url' => null],
    ]" />

    {{-- Main Content Area --}}
    <div class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Left Column - Main Content --}}
            <main class="flex-1 min-w-0 order-1 lg:order-1">
                {{-- Main Container --}}
                <div class="bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-6 md:p-8 backdrop-blur-sm">
                    {{-- Page Title --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full"></div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-0 uppercase break-words tracking-tight">
                            <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Top Nhà Cái Uy Tín</span>
                        </h1>
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <p class="text-gray-300 text-sm sm:text-base leading-relaxed">
                            Danh sách các nhà cái uy tín hàng đầu được chúng tôi đánh giá và khuyến nghị. 
                            Tất cả đều đã được kiểm chứng về độ tin cậy, tỷ lệ cược tốt và dịch vụ chăm sóc khách hàng chuyên nghiệp.
                        </p>
                    </div>

                    {{-- Bookmaker Slider --}}
                    <div class="mb-8">
                        <x-bookmaker-slider />
                    </div>

                    {{-- Bookmakers Grid --}}
                    @if($bookmakers->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            @foreach($bookmakers as $index => $bookmaker)
                                <div class="group relative bg-gradient-to-br from-slate-800/90 to-slate-900/90 rounded-xl border border-slate-700/50 p-4 sm:p-6 hover:border-amber-500/50 transition-all duration-300 hover:shadow-2xl hover:shadow-amber-500/20 hover:-translate-y-1 overflow-hidden">
                                    {{-- Rank Badge --}}
                                    <div class="absolute top-3 right-3 z-10">
                                        @if($index < 3)
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $index === 0 ? 'from-yellow-400 to-yellow-600' : ($index === 1 ? 'from-gray-300 to-gray-500' : 'from-amber-600 to-amber-800') }} flex items-center justify-center shadow-lg">
                                                <span class="text-white font-bold text-sm">#{{ $index + 1 }}</span>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center shadow-lg">
                                                <span class="text-gray-300 font-bold text-sm">#{{ $index + 1 }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Bookmaker Logo --}}
                                    <div class="flex justify-center mb-4">
                                        <a href="{{ $bookmaker->link }}" target="{{ $bookmaker->target }}" class="block w-1/2 mx-auto aspect-square overflow-hidden rounded-xl transition-all duration-300 group-hover:scale-105">
                                            @if($bookmaker->image)
                                                <img src="{{ Storage::url($bookmaker->image) }}" 
                                                     alt="{{ $bookmaker->name }}" 
                                                     class="w-full h-full object-cover rounded-xl">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm bg-white/5 rounded-xl">
                                                    {{ $bookmaker->name }}
                                                </div>
                                            @endif
                                        </a>
                                    </div>

                                    {{-- Bookmaker Name --}}
                                    <h3 class="text-lg sm:text-xl font-bold text-white text-center mb-3 group-hover:text-amber-400 transition-colors">
                                        {{ $bookmaker->name }}
                                    </h3>

                                    {{-- Notes/Description --}}
                                    @if($bookmaker->notes)
                                        <p class="text-gray-400 text-xs sm:text-sm text-center mb-4 line-clamp-2">
                                            {{ $bookmaker->notes }}
                                        </p>
                                    @endif

                                    {{-- CTA Button --}}
                                    <a href="{{ $bookmaker->link }}" 
                                       target="{{ $bookmaker->target }}"
                                       class="block w-full py-3 px-4 bg-gradient-to-r from-amber-500 via-orange-500 to-orange-600 hover:from-amber-400 hover:via-orange-400 hover:to-orange-500 text-white font-bold text-sm sm:text-base rounded-lg shadow-lg hover:shadow-xl hover:shadow-amber-500/50 transition-all duration-300 text-center transform hover:scale-105 relative overflow-hidden group/btn">
                                        <span class="relative z-10 flex items-center justify-center gap-2">
                                            <span>CƯỢC NGAY</span>
                                            <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-700"></div>
                                    </a>

                                    {{-- Decorative Elements --}}
                                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-300 mb-2">Chưa có nhà cái nào</h3>
                            <p class="text-gray-400 text-sm">Vui lòng quay lại sau.</p>
                        </div>
                    @endif

                    {{-- Additional Info Section --}}
                    <div class="mt-8 pt-6 border-t border-slate-700/50">
                        <div class="bg-gradient-to-r from-slate-800/50 to-slate-900/50 rounded-lg p-4 sm:p-6 border border-slate-700/30">
                            <h2 class="text-lg sm:text-xl font-bold text-white mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tại sao chọn nhà cái từ danh sách này?
                            </h2>
                            <ul class="space-y-2 text-gray-300 text-sm sm:text-base">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Đã được kiểm chứng về độ uy tín và bảo mật</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Tỷ lệ cược cạnh tranh và hấp dẫn</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Dịch vụ chăm sóc khách hàng 24/7</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Nhiều khuyến mãi và ưu đãi hấp dẫn</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Giao dịch nhanh chóng và an toàn</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

{{-- Custom Styles --}}
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection

