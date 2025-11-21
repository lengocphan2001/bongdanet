@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, outline
    'size' => 'md', // sm, md, lg
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95';
    
    $variantClasses = [
        'primary' => 'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-500 hover:to-blue-600 focus:ring-blue-500 shadow-blue-500/25',
        'secondary' => 'bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-500 hover:to-gray-600 focus:ring-gray-500 shadow-gray-500/25',
        'danger' => 'bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-500 hover:to-red-600 focus:ring-red-500 shadow-red-500/25',
        'outline' => 'border-2 border-slate-600 text-gray-300 hover:bg-slate-700/50 hover:border-slate-500 focus:ring-slate-500 shadow-slate-500/10',
    ];
    
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ];
    
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
    
    $tag = $href ? 'a' : 'button';
    $attributes = $href 
        ? $attributes->merge(['href' => $href, 'class' => $classes])
        : $attributes->merge(['type' => $type, 'class' => $classes]);
@endphp

<{{ $tag }} {{ $attributes }}>
    {{ $slot }}
</{{ $tag }}>

