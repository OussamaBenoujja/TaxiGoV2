<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TaxiApp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Pusher -->
        <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
        
        <!-- Additional Styles -->
        <style>
            [x-cloak] { display: none !important; }
            
            .btn-primary {
                @apply bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200;
            }
            
            .btn-secondary {
                @apply bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow border border-gray-700 transition-colors duration-200;
            }
            
            .btn-danger {
                @apply bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200;
            }
            
            .card {
                @apply bg-gray-900 border border-gray-800 rounded-lg shadow-md overflow-hidden;
            }
            
            .card-header {
                @apply bg-gray-800 px-6 py-4 border-b border-gray-700;
            }
            
            .form-input {
                @apply mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-500/30;
            }
            
            .form-select {
                @apply mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-500/30;
            }
            
            .form-checkbox {
                @apply rounded text-yellow-500 border-gray-700 bg-gray-800 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-500/30;
            }
            
            .form-label {
                @apply block font-medium text-gray-300;
            }
        </style>
    </head>
    <body class="font-sans antialiased min-h-screen bg-gray-950 text-gray-100">
        <div class="flex flex-col min-h-screen">
            <header class="bg-gray-900 border-b border-gray-800 shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('dashboard') }}" class="flex items-center">
                                    <div class="bg-yellow-500 p-2 rounded-full mr-2">
                                        <i class="fas fa-taxi text-black text-xl"></i>
                                    </div>
                                    <span class="text-xl font-bold text-white">TaxiApp</span>
                                </a>
                            </div>
                            
                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:ml-10 sm:flex">
                                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-yellow-500 border-b-2 border-yellow-500' : 'text-gray-300 hover:text-yellow-500' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                                    Dashboard
                                </a>
                                @auth
                                    @if(Auth::user()->role == 'client')
                                        <a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.create') ? 'text-yellow-500 border-b-2 border-yellow-500' : 'text-gray-300 hover:text-yellow-500' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                                            Book a Taxi
                                        </a>
                                    @endif
                                    
                                    @if(Auth::user()->role !== 'driver')
                                        <a href="{{ route('become.driver') }}" class="{{ request()->routeIs('become.driver') ? 'text-yellow-500 border-b-2 border-yellow-500' : 'text-gray-300 hover:text-yellow-500' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                                            Become a Driver
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                        
                        <!-- Settings Dropdown -->
                        @auth
                            <div class="flex items-center ml-6">
                                <div id="message-notification" class="relative mr-4">
                                    <a href="#" class="text-gray-400 hover:text-yellow-500 transition-colors duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </a>
                                    <span id="unread-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                                </div>
                                
                                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                    <div>
                                        <button @click="open = !open" class="flex text-sm bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                            <span class="sr-only">Open user menu</span>
                                            <div class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center text-black font-bold">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </div>
                                        </button>
                                    </div>
                                    
                                    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 py-1 bg-gray-800 rounded-md shadow-lg z-50">
                                        <div class="px-4 py-2 text-xs text-gray-400">
                                            Logged in as <span class="font-medium text-yellow-500">{{ Auth::user()->name }}</span>
                                        </div>
                                        
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-yellow-500">
                                            Profile
                                        </a>
                                        
                                        <!-- Authentication -->
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-yellow-500">
                                                Log Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('login') }}" class="text-gray-300 hover:text-yellow-500 text-sm font-medium">Log in</a>
                                <a href="{{ route('register') }}" class="bg-yellow-500 hover:bg-yellow-600 text-black py-2 px-4 rounded text-sm font-medium transition-colors duration-150">Register</a>
                            </div>
                        @endauth
                        
                        <!-- Mobile menu button -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button x-data="{ open: false }" @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-yellow-500">
                                <span class="sr-only">Open main menu</span>
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Mobile Navigation Menu -->
            <div class="sm:hidden bg-gray-900" x-data="{ open: false }" x-show="open">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-gray-800 text-yellow-500' : 'text-gray-300 hover:bg-gray-700 hover:text-yellow-500' }} block px-3 py-2 rounded-md text-base font-medium">
                        Dashboard
                    </a>
                    @auth
                        @if(Auth::user()->role == 'client')
                            <a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.create') ? 'bg-gray-800 text-yellow-500' : 'text-gray-300 hover:bg-gray-700 hover:text-yellow-500' }} block px-3 py-2 rounded-md text-base font-medium">
                                Book a Taxi
                            </a>
                        @endif
                        
                        @if(Auth::user()->role !== 'driver')
                            <a href="{{ route('become.driver') }}" class="{{ request()->routeIs('become.driver') ? 'bg-gray-800 text-yellow-500' : 'text-gray-300 hover:bg-gray-700 hover:text-yellow-500' }} block px-3 py-2 rounded-md text-base font-medium">
                                Become a Driver
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
            
            <!-- Page Content -->
            <main class="flex-grow">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-gray-900 border-t border-gray-800 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0">
                            <div class="flex items-center">
                                <div class="bg-yellow-500 p-2 rounded-full mr-2">
                                    <i class="fas fa-taxi text-black text-xl"></i>
                                </div>
                                <span class="text-lg font-bold text-white">TaxiApp</span>
                            </div>
                            <p class="text-gray-400 text-sm mt-2">Safe and reliable transportation</p>
                        </div>
                        
                        <div class="flex space-x-6">
                            <a href="#" class="text-gray-400 hover:text-yellow-500">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-yellow-500">
                                <i class="fab fa-facebook text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-yellow-500">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-8 border-t border-gray-800 pt-4 text-center text-gray-400 text-sm">
                        <p>&copy; {{ date('Y') }} TaxiApp. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
        
        @stack('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check for unread messages every 60 seconds for authenticated users
                @auth
                function checkUnreadMessages() {
                    fetch('{{ route("messages.unread-count") }}')
                        .then(response => response.json())
                        .then(data => {
                            const badge = document.getElementById('unread-badge');
                            if (badge) {
                                if (data.unreadCount > 0) {
                                    badge.textContent = data.unreadCount;
                                    badge.classList.remove('hidden');
                                } else {
                                    badge.classList.add('hidden');
                                }
                            }
                        });
                }
                
                // Check on page load and periodically
                checkUnreadMessages();
                setInterval(checkUnreadMessages, 60000);
                @endauth
            });
        </script>
    </body>
</html>