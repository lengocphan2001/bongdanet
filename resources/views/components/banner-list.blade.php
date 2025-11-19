@props(['position', 'type' => 'normal'])

@php
    use App\Models\Banner;
    
    // Load all active banners for this position and type
    $banners = Banner::active()
        ->byType($type)
        ->byPosition($position)
        ->ordered()
        ->get();
@endphp

@foreach($banners as $banner)
    <x-ad-banner :banner="$banner" />
@endforeach

