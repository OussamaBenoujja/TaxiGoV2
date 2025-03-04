@extends('layouts.app')

@section('content')
<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with icon -->
        <div class="flex items-center justify-center mb-8">
            <div class="bg-white p-4 rounded-full shadow-lg mr-4">
                <i class="fas fa-taxi text-yellow-500 text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-800">Book Your Taxi</h1>
        </div>
        
        <!-- Main content card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-4xl mx-auto">
            <!-- Decorative header image -->
            <div class="h-32 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 relative">
                <div class="absolute bottom-0 left-0 right-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-16">
                        <path fill="#ffffff" fill-opacity="1" d="M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,85.3C672,75,768,85,864,112C960,139,1056,181,1152,181.3C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                    </svg>
                </div>
                <div class="absolute top-4 left-6 text-white">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span class="text-sm font-medium">Safe & Quick Rides</span>
                    </div>
                </div>
            </div>
            
            <!-- Booking form -->
            <form method="POST" action="{{ route('booking.store') }}" class="p-6 sm:p-8" id="booking-form">
                @csrf
                
                <!-- Progress steps -->
                <div class="flex justify-between items-center mb-8 px-2">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <span class="text-xs font-medium mt-2">Select Driver</span>
                    </div>
                    <div class="h-1 flex-1 bg-gray-200 mx-2 rounded-full relative">
                        <div class="h-1 bg-yellow-500 rounded-full absolute top-0 left-0" style="width: 33%;"></div>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <span class="text-xs font-medium mt-2">Choose Time</span>
                    </div>
                    <div class="h-1 flex-1 bg-gray-200 mx-2 rounded-full">
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span class="text-xs font-medium mt-2">Confirm</span>
                    </div>
                </div>
                
                <!-- Driver selection -->
                <div class="mb-6">
                    <label for="driver_id" class="block font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-user-tie mr-2 text-yellow-500"></i>
                        <span>Select A Driver:</span>
                    </label>
                    <div class="relative">
                        <select name="driver_id" id="driver_id" required class="w-full p-3 border border-gray-300 rounded-lg pl-10 pr-10 bg-gray-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 appearance-none">
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
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Driver profile card -->
                <div id="driver-profile" class="mb-8 hidden">
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="bg-yellow-100 p-3 rounded-full mr-4">
                                <i class="fas fa-id-card text-yellow-600 text-xl"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">Driver Profile</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                <div class="mr-3 text-yellow-500">
                                    <i class="fas fa-car"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Car Model</p>
                                    <p class="font-medium" id="car_model">N/A</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                <div class="mr-3 text-yellow-500">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Work Days</p>
                                    <p class="font-medium" id="work_days">N/A</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                <div class="mr-3 text-yellow-500">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Working Hours</p>
                                    <p class="font-medium" id="work_hours">N/A</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Date and time selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="pickup_date" class="block font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-yellow-500"></i>
                            <span>Pickup Date:</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="pickup_date" id="pickup_date" required class="w-full p-3 border border-gray-300 rounded-lg pl-10 bg-gray-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="pickup_time" class="block font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-clock mr-2 text-yellow-500"></i>
                            <span>Pickup Time:</span>
                        </label>
                        <div class="relative">
                            <input type="time" name="pickup_time" id="pickup_time" required class="w-full p-3 border border-gray-300 rounded-lg pl-10 bg-gray-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Destination -->
                <div class="mb-8">
                    <label for="destination" class="block font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-yellow-500"></i>
                        <span>Destination:</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="destination" id="destination" required placeholder="Enter your destination" class="w-full p-3 border border-gray-300 rounded-lg pl-10 bg-gray-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-map-pin text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                <label for="pickup_place" class="block font-medium text-gray-700">
                    <i class="fas fa-map-marker-alt mr-2 text-yellow-500"></i>
                    <span>Pickup Location:</span>
                </label>
                <div class="relative">
                <input type="text" name="pickup_place" id="pickup_place" required placeholder="Enter your PickUp Spot" class="w-full p-3 border border-gray-300 rounded-lg pl-10 bg-gray-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-map-pin text-gray-400"></i>
                        </div>
                    </div>
            </div>

            <br>
                
                <!-- Popular destinations -->
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-star mr-2 text-yellow-500"></i>
                        <span>Popular Destinations:</span>
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="destination-btn px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-yellow-100 hover:text-yellow-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-building mr-1"></i> Downtown
                        </button>
                        <button type="button" class="destination-btn px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-yellow-100 hover:text-yellow-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-plane mr-1"></i> Airport
                        </button>
                        <button type="button" class="destination-btn px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-yellow-100 hover:text-yellow-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-shopping-bag mr-1"></i> Mall
                        </button>
                        <button type="button" class="destination-btn px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-yellow-100 hover:text-yellow-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-hotel mr-1"></i> Hotel
                        </button>
                    </div>
                </div>
                
                <!-- Additional options -->
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-cog mr-2 text-yellow-500"></i>
                        <span>Additional Options:</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex items-center">
                            <input id="wheelchair" type="checkbox" class="w-4 h-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-500">
                            <label for="wheelchair" class="ml-2 text-sm text-gray-700">Wheelchair Accessible</label>
                        </div>
                        <div class="flex items-center">
                            <input id="child_seat" type="checkbox" class="w-4 h-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-500">
                            <label for="child_seat" class="ml-2 text-sm text-gray-700">Child Seat</label>
                        </div>
                        <div class="flex items-center">
                            <input id="pet_friendly" type="checkbox" class="w-4 h-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-500">
                            <label for="pet_friendly" class="ml-2 text-sm text-gray-700">Pet Friendly</label>
                        </div>
                        <div class="flex items-center">
                            <input id="extra_luggage" type="checkbox" class="w-4 h-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-500">
                            <label for="extra_luggage" class="ml-2 text-sm text-gray-700">Extra Luggage Space</label>
                        </div>
                    </div>
                </div>
                
                <!-- Submit button -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-taxi mr-2"></i>
                        Book Now
                    </button>
                </div>
            </form>
            
            <!-- Features section -->
            <div class="bg-gray-50 px-6 py-8 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-shield-alt text-yellow-600"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-1">Safe Rides</h3>
                        <p class="text-sm text-gray-600">All our drivers are vetted and verified for your safety</p>
                    </div>
                    
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-money-bill-wave text-yellow-600"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-1">Fair Pricing</h3>
                        <p class="text-sm text-gray-600">Transparent pricing with no hidden charges</p>
                    </div>
                    
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-headset text-yellow-600"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-1">24/7 Support</h3>
                        <p class="text-sm text-gray-600">Our customer service team is always ready to help</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
        document.getElementById('driver-profile').classList.remove('hidden');
        document.getElementById('pickup_date').value = '';
        document.getElementById('pickup_time').value = '';
        
        // Update step indicator
        document.querySelectorAll('.progress-steps .step')[0].classList.add('active');
        
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
                iconColor: '#f59e0b'
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
            iconColor: '#f59e0b'
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
            confirmButtonColor: '#f59e0b'
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
            confirmButtonColor: '#f59e0b'
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
                confirmButtonColor: '#f59e0b'
            });
            return false;
        }
    }
});
    
    document.querySelectorAll('.destination-btn').forEach(btn => {
        btn.addEventListener('click', function() {
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

</script>
@endsection