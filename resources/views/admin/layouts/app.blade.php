<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Keobongda</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-icon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo-icon.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @php
        // Determine active menu item based on current route
        $activeItem = null;
        $currentRoute = request()->route()->getName();
        
        if (str_contains($currentRoute, 'admin.predictions')) {
            $activeItem = 'predictions';
        } elseif (str_contains($currentRoute, 'admin.banners')) {
            $activeItem = 'banners';
        } elseif (str_contains($currentRoute, 'admin.bookmakers')) {
            $activeItem = 'bookmakers';
        } elseif (str_contains($currentRoute, 'admin.access-logs')) {
            $activeItem = 'access-logs';
        } elseif (str_contains($currentRoute, 'admin.users')) {
            $activeItem = 'users';
        } elseif (str_contains($currentRoute, 'admin.settings')) {
            $activeItem = 'settings';
        } elseif (str_contains($currentRoute, 'admin.dashboard')) {
            $activeItem = 'dashboard';
        }
    @endphp

    <x-admin-navbar :activeItem="$activeItem" />

    <div class="flex pt-16">
        {{-- Sidebar --}}
        <x-admin-sidebar :activeItem="$activeItem" />

        {{-- Main Content --}}
        <main class="flex-1 lg:ml-64 transition-all duration-300">
            <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>

