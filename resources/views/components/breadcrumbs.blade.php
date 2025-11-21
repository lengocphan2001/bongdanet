@props([
    'items' => [],
])

<div class="bg-gradient-to-r from-slate-800/95 via-slate-800/90 to-slate-900/95 border-b border-slate-700/50 backdrop-blur-sm">
    <div class="container mx-auto px-2 sm:px-4 py-3">
        <nav class="text-xs sm:text-sm flex flex-wrap items-center gap-2 sm:gap-3">
            @foreach ($items as $index => $item)
                @if ($loop->first)
                    {{-- First item (parent) - Bold with icon --}}
                    <a href="{{ $item['url'] ?? '#' }}" class="inline-flex items-center gap-1.5 font-bold text-white hover:text-blue-400 transition-colors duration-200 break-words group">
                        <svg class="w-3 h-3 text-blue-400 group-hover:text-blue-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @else
                    {{-- Separator icon --}}
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    {{-- Child items - Medium --}}
                    @if (isset($item['url']))
                        <a href="{{ $item['url'] }}" class="font-medium text-gray-300 hover:text-blue-400 transition-colors duration-200 break-words">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="font-medium text-gray-200 break-words">{{ $item['label'] }}</span>
                    @endif
                @endif
            @endforeach
        </nav>
    </div>
</div>

