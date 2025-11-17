<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Bongdanet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-[#1a5f2f] text-white shadow-lg">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center h-auto sm:h-16 py-2 sm:py-0 space-y-2 sm:space-y-0">
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('admin.predictions.index') }}" class="text-lg sm:text-xl font-bold">Admin Panel</a>
                    <a href="{{ route('admin.predictions.index') }}" class="text-sm sm:text-base hover:text-gray-300">Nhận định</a>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                    <span class="text-xs sm:text-sm">Xin chào, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 sm:px-4 py-1 sm:py-2 rounded text-xs sm:text-sm w-full sm:w-auto">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

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
</body>
</html>

