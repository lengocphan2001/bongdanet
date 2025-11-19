@props(['banner'])

@php
    use Illuminate\Support\Facades\Storage;
    
    $bannerId = $banner->id;
    $storageKey = "banner_sticky_closed_{$bannerId}";
@endphp

<div id="ad-sticky-{{ $bannerId }}" 
     class="fixed top-0 left-0 right-0 z-[9998] bg-white shadow-lg border-b border-gray-200 hidden"
     data-banner-id="{{ $bannerId }}">
    <div class="container mx-auto px-2 sm:px-4 py-2 relative">
        {{-- Close Button --}}
        <button onclick="closeAdSticky({{ $bannerId }})" 
                class="absolute top-2 right-2 sm:right-4 bg-gray-100 hover:bg-gray-200 rounded-full p-1.5 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        {{-- Banner Content --}}
        <div class="flex items-center justify-center pr-10 sm:pr-12">
            @if($banner->code)
                {{-- Custom Ad Code --}}
                <div class="w-full">
                    {!! $banner->code !!}
                </div>
            @elseif($banner->image)
                {{-- Image Banner --}}
                @if($banner->link)
                    <a href="{{ $banner->link }}" 
                       target="{{ $banner->target }}" 
                       rel="nofollow noopener"
                       class="block">
                        <img src="{{ Storage::url($banner->image) }}" 
                             alt="{{ $banner->alt }}" 
                             class="max-h-20 sm:max-h-24 w-auto mx-auto">
                    </a>
                @else
                    <img src="{{ Storage::url($banner->image) }}" 
                         alt="{{ $banner->alt }}" 
                         class="max-h-20 sm:max-h-24 w-auto mx-auto">
                @endif
            @endif
        </div>
    </div>
</div>

<script>
function closeAdSticky(bannerId) {
    const sticky = document.getElementById('ad-sticky-' + bannerId);
    if (sticky) {
        sticky.classList.add('hidden');
        // Lưu vào localStorage để không hiển thị lại trong session này
        localStorage.setItem('banner_sticky_closed_' + bannerId, 'true');
        // Điều chỉnh padding top của body dựa trên các sticky banners còn lại
        setTimeout(() => {
            const allStickyBanners = document.querySelectorAll('[id^="ad-sticky-"]:not(.hidden)');
            let totalHeight = 0;
            allStickyBanners.forEach(banner => {
                totalHeight += banner.offsetHeight;
            });
            document.body.style.paddingTop = totalHeight + 'px';
        }, 100);
    }
}

// Kiểm tra và hiển thị sticky khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const bannerId = {{ $bannerId }};
    const storageKey = 'banner_sticky_closed_' + bannerId;
    
    // Chỉ hiển thị nếu chưa đóng trong session này
    if (!localStorage.getItem(storageKey)) {
        const sticky = document.getElementById('ad-sticky-' + bannerId);
        if (sticky) {
            sticky.classList.remove('hidden');
            // Điều chỉnh padding top của body để tránh che nội dung
            // Tính tổng chiều cao của tất cả sticky banners đang hiển thị
            setTimeout(() => {
                const allStickyBanners = document.querySelectorAll('[id^="ad-sticky-"]:not(.hidden)');
                let totalHeight = 0;
                allStickyBanners.forEach(banner => {
                    totalHeight += banner.offsetHeight;
                });
                document.body.style.paddingTop = totalHeight + 'px';
            }, 100);
        }
    }
});
</script>

