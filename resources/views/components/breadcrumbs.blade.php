@props([
    'items' => [],
])

<div class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-2 sm:px-4 py-2">
        <nav class="text-xs sm:text-sm text-gray-600 flex flex-wrap items-center gap-1 sm:gap-2">
            @foreach ($items as $index => $item)
                @if ($loop->first)
                    {{-- First item (parent) - Bold --}}
                    <a href="{{ $item['url'] ?? '#' }}" class="font-bold hover:text-green-600 transition-colors break-words">
                        {{ $item['label'] }}
                    </a>
                @else
                    {{-- Separator icon --}}
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    {{-- Child items - Medium --}}
                    @if (isset($item['url']))
                        <a href="{{ $item['url'] }}" class="font-medium text-gray-900 hover:text-green-600 transition-colors break-words">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="font-medium text-gray-900 break-words">{{ $item['label'] }}</span>
                    @endif
                @endif
            @endforeach
        </nav>
    </div>
</div>

