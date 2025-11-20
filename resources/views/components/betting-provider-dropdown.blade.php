@props([
    'selected' => 'Bet365',
    'bookmakers' => [],
])

@php
    // Use provided bookmakers or fallback to default list
    $providers = !empty($bookmakers) ? $bookmakers : [
        'Macauslot',
        'Crown',
        'Ladbrokes',
        'Bet365',
        'William Hill',
        'Vcbet',
        'Mansion88',
        '10BET',
        '188bet',
        '12bet',
        'Sbobet',
    ];
    
    // Ensure selected is in the list
    if (!in_array($selected, $providers) && !empty($providers)) {
        $selected = $providers[0];
    }
@endphp

<div class="relative" id="betting-provider-dropdown" style="z-index: 1000;">
    <button 
        type="button"
        id="betting-provider-button"
        class="flex items-center justify-center space-x-1 text-xs font-bold text-gray-300 uppercase tracking-wider hover:text-white transition-colors"
    >
        <span id="betting-provider-selected">{{ $selected }}</span>
        <svg id="betting-provider-arrow" class="w-3 h-3 transition-transform" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    
    <div 
        id="betting-provider-menu"
        class="absolute mt-1 w-48 bg-slate-800 border border-slate-700 rounded-md shadow-xl max-h-60 overflow-y-auto hidden"
        style="z-index: 9999; right: 0; top: 100%;"
    >
        <ul class="py-1">
            @foreach ($providers as $provider)
                <li>
                    <button
                        type="button"
                        data-value="{{ $provider }}"
                        class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-slate-700 hover:text-blue-400 transition-colors betting-provider-option {{ $provider === $selected ? 'bg-slate-700 text-blue-400' : '' }}"
                    >
                        {{ $provider }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('betting-provider-dropdown');
    const button = document.getElementById('betting-provider-button');
    const menu = document.getElementById('betting-provider-menu');
    const selected = document.getElementById('betting-provider-selected');
    const arrow = document.getElementById('betting-provider-arrow');
    const options = dropdown.querySelectorAll('.betting-provider-option');

    if (!button || !menu || !selected) return;

    // Toggle dropdown
    button.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    });

    // Select option
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            let value = this.getAttribute('data-value');
            // Decode HTML entities
            value = value.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&');
            
            selected.textContent = value;
            
            // Update active state
            options.forEach(opt => {
                opt.classList.remove('bg-slate-700', 'text-blue-400');
                let optValue = opt.getAttribute('data-value');
                optValue = optValue.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&');
                if (optValue === value) {
                    opt.classList.add('bg-slate-700', 'text-blue-400');
                }
            });
            
            menu.classList.add('hidden');
            arrow.classList.remove('rotate-180');
            
            // Trigger custom event for odds update
            const event = new CustomEvent('bookmakerChanged', { detail: { bookmaker: value } });
            document.dispatchEvent(event);
        });
    });

    // Prevent scroll propagation
    menu.addEventListener('wheel', function(e) {
        e.stopPropagation();
    }, { passive: false });
    
    menu.addEventListener('touchmove', function(e) {
        e.stopPropagation();
    }, { passive: false });

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            menu.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    });
});
</script>
