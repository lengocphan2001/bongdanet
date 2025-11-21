@php
    use App\Models\Bookmaker;
    $bookmakers = Bookmaker::active()->ordered()->get();
@endphp

@if($bookmakers->count() > 0)
<div class="mt-1 overflow-hidden relative mb-2">
    <div class="relative overflow-hidden">
        <div id="bookmaker-slider" class="flex gap-2 sm:gap-3 transition-transform duration-500 ease-in-out">
            @foreach($bookmakers as $bookmaker)
                <div class="flex-shrink-0 bookmaker-item flex flex-col items-center gap-2">
                    {{-- Logo --}}
                    <a href="{{ $bookmaker->link }}" target="{{ $bookmaker->target }}" class="block w-3/4 mx-auto aspect-square overflow-hidden">
                        <img src="{{ Storage::url($bookmaker->image) }}" 
                             alt="{{ $bookmaker->name }}" 
                             class="w-full h-full object-cover">
                    </a>
                    
                    {{-- Bet Now Button --}}
                    <a href="{{ $bookmaker->link }}" target="{{ $bookmaker->target }}" 
                       class="bet-now-button inline-flex items-center justify-center px-2 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-bold text-[10px] sm:text-xs rounded-lg shadow-lg transition-all duration-300 hover:scale-105 whitespace-nowrap">
                        CƯỢC NGAY
                    </a>
                </div>
            @endforeach
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('bookmaker-slider');
    
    if (!slider) return;
    
    const totalItems = slider.children.length;
    const itemsPerSlide = 3;
    
    // Chỉ chạy autoplay nếu có nhiều hơn 3 items
    if (totalItems <= itemsPerSlide) {
        return;
    }
    
    let currentIndex = 0;
    let autoplayInterval;
    
    function calculateItemWidth() {
        const container = slider.parentElement;
        const containerWidth = container.offsetWidth;
        const gap = window.innerWidth >= 640 ? 12 : 8; // gap-3 = 12px, gap-2 = 8px
        // Tính width chính xác: (container width - 2 gaps) / 3
        const itemWidth = (containerWidth - (gap * 2)) / itemsPerSlide;
        return { itemWidth, gap };
    }
    
    function setItemWidths() {
        const { itemWidth, gap } = calculateItemWidth();
        Array.from(slider.children).forEach(item => {
            item.style.width = `${itemWidth}px`;
        });
    }
    
    function getItemWidth() {
        if (slider.children.length === 0) return 0;
        const { itemWidth, gap } = calculateItemWidth();
        return itemWidth + gap;
    }
    
    function updateSlider() {
        setItemWidths(); // Đảm bảo width được set đúng
        const itemWidth = getItemWidth();
        if (itemWidth === 0) return;
        
        // Scroll 1 item mỗi lần
        const translateX = currentIndex * itemWidth;
        slider.style.transform = `translateX(-${translateX}px)`;
    }
    
    function nextSlide() {
        currentIndex++;
        // Nếu vượt quá, quay về đầu
        if (currentIndex > totalItems - itemsPerSlide) {
            currentIndex = 0;
        }
        updateSlider();
    }
    
    function startAutoplay() {
        autoplayInterval = setInterval(nextSlide, 3000); // Tự động scroll mỗi 3 giây
    }
    
    function stopAutoplay() {
        if (autoplayInterval) {
            clearInterval(autoplayInterval);
            autoplayInterval = null;
        }
    }
    
    // Dừng autoplay khi hover, tiếp tục khi rời chuột
    const sliderContainer = slider.closest('.relative');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoplay);
        sliderContainer.addEventListener('mouseleave', startAutoplay);
    }
    
    // Recalculate on resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Reset về đầu khi resize để tránh lỗi
            currentIndex = 0;
            updateSlider();
        }, 100);
    });
    
    // Khởi tạo slider và bắt đầu autoplay
    setTimeout(() => {
        setItemWidths(); // Set width cho tất cả items
        updateSlider();
        startAutoplay();
    }, 100);
});
</script>

<style>
@keyframes blink {
    0%, 100% {
        opacity: 1;
        box-shadow: 0 0 15px rgba(251, 191, 36, 0.6), 0 0 30px rgba(251, 191, 36, 0.4);
    }
    50% {
        opacity: 0.8;
        box-shadow: 0 0 25px rgba(251, 191, 36, 0.8), 0 0 50px rgba(251, 191, 36, 0.6);
    }
}

.bet-now-button {
    animation: blink 1.5s ease-in-out infinite;
}

/* Xóa tất cả border */
.mt-1.overflow-hidden,
.mt-1.overflow-hidden > div,
#bookmaker-slider,
#bookmaker-slider > div,
.bookmaker-item,
.bookmaker-item *,
.bookmaker-item img,
.bookmaker-item a {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}
</style>
@endif

