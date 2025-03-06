@extends('layouts.theme')

@section('content')
<div class="bg-gray-950 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Find Your Perfect Ride</h1>
            <p class="text-xl text-gray-400 max-w-3xl mx-auto">Book a professional driver with just a few clicks and enjoy a comfortable journey to your destination.</p>
            
            @guest
                <div class="mt-8 flex justify-center gap-4">
                    <a href="{{ route('login') }}" class="btn-secondary">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary">
                        <i class="fas fa-user-plus mr-2"></i>
                        Register
                    </a>
                </div>
            @else
                <div class="mt-8">
                    <a href="{{ route('booking.create') }}" class="btn-primary inline-flex items-center">
                        <i class="fas fa-car mr-2"></i>
                        Book a Ride Now
                    </a>
                </div>
            @endguest
        </div>

        <!-- Search and Filter Section -->
        <div class="mb-12">
            <div class="card p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-yellow-500"></i>
                        </div>
                        <input type="text" id="search-drivers" placeholder="Search drivers by name or car model..." 
                            class="form-input pl-10 w-full">
                    </div>
                    
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-map-marker-alt text-yellow-500"></i>
                        </div>
                        <select id="filter-city" class="form-select pl-10 w-full">
                            <option value="">All Cities</option>
                            <!-- Cities will be populated with JS -->
                        </select>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <input type="checkbox" id="available-now" class="form-checkbox">
                            <label for="available-now" class="ml-2 text-gray-300">Available Now</label>
                        </div>
                        
                        <div class="flex items-center ml-6">
                            <input type="checkbox" id="highest-rated" class="form-checkbox">
                            <label for="highest-rated" class="ml-2 text-gray-300">Highest Rated</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drivers Grid -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6">Our Professional Drivers</h2>
            
            @if($drivers->isEmpty())
                <div class="card p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-800 mb-4">
                        <i class="fas fa-car text-yellow-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white">No drivers available</h3>
                    <p class="text-gray-400 mt-2">We currently don't have any drivers registered in our system.</p>
                </div>
            @else
                <div id="drivers-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($drivers as $driver)
                        <div class="driver-card card overflow-hidden" 
                             data-name="{{ $driver->name }}" 
                             data-car="{{ $driver->driverProfile->car_model ?? '' }}"
                             data-city="{{ $driver->driverProfile->city ?? '' }}">
                            <!-- Driver Header with Background -->
                            <div class="h-32 bg-gradient-to-r from-gray-800 to-gray-900 relative">
                                <!-- Available indicator -->
                                @php
                                    $isAvailable = false;
                                    if ($driver->driverProfile && $driver->driverProfile->work_days) {
                                        $today = strtolower(date('l'));
                                        $workDays = array_map('strtolower', $driver->driverProfile->work_days);
                                        $isAvailable = in_array($today, $workDays);
                                        
                                        // Check time if work hours are defined
                                        if ($isAvailable && $driver->driverProfile->work_start && $driver->driverProfile->work_end) {
                                            $currentTime = date('H:i:s');
                                            $startTime = $driver->driverProfile->work_start;
                                            $endTime = $driver->driverProfile->work_end;
                                            
                                            if ($endTime > $startTime) {
                                                // Normal day shift
                                                $isAvailable = $currentTime >= $startTime && $currentTime <= $endTime;
                                            } else {
                                                // Overnight shift
                                                $isAvailable = $currentTime >= $startTime || $currentTime <= $endTime;
                                            }
                                        }
                                    }
                                @endphp
                                
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $isAvailable ? 'bg-green-500 bg-opacity-20 text-green-400' : 'bg-red-500 bg-opacity-20 text-red-400' }}">
                                        {{ $isAvailable ? 'Available Now' : 'Unavailable' }}
                                    </span>
                                </div>
                                
                                <!-- Driver profile image -->
                                <div class="absolute -bottom-10 left-6">
                                    <div class="w-20 h-20 rounded-full bg-gray-700 border-4 border-gray-800 overflow-hidden">
                                        @if($driver->driverProfile && $driver->driverProfile->profile_picture)
                                            <img src="{{ Storage::url($driver->driverProfile->profile_picture) }}" 
                                                 alt="{{ $driver->name }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full w-full bg-yellow-500 text-gray-900">
                                                <span class="text-2xl font-bold">{{ substr($driver->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Driver Info -->
                            <div class="pt-12 px-6 pb-6">
                                <h3 class="text-xl font-bold text-white">
                                    <a href="{{ route('profiles.public', $driver->id) }}" class="hover:text-yellow-500">
                                        {{ $driver->name }}
                                    </a>
                                </h3>
                                
                                <!-- Rating -->
                                <div class="flex items-center mt-1">
                                    <div class="flex text-yellow-500">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($driver->average_rating))
                                                <i class="fas fa-star"></i>
                                            @elseif($i - 0.5 <= $driver->average_rating)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-yellow-500 font-semibold">{{ number_format($driver->average_rating, 1) }}</span>
                                    <span class="ml-1 text-gray-400">({{ $driver->reviews_count }} {{ Str::plural('review', $driver->reviews_count) }})</span>
                                </div>
                                
                                <!-- Driver Details -->
                                <div class="mt-4 space-y-2">
                                    @if($driver->driverProfile->car_model)
                                        <div class="flex items-center text-gray-400">
                                            <i class="fas fa-car text-yellow-500 w-5"></i>
                                            <span class="ml-2">{{ $driver->driverProfile->car_model }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($driver->driverProfile->city)
                                        <div class="flex items-center text-gray-400">
                                            <i class="fas fa-map-marker-alt text-yellow-500 w-5"></i>
                                            <span class="ml-2">{{ $driver->driverProfile->city }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($driver->driverProfile && $driver->driverProfile->work_days)
                                        <div class="flex items-center text-gray-400">
                                            <i class="fas fa-calendar-alt text-yellow-500 w-5"></i>
                                            <span class="ml-2">{{ implode(', ', array_slice($driver->driverProfile->work_days, 0, 3)) }}{{ count($driver->driverProfile->work_days) > 3 ? '...' : '' }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Driver Description (truncated) -->
                                @if($driver->driverProfile->description)
                                    <div class="mt-4">
                                        <p class="text-gray-400 text-sm line-clamp-2">{{ $driver->driverProfile->description }}</p>
                                    </div>
                                @endif
                                
                                <!-- Action Buttons -->
                                <div class="mt-6 flex justify-between">
                                    <a href="{{ route('profiles.public', $driver->id) }}" class="btn-secondary text-sm">
                                        <i class="fas fa-user mr-1"></i>
                                        View Profile
                                    </a>
                                    
                                    @auth
                                        <a href="{{ route('booking.create', ['driver_id' => $driver->id]) }}" class="btn-primary text-sm">
                                            <i class="fas fa-taxi mr-1"></i>
                                            Book Now
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}?redirect=booking&driver_id={{ $driver->id }}" class="btn-primary text-sm">
                                            <i class="fas fa-taxi mr-1"></i>
                                            Book Now
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Features Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-8 text-center">Why Choose Our Service</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card p-6 text-center hover:border-yellow-500 transition-colors duration-300">
                    <div class="w-14 h-14 rounded-full bg-yellow-500 bg-opacity-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-yellow-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Safety First</h3>
                    <p class="text-gray-400">All our drivers are verified and background-checked for your peace of mind.</p>
                </div>
                
                <div class="card p-6 text-center hover:border-yellow-500 transition-colors duration-300">
                    <div class="w-14 h-14 rounded-full bg-yellow-500 bg-opacity-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-money-bill-wave text-yellow-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Transparent Pricing</h3>
                    <p class="text-gray-400">No hidden fees or surprise charges. Pay only for what you see.</p>
                </div>
                
                <div class="card p-6 text-center hover:border-yellow-500 transition-colors duration-300">
                    <div class="w-14 h-14 rounded-full bg-yellow-500 bg-opacity-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-yellow-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">24/7 Availability</h3>
                    <p class="text-gray-400">Need a ride at any time? Our service is available round the clock.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Collect all unique cities for the filter
        const cities = new Set();
        document.querySelectorAll('.driver-card').forEach(card => {
            const city = card.getAttribute('data-city');
            if (city) cities.add(city);
        });
        
        // Populate city filter
        const cityFilter = document.getElementById('filter-city');
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            cityFilter.appendChild(option);
        });
        
        // Search functionality
        const searchInput = document.getElementById('search-drivers');
        searchInput.addEventListener('input', filterDrivers);
        
        // City filter
        cityFilter.addEventListener('change', filterDrivers);
        
        // Checkboxes
        document.getElementById('available-now').addEventListener('change', filterDrivers);
        document.getElementById('highest-rated').addEventListener('change', filterDrivers);
        
        function filterDrivers() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCity = cityFilter.value.toLowerCase();
            const availableOnly = document.getElementById('available-now').checked;
            const highestRated = document.getElementById('highest-rated').checked;
            
            document.querySelectorAll('.driver-card').forEach(card => {
                const driverName = card.getAttribute('data-name').toLowerCase();
                const carModel = card.getAttribute('data-car').toLowerCase();
                const city = card.getAttribute('data-city').toLowerCase();
                const isAvailable = card.querySelector('.bg-green-500') !== null;
                
                // Check if card matches all active filters
                const matchesSearch = driverName.includes(searchTerm) || carModel.includes(searchTerm);
                const matchesCity = !selectedCity || city === selectedCity;
                const matchesAvailability = !availableOnly || isAvailable;
                
                // Show/hide based on filter results
                if (matchesSearch && matchesCity && matchesAvailability) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // If highest rated is checked, sort visible cards
            if (highestRated) {
                const driversGrid = document.getElementById('drivers-grid');
                const cards = Array.from(driversGrid.children);
                
                // Only sort visible cards
                const visibleCards = cards.filter(card => card.style.display !== 'none');
                
                visibleCards.sort((a, b) => {
                    const ratingA = parseFloat(a.querySelector('.text-yellow-500.font-semibold').textContent);
                    const ratingB = parseFloat(b.querySelector('.text-yellow-500.font-semibold').textContent);
                    return ratingB - ratingA;
                });
                
                // Re-append sorted cards
                visibleCards.forEach(card => {
                    driversGrid.appendChild(card);
                });
            }
        }
    });
</script>
@endpush
@endsection