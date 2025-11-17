@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, outline
    'size' => 'md', // sm, md, lg
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';
    
    $variantClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'outline' => 'border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-gray-500',
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

