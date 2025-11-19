@props(['banner'])

@php
    use Illuminate\Support\Facades\Storage;
    
    $bannerId = $banner->id;
    $storageKey = "banner_modal_closed_{$bannerId}";
@endphp

<div id="ad-modal-{{ $bannerId }}" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm hidden"
     data-banner-id="{{ $bannerId }}"
     onclick="if(event.target === this) closeAdModal({{ $bannerId }})">
    <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-auto" onclick="event.stopPropagation()">
        {{-- Close Button --}}
        <button onclick="closeAdModal({{ $bannerId }})" 
                class="absolute top-2 right-2 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors"
                aria-label="Đóng">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        {{-- Banner Content --}}
        <div class="p-4">
            @if($banner->code)
                {{-- Custom Ad Code --}}
                <div class="flex items-center justify-center min-h-[300px]">
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
                             class="w-full h-auto rounded">
                    </a>
                @else
                    <img src="{{ Storage::url($banner->image) }}" 
                         alt="{{ $banner->alt }}" 
                         class="w-full h-auto rounded">
                @endif
            @endif
        </div>
    </div>
</div>

<script>
function closeAdModal(bannerId) {
    const modal = document.getElementById('ad-modal-' + bannerId);
    if (modal) {
        modal.classList.add('hidden');
        // Lưu vào localStorage để không hiển thị lại trong session này
        localStorage.setItem('banner_modal_closed_' + bannerId, 'true');
    }
}

// Kiểm tra và hiển thị modal khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const bannerId = {{ $bannerId }};
    const storageKey = 'banner_modal_closed_' + bannerId;
    
    // Chỉ hiển thị nếu chưa đóng trong session này
    if (!localStorage.getItem(storageKey)) {
        const modal = document.getElementById('ad-modal-' + bannerId);
        if (modal) {
            // Hiển thị sau 500ms để trang load xong
            setTimeout(() => {
                modal.classList.remove('hidden');
            }, 500);
        }
    }
});
</script>

