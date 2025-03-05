<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Admin Dashboard - {{ config('app.name', 'TaxiApp') }}</title>
    
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <div class="flex items-center mb-6">
                    <div class="bg-yellow-500 p-2 rounded-full mr-2">
                        <i class="fas fa-taxi text-black text-xl"></i>
                    </div>
                    <span class="text-xl font-bold">TaxiApp Admin</span>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="block p-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}" class="block p-2 rounded {{ request()->routeIs('admin.users') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-users mr-2"></i> Users
                    </a>
                    <a href="{{ route('admin.bookings') }}" class="block p-2 rounded {{ request()->routeIs('admin.bookings') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-check mr-2"></i> Bookings
                    </a>
                    <a href="{{ route('admin.drivers') }}" class="block p-2 rounded {{ request()->routeIs('admin.drivers') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-id-card mr-2"></i> Drivers
                    </a>
                    <a href="{{ route('admin.statistics') }}" class="block p-2 rounded {{ request()->routeIs('admin.statistics') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-chart-bar mr-2"></i> Statistics
                    </a>
                    
                    <hr class="my-4 border-gray-700">
                    
                    <a href="{{ route('dashboard') }}" class="block p-2 rounded hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Site
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">@yield('header', 'Dashboard')</h1>
                </div>
            </header>
            
            <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>