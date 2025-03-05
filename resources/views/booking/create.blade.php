@extends('layouts.theme')

@section('content')
<div class="bg-gradient-to-br from-gray-950 to-gray-900 min-h-screen py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header with Animation -->
        <div class="flex flex-col items-center justify-center mb-12">
            <div class="bg-yellow-500 p-5 rounded-full shadow-lg mb-6 transform transition-all duration-500 hover:scale-110">
                <i class="fas fa-taxi text-black text-4xl"></i>
            </div>
            <h1 class="text-5xl font-bold text-white text-center mb-3 tracking-tight">Book Your Taxi</h1>
            <div class="h-1 w-20 bg-yellow-500 rounded mb-6"></div>
            <p class="text-gray-300 text-lg text-center max-w-2xl">Quick, reliable, and safe transportation at your fingertips</p>
        </div>
        
        <div class="max-w-4xl mx-auto">
            <div class="bg-gray-900 rounded-2xl overflow-hidden shadow-2xl border border-gray-800">
                <!-- Header with gradient -->
                <div class="h-40 bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 relative">
                    <div class="absolute bottom-0 left-0 right-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-20">
                            <path fill="#111827" fill-opacity="1" d="M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,85.3C672,75,768,85,864,112C960,139,1056,181,1152,181.3C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                        </svg>
                    </div>
                    <div class="absolute top-6 left-8 text-black">
                        <div class="flex items-center">
                            <div class="bg-black bg-opacity-20 rounded-full p-2 mr-3">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <span class="text-base font-semibold uppercase tracking-wider">Safe & Quick Rides</span>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Form -->
                <form method="POST" action="{{ route('booking.store') }}" class="p-8" id="booking-form">
                    @csrf
                    
                    <!-- Progress Steps -->
                    <div class="flex justify-between items-center mb-10 px-2">
                        <div class="flex flex-col items-center relative z-10">
                            <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center text-black font-bold shadow-lg">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <span class="text-sm font-medium mt-2 text-yellow-500">Select Driver</span>
                        </div>
                        <div class="h-1 flex-1 bg-gray-700 mx-2 rounded-full relative">
                            <div class="h-1 bg-yellow-500 rounded-full absolute top-0 left-0" style="width: 33%;"></div>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 font-bold shadow border border-gray-700">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span class="text-sm font-medium mt-2 text-gray-400">Choose Time</span>
                        </div>
                        <div class="h-1 flex-1 bg-gray-700 mx-2 rounded-full">
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 font-bold shadow border border-gray-700">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="text-sm font-medium mt-2 text-gray-400">Confirm</span>
                        </div>
                    </div>

                    <!-- Driver Selection -->
                    <div class="mb-8">
                        <label for="driver_id" class="block font-medium text-gray-200 mb-3 text-lg flex items-center">
                            <i class="fas fa-user-tie mr-3 text-yellow-500"></i>
                            <span>Select A Driver</span>
                        </label>
                        <div class="relative">
                            <select name="driver_id" id="driver_id" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 pl-12 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 appearance-none transition-colors duration-200">
                                <option value="" selected disabled>Select a driver</option>
                                @foreach($drivers as $driver)
                                    <option 
                                        value="{{ $driver->id }}"
                                        data-car_model="{{ $driver->driverProfile->car_model ?? 'Unknown' }}"
                                        data-work_days="{{ json_encode($driver->driverProfile->work_days ?? []) }}"
                                        data-work_start="{{ $driver->driverProfile->work_start ?? 'N/A' }}"
                                        data-work_end="{{ $driver->driverProfile->work_end ?? 'N/A' }}"
                                    >
                                        {{ $driver->name }} - {{ $driver->driverProfile->car_model ?? 'No car info' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-user text-yellow-500"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Driver Profile Card -->
                    <div id="driver-profile" class="mb-10 hidden transform transition-all duration-300">
                        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 shadow-lg">
                            <div class="flex items-center mb-6">
                                <div class="bg-yellow-500 bg-opacity-20 p-4 rounded-full mr-4">
                                    <i class="fas fa-id-card text-yellow-500 text-xl"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-white">Driver Profile</h2>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div class="flex items-center p-4 bg-gray-900 rounded-lg shadow-md border border-gray-800 transition-transform duration-300 hover:transform hover:scale-105">
                                    <div class="mr-4 text-yellow-500 text-xl">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase tracking-wide">Car Model</p>
                                        <p class="font-medium text-white text-lg" id="car_model">N/A</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center p-4 bg-gray-900 rounded-lg shadow-md border border-gray-800 transition-transform duration-300 hover:transform hover:scale-105">
                                    <div class="mr-4 text-yellow-500 text-xl">
                                        <i class="fas fa-calendar-week"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase tracking-wide">Work Days</p>
                                        <p class="font-medium text-white text-lg" id="work_days">N/A</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center p-4 bg-gray-900 rounded-lg shadow-md border border-gray-800 transition-transform duration-300 hover:transform hover:scale-105">
                                    <div class="mr-4 text-yellow-500 text-xl">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase tracking-wide">Working Hours</p>
                                        <p class="font-medium text-white text-lg" id="work_hours">N/A</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date and time selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="pickup_date" class="block font-medium text-gray-200 mb-3 text-lg flex items-center">
                                <i class="fas fa-calendar-alt mr-3 text-yellow-500"></i>
                                <span>Pickup Date</span>
                            </label>
                            <div class="relative">
                                <input type="date" name="pickup_date" id="pickup_date" required 
                                       class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 pl-12 
                                              focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 
                                              transition-colors duration-200">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                    <i class="fas fa-calendar text-yellow-500"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="pickup_time" class="block font-medium text-gray-200 mb-3 text-lg flex items-center">
                                <i class="fas fa-clock mr-3 text-yellow-500"></i>
                                <span>Pickup Time</span>
                            </label>
                            <div class="relative">
                                <input type="time" name="pickup_time" id="pickup_time" required 
                                       class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 pl-12 
                                              focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 
                                              transition-colors duration-200">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                    <i class="fas fa-clock text-yellow-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Locations -->
                    <div class="mb-6">
                        <label for="pickup_place" class="block font-medium text-gray-200 mb-3 text-lg flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-yellow-500"></i>
                            <span>Pickup Location</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="pickup_place" id="pickup_place" required placeholder="Enter your pickup location" 
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 pl-12 
                                          focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 
                                          transition-colors duration-200">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-map-pin text-yellow-500"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-10">
                        <label for="destination" class="block font-medium text-gray-200 mb-3 text-lg flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-yellow-500"></i>
                            <span>Destination</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="destination" id="destination" required placeholder="Enter your destination" 
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 pl-12 
                                          focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 
                                          transition-colors duration-200">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-map-pin text-yellow-500"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Popular destinations -->
                    <div class="mb-10">
                        <h3 class="text-lg font-medium text-white mb-4 flex items-center">
                            <i class="fas fa-star mr-3 text-yellow-500"></i>
                            <span>Popular Destinations</span>
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <button type="button" class="destination-btn px-4 py-2 bg-gray-800 text-gray-300 rounded-lg text-sm 
                                                        hover:bg-yellow-500 hover:text-black transition-all duration-300 
                                                        flex items-center border border-gray-700 hover:border-yellow-600 shadow-md">
                                <i class="fas fa-building mr-2"></i> Downtown
                            </button>
                            <button type="button" class="destination-btn px-4 py-2 bg-gray-800 text-gray-300 rounded-lg text-sm 
                                                        hover:bg-yellow-500 hover:text-black transition-all duration-300 
                                                        flex items-center border border-gray-700 hover:border-yellow-600 shadow-md">
                                <i class="fas fa-plane mr-2"></i> Airport
                            </button>
                            <button type="button" class="destination-btn px-4 py-2 bg-gray-800 text-gray-300 rounded-lg text-sm 
                                                        hover:bg-yellow-500 hover:text-black transition-all duration-300 
                                                        flex items-center border border-gray-700 hover:border-yellow-600 shadow-md">
                                <i class="fas fa-shopping-bag mr-2"></i> Mall
                            </button>
                            <button type="button" class="destination-btn px-4 py-2 bg-gray-800 text-gray-300 rounded-lg text-sm 
                                                        hover:bg-yellow-500 hover:text-black transition-all duration-300 
                                                        flex items-center border border-gray-700 hover:border-yellow-600 shadow-md">
                                <i class="fas fa-hotel mr-2"></i> Hotel
                            </button>
                        </div>
                    </div>
                    
                    <!-- Additional options -->
                    <div class="mb-10">
                        <h3 class="text-lg font-medium text-white mb-4 flex items-center">
                            <i class="fas fa-cog mr-3 text-yellow-500"></i>
                            <span>Additional Options</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center bg-gray-800 rounded-lg p-3 border border-gray-700 transition-transform duration-300 hover:bg-gray-750">
                                <input id="wheelchair" type="checkbox" class="w-5 h-5 text-yellow-500 rounded border-gray-600 focus:ring-yellow-500 focus:ring-opacity-25">
                                <label for="wheelchair" class="ml-3 text-base text-gray-300">Wheelchair Accessible</label>
                            </div>
                            <div class="flex items-center bg-gray-800 rounded-lg p-3 border border-gray-700 transition-transform duration-300 hover:bg-gray-750">
                                <input id="child_seat" type="checkbox" class="w-5 h-5 text-yellow-500 rounded border-gray-600 focus:ring-yellow-500 focus:ring-opacity-25">
                                <label for="child_seat" class="ml-3 text-base text-gray-300">Child Seat</label>
                            </div>
                            <div class="flex items-center bg-gray-800 rounded-lg p-3 border border-gray-700 transition-transform duration-300 hover:bg-gray-750">
                                <input id="pet_friendly" type="checkbox" class="w-5 h-5 text-yellow-500 rounded border-gray-600 focus:ring-yellow-500 focus:ring-opacity-25">
                                <label for="pet_friendly" class="ml-3 text-base text-gray-300">Pet Friendly</label>
                            </div>
                            <div class="flex items-center bg-gray-800 rounded-lg p-3 border border-gray-700 transition-transform duration-300 hover:bg-gray-750">
                                <input id="extra_luggage" type="checkbox" class="w-5 h-5 text-yellow-500 rounded border-gray-600 focus:ring-yellow-500 focus:ring-opacity-25">
                                <label for="extra_luggage" class="ml-3 text-base text-gray-300">Extra Luggage Space</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit button -->
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-4 px-6 rounded-xl 
                                                shadow-lg transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl 
                                                flex items-center justify-center text-lg">
                        <i class="fas fa-taxi mr-3 text-xl"></i>
                        Book Your Ride Now
                    </button>
                </form>
                
                <!-- Features section -->
                <div class="bg-gray-800 px-8 py-10 border-t border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="flex flex-col items-center text-center transition-transform duration-300 transform hover:scale-105">
                            <div class="w-16 h-16 bg-yellow-500 bg-opacity-20 rounded-full flex items-center justify-center mb-4 shadow-lg">
                                <i class="fas fa-shield-alt text-yellow-500 text-2xl"></i>
                            </div>
                            <h3 class="font-medium text-white text-xl mb-2">Safe Rides</h3>
                            <p class="text-base text-gray-400">All our drivers are vetted and verified for your safety</p>
                        </div>
                        
                        <div class="flex flex-col items-center text-center transition-transform duration-300 transform hover:scale-105">
                            <div class="w-16 h-16 bg-yellow-500 bg-opacity-20 rounded-full flex items-center justify-center mb-4 shadow-lg">
                                <i class="fas fa-money-bill-wave text-yellow-500 text-2xl"></i>
                            </div>
                            <h3 class="font-medium text-white text-xl mb-2">Fair Pricing</h3>
                            <p class="text-base text-gray-400">Transparent pricing with no hidden charges</p>
                        </div>
                        
                        <div class="flex flex-col items-center text-center transition-transform duration-300 transform hover:scale-105">
                            <div class="w-16 h-16 bg-yellow-500 bg-opacity-20 rounded-full flex items-center justify-center mb-4 shadow-lg">
                                <i class="fas fa-headset text-yellow-500 text-2xl"></i>
                            </div>
                            <h3 class="font-medium text-white text-xl mb-2">24/7 Support</h3>
                            <p class="text-base text-gray-400">Our customer service team is always ready to help</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const DAYS_MAP = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    
    let currentDriverWorkDays = [];
    let currentDriverWorkStart = '';
    let currentDriverWorkEnd = '';
    
    document.getElementById('driver_id').addEventListener('change', function () {
        let selectedDriver = this.options[this.selectedIndex];       
        let carModel = selectedDriver.getAttribute('data-car_model');
        currentDriverWorkDays = JSON.parse(selectedDriver.getAttribute('data-work_days'));
        currentDriverWorkStart = selectedDriver.getAttribute('data-work_start');
        currentDriverWorkEnd = selectedDriver.getAttribute('data-work_end');              
        document.getElementById('car_model').textContent = carModel;
        document.getElementById('work_days').textContent = currentDriverWorkDays.length ? currentDriverWorkDays.join(', ') : 'N/A';
        document.getElementById('work_hours').textContent = currentDriverWorkStart + ' - ' + currentDriverWorkEnd;               
        
        // Animate the appearance of the driver profile
        const driverProfile = document.getElementById('driver-profile');
        driverProfile.classList.remove('hidden');
        driverProfile.classList.add('animate-fadeIn');
        
        document.getElementById('pickup_date').value = '';
        document.getElementById('pickup_time').value = '';
        
        setupDatePicker();
    });
    
    function setupDatePicker() {
        const dateInput = document.getElementById('pickup_date');
        const today = new Date();
        dateInput.min = today.toISOString().split('T')[0];
        dateInput.addEventListener('input', function() {
            validateSelectedDate(this.value);
        });
    }
    
    function validateSelectedDate(dateStr) {
        if (!dateStr || currentDriverWorkDays.length === 0) return;
        const selectedDate = new Date(dateStr);
        const dayOfWeek = selectedDate.getDay(); 
        const dayName = DAYS_MAP[dayOfWeek].toLowerCase();
        if (!currentDriverWorkDays.map(day => day.toLowerCase()).includes(dayName)) {
            Swal.fire({
                title: 'Unavailable Day',
                text: `The driver does not work on ${dayName}s. Please select a different day.`,
                icon: 'warning',
                confirmButtonColor: '#f59e0b',
                iconColor: '#f59e0b',
                background: '#1f2937',
                color: '#fff'
            });
            document.getElementById('pickup_date').value = '';
        }
    }
    
    document.getElementById('pickup_time').addEventListener('change', function() {
        if (!this.value || !currentDriverWorkStart || !currentDriverWorkEnd) return;
        
        const selectedTimeMinutes = convertTimeToMinutes(this.value);
        const workStartMinutes = convertTimeToMinutes(currentDriverWorkStart);
        const workEndMinutes = convertTimeToMinutes(currentDriverWorkEnd);
        
        let isTimeValid = false;
        if (workEndMinutes < workStartMinutes) {
            isTimeValid = selectedTimeMinutes >= workStartMinutes || selectedTimeMinutes <= workEndMinutes;
        } else {
            isTimeValid = selectedTimeMinutes >= workStartMinutes && selectedTimeMinutes <= workEndMinutes;
        }
        
        if (!isTimeValid) {
            Swal.fire({
                title: 'Outside Working Hours',
                text: `The driver only works between ${currentDriverWorkStart.split(':')[0]}:${currentDriverWorkStart.split(':')[1]} and ${currentDriverWorkEnd.split(':')[0]}:${currentDriverWorkEnd.split(':')[1]}. Please select a time within this range.`,
                icon: 'warning',
                confirmButtonColor: '#f59e0b',
                iconColor: '#f59e0b',
                background: '#1f2937',
                color: '#fff'
            });
            this.value = '';
        }
    });
    
    document.getElementById('booking-form').addEventListener('submit', function(e) {
        const dateInput = document.getElementById('pickup_date');
        const timeInput = document.getElementById('pickup_time');
        
        if (!dateInput.value || !timeInput.value) {
            e.preventDefault();
            Swal.fire({
                title: 'Incomplete Booking',
                text: 'Please select valid pickup date and time.',
                icon: 'error',
                confirmButtonColor: '#f59e0b',
                background: '#1f2937',
                color: '#fff'
            });
            return false;
        }
        
        const selectedDate = new Date(dateInput.value);
        const dayOfWeek = selectedDate.getDay();
        const dayName = DAYS_MAP[dayOfWeek].toLowerCase();
        
        if (!currentDriverWorkDays.map(day => day.toLowerCase()).includes(dayName)) {
            e.preventDefault();
            Swal.fire({
                title: 'Invalid Day Selected',
                text: `The driver does not work on ${dayName}s.`,
                icon: 'error',
                confirmButtonColor: '#f59e0b',
                background: '#1f2937',
                color: '#fff'
            });
            return false;
        }

        // Time validation
        if (timeInput.value) {
            const selectedTimeMinutes = convertTimeToMinutes(timeInput.value);
            const workStartMinutes = convertTimeToMinutes(currentDriverWorkStart);
            const workEndMinutes = convertTimeToMinutes(currentDriverWorkEnd);
            
            let isTimeValid = false;
            if (workEndMinutes < workStartMinutes) {
                // Overnight shift
                isTimeValid = selectedTimeMinutes >= workStartMinutes || selectedTimeMinutes <= workEndMinutes;
            } else {
                // Normal day shift
                isTimeValid = selectedTimeMinutes >= workStartMinutes && selectedTimeMinutes <= workEndMinutes;
            }
            
            if (!isTimeValid) {
                e.preventDefault();
                Swal.fire({
                    title: 'Invalid Time Selected',
                    text: `The driver only works between ${currentDriverWorkStart.split(':')[0]}:${currentDriverWorkStart.split(':')[1]} and ${currentDriverWorkEnd.split(':')[0]}:${currentDriverWorkEnd.split(':')[1]}.`,
                    icon: 'error',
                    confirmButtonColor: '#f59e0b',
                    background: '#1f2937',
                    color: '#fff'
                });
                return false;
            }
        }
    });

    document.querySelectorAll('.destination-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Add visual feedback when clicking a destination button
            this.classList.add('bg-yellow-500', 'text-black');
            
            // Remove highlight from other buttons
            document.querySelectorAll('.destination-btn').forEach(otherBtn => {
                if (otherBtn !== this) {
                    otherBtn.classList.remove('bg-yellow-500', 'text-black');
                }
            });
            
            const destinationText = this.textContent.trim();
            document.getElementById('destination').value = destinationText;
        });
    });

    function convertTimeToMinutes(timeStr) {
        const timeParts = timeStr.split(':');
        const hours = parseInt(timeParts[0], 10);
        const minutes = parseInt(timeParts[1], 10);
        return hours * 60 + minutes;
    }
    
    // Add this to your existing CSS or in a style tag
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            .animate-fadeIn {
                animation: fadeIn 0.5s ease-in-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            /* Improved focus styles for better accessibility */
            input:focus, select:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.3);
            }
            
            /* Custom style for the driver profile hover */
            .hover\:bg-gray-750:hover {
                background-color: #2d3748;
            }
        </style>
    `);
</script>
@endsection