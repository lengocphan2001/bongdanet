<div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-xl border border-slate-700/50 rounded-xl overflow-hidden w-full backdrop-blur-sm">
    {{-- Header with gradient bar --}}
    <div class="bg-gradient-to-r from-amber-600/90 to-orange-700/90 px-4 py-3.5 border-b border-amber-500/30">
        <div class="flex items-center space-x-3">
            <div class="w-1 h-6 bg-gradient-to-b from-white/80 to-white/40 rounded-full"></div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wide">NHẬN ĐỊNH BÓNG ĐÁ HÔM NAY</h2>
        </div>
    </div>

    {{-- Content List --}}
    <div class="bg-slate-800/50 py-2">
        @php
            // Use predictions from View Composer (database data) or fallback to empty array
            $predictions = $predictionsToday ?? [];
        @endphp
        
        @forelse ($predictions as $index => $prediction)
            <a href="{{ $prediction['url'] ?? '#' }}" class="block px-4 py-3 {{ $index < count($predictions) - 1 ? 'border-b border-slate-700/50' : '' }} hover:bg-gradient-to-r hover:from-amber-600/10 hover:to-orange-700/10 transition-all duration-200 cursor-pointer group">
                <p class="text-xs text-gray-100 leading-relaxed group-hover:text-amber-400 transition-colors">
                    <span class="font-semibold text-amber-400">{{ $prediction['type'] ?? 'Nhận định, nhận định' }}</span>
                    <span class="text-gray-300"> {{ $prediction['teams'] ?? 'N/A' }}, {{ $prediction['time'] ?? 'N/A' }}</span>
                    @if (!empty($prediction['comment']))
                        <span class="text-gray-400">: {{ $prediction['comment'] }}</span>
                    @endif
                </p>
            </a>
        @empty
            <div class="px-4 py-10 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-800/50 border border-slate-700/50 mb-3">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-xs text-gray-400 font-medium">Chưa có nhận định nào hôm nay</p>
            </div>
        @endforelse
    </div>
</div>

