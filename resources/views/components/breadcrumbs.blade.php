@props([
    'items' => [],
])

<div class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 py-2">
        <nav class="text-sm text-gray-600 flex items-center space-x-2">
            @foreach ($items as $index => $item)
                @if ($loop->first)
                    {{-- First item (parent) - Bold --}}
                    <a href="{{ $item['url'] ?? '#' }}" class="font-bold hover:text-green-600 transition-colors">
                        {{ $item['label'] }}
                    </a>
                @else
                    {{-- Separator icon --}}
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    {{-- Child items - Medium --}}
                    @if (isset($item['url']))
                        <a href="{{ $item['url'] }}" class="font-medium text-gray-900 hover:text-green-600 transition-colors">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="font-medium text-gray-900">{{ $item['label'] }}</span>
                    @endif
                @endif
            @endforeach
        </nav>
    </div>
</div>

