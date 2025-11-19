<div class="bg-white shadow-sm border border-gray-200 overflow-hidden w-full">
    {{-- Header with green bar --}}
    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
        <div class="flex items-center space-x-2">
            <div class="w-1 h-5 bg-green-600"></div>
            <h2 class="text-sm font-bold uppercase">NHẬN ĐỊNH BÓNG ĐÁ HÔM NAY</h2>
        </div>
    </div>

    {{-- Content List --}}
    <div class="bg-gray-50 py-2">
        @php
            // Use predictions from View Composer (database data) or fallback to empty array
            $predictions = $predictionsToday ?? [];
        @endphp
        
        @forelse ($predictions as $index => $prediction)
            <a href="{{ $prediction['url'] ?? '#' }}" class="block px-4 py-2 {{ $index < count($predictions) - 1 ? 'border-b border-gray-200' : '' }} hover:bg-green-50 transition-colors duration-200 cursor-pointer group">
                <p class="text-xs text-gray-900 leading-relaxed group-hover:text-green-600">
                    <span class="font-medium">{{ $prediction['type'] ?? 'Nhận định, nhận định' }}</span>
                    <span class="text-gray-700"> {{ $prediction['teams'] ?? 'N/A' }}, {{ $prediction['time'] ?? 'N/A' }}</span>
                    @if (!empty($prediction['comment']))
                        <span class="text-gray-600">: {{ $prediction['comment'] }}</span>
                    @endif
                </p>
            </a>
        @empty
            <div class="px-4 py-8 text-center">
                <p class="text-xs text-gray-500">Chưa có nhận định nào hôm nay</p>
            </div>
        @endforelse
    </div>
</div>

