@props([
    'banner' => null,         // Banner model từ database
    'bannerId' => null,       // Banner ID để load từ database
    'position' => null,       // Vị trí để load banner từ database
    'image' => null,          // URL hoặc path đến hình ảnh banner
    'link' => null,           // URL khi click vào banner
    'alt' => 'Advertisement', // Alt text cho hình ảnh
    'size' => 'medium',       // small, medium, large, full-width, sidebar
    'code' => null,           // HTML/JavaScript code quảng cáo (Google Ads, etc.)
    'target' => '_blank',     // _blank, _self
    'class' => '',           // Custom CSS classes
    'sticky' => false,       // Sticky banner (cố định khi scroll)
])

@php
    use App\Models\Banner;
    use Illuminate\Support\Facades\Storage;
    
    // Load banner from database if banner, bannerId, or position is provided
    if ($banner === null) {
        if ($bannerId) {
            $banner = Banner::active()->find($bannerId);
        } elseif ($position) {
            $banner = Banner::active()->byPosition($position)->ordered()->first();
        }
    }
    
    // Use banner data if available
    if ($banner) {
        $image = $banner->image ? Storage::url($banner->image) : null;
        $link = $banner->link;
        $alt = $banner->alt;
        $size = $banner->size;
        $code = $banner->code;
        $target = $banner->target;
        $position = $banner->position;
    }
@endphp

@php
    // Size classes mapping
    $sizeClasses = [
        'small' => 'w-full max-w-[300px] h-[100px]',
        'medium' => 'w-full max-w-[728px] h-[90px]',      // Leaderboard
        'large' => 'w-full max-w-[970px] h-[250px]',      // Billboard
        'full-width' => 'w-full h-[100px]',
        'sidebar' => 'w-full max-w-[300px] h-[250px]',    // Skyscraper
        'square' => 'w-full max-w-[300px] h-[300px]',     // Square
        'rectangle' => 'w-full max-w-[300px] h-[250px]',  // Rectangle
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['medium'];
    
    // Position classes
    $positionClasses = [
        'top' => 'mb-4',
        'sidebar' => 'mb-4',
        'bottom' => 'mt-4',
        'inline' => 'my-4',
        'sticky' => 'sticky top-4 z-10',
    ];

    $positionClass = $positionClasses[$position] ?? ($position ? $positionClasses[$position] ?? '' : '');
    
    if ($sticky || ($position === 'sticky')) {
        $positionClass .= ' sticky top-4 z-10';
    }
    
    // Chỉ hiển thị nếu có banner data
    $hasBanner = ($code || $image);
@endphp

@if($hasBanner)
<div class="ad-banner {{ $positionClass }} {{ $class }}" 
     data-size="{{ $size }}" 
     data-position="{{ $position }}">
    @if($code)
        {{-- Custom Ad Code (Google Ads, etc.) --}}
        <div class="flex items-center justify-center {{ $sizeClass }} mx-auto bg-gray-100 border border-gray-200 rounded overflow-hidden">
            {!! $code !!}
        </div>
    @elseif($image)
        {{-- Image Banner --}}
        @if($link)
            <a href="{{ $link }}" 
               target="{{ $target }}" 
               rel="nofollow noopener"
               class="block {{ $sizeClass }} mx-auto overflow-hidden rounded shadow-sm hover:shadow-md transition-shadow duration-200">
                <img src="{{ $image }}" 
                     alt="{{ $alt }}" 
                     class="w-full h-full object-cover object-center">
            </a>
        @else
            <div class="{{ $sizeClass }} mx-auto overflow-hidden rounded shadow-sm">
                <img src="{{ $image }}" 
                     alt="{{ $alt }}" 
                     class="w-full h-full object-cover object-center">
            </div>
        @endif
    @endif
</div>
@endif

