@php
    use App\Models\Bookmaker;
    $bookmakers = Bookmaker::active()->ordered()->get();
@endphp

@if($bookmakers->count() > 0)
<div class="mt-1 bg-gradient-to-br from-slate-800 via-slate-800 to-slate-900 rounded-xl shadow-2xl border border-slate-700/50 p-4 sm:p-4 md:p-8 overflow-hidden backdrop-blur-sm relative mb-2">
    <div class="overflow-hidden rounded-lg">
        <div id="bookmaker-slider" class="flex transition-transform duration-500 ease-in-out">
            @foreach($bookmakers as $bookmaker)
                <div class="flex-shrink-0 w-full">
                    <a href="{{ $bookmaker->link }}" target="{{ $bookmaker->target }}" class="block">
                        <div class="flex items-center justify-center py-4 sm:py-4 md:py-6 lg:py-6">
                            <img src="{{ Storage::url($bookmaker->image) }}" 
                                 alt="{{ $bookmaker->name }}" 
                                 class="w-full max-w-4xl max-h-32 sm:max-h-40 md:max-h-52 lg:max-h-64 xl:max-h-80 object-contain">
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    @if($bookmakers->count() > 1)
        <button id="bookmaker-slider-prev" class="absolute left-4 top-1/2 -translate-y-1/2 bg-slate-800/90 hover:bg-slate-700 text-white p-2 rounded-full shadow-lg z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button id="bookmaker-slider-next" class="absolute right-4 top-1/2 -translate-y-1/2 bg-slate-800/90 hover:bg-slate-700 text-white p-2 rounded-full shadow-lg z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('bookmaker-slider');
    const prevBtn = document.getElementById('bookmaker-slider-prev');
    const nextBtn = document.getElementById('bookmaker-slider-next');
    
    if (!slider) return;
    
    let currentIndex = 0;
    const totalItems = slider.children.length;
    const maxIndex = totalItems - 1;
    
    function updateSlider() {
        const container = slider.closest('.w-full');
        const containerWidth = container ? container.offsetWidth : slider.parentElement.offsetWidth;
        slider.style.transform = `translateX(-${currentIndex * containerWidth}px)`;
        
        if (prevBtn) {
            prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
            prevBtn.disabled = currentIndex === 0;
        }
        if (nextBtn) {
            nextBtn.style.opacity = currentIndex >= maxIndex ? '0.5' : '1';
            nextBtn.disabled = currentIndex >= maxIndex;
        }
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateSlider();
            }
        });
    }
    
    let autoPlay = setInterval(() => {
        currentIndex = currentIndex < maxIndex ? currentIndex + 1 : 0;
        updateSlider();
    }, 3000);
    
    slider.parentElement.addEventListener('mouseenter', () => clearInterval(autoPlay));
    slider.parentElement.addEventListener('mouseleave', () => {
        autoPlay = setInterval(() => {
            currentIndex = currentIndex < maxIndex ? currentIndex + 1 : 0;
            updateSlider();
        }, 3000);
    });
    
    window.addEventListener('resize', () => {
        clearTimeout(window.resizeTimeout);
        window.resizeTimeout = setTimeout(updateSlider, 250);
    });
    
    updateSlider();
});
</script>
@endif

