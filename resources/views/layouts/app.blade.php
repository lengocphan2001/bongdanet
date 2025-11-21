<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/bongdanet-logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/bongdanet-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/bongdanet-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-gray-100">
    <div id="app">
        {{-- Modal Quảng Cáo --}}
        @php
            use App\Models\Banner;
            $modalBanners = Banner::active()->byType('modal')->ordered()->get();
            $stickyBanners = Banner::active()->byType('sticky')->ordered()->get();
        @endphp
        
        @foreach($modalBanners as $banner)
            <x-ad-modal :banner="$banner" />
        @endforeach

        {{-- Sticky Banner ở đầu trang --}}
        @foreach($stickyBanners as $banner)
            <x-ad-sticky :banner="$banner" />
        @endforeach

        @include('partials.header')

        <main class="pt-16">
            @yield('content')
            <x-affiliate-partners />
        </main>

        {{-- Affiliate Partners Section --}}
        

        @include('partials.footer')
        
        @include('partials.bottom-menu')
    </div>
    
    <script>
    // Remove refresh parameter from URL if present (clean URL)
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('refresh')) {
            urlParams.delete('refresh');
            const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }
    })();
    
    // Unregister ALL service workers to prevent any caching
    // Home page data must always be fresh, no caching allowed
    if ('serviceWorker' in navigator) {
        // Unregister all service workers
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for(let registration of registrations) {
                registration.unregister().then(function(success) {
                    if (success) {
                        console.log('Service Worker unregistered');
                    }
                });
            }
        });
        
        // Also clear all caches
        if ('caches' in window) {
            caches.keys().then(function(cacheNames) {
                return Promise.all(
                    cacheNames.map(function(cacheName) {
                        return caches.delete(cacheName);
                    })
                );
            }).then(function() {
                console.log('All caches cleared');
            });
        }
    }
    </script>
</body>
</html>

