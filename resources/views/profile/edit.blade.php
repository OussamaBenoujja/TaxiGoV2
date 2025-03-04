@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-32 flex items-center justify-center md:justify-start px-8">
                <div class="relative mt-16">
                    <!-- Profile Picture/Placeholder -->
                    <div class="w-24 h-24 rounded-full bg-white border-4 border-white shadow-lg overflow-hidden">
                        @if(Auth::user()->role == 'driver' && Auth::user()->driverProfile && Auth::user()->driverProfile->profile_picture)
                            <img src="{{ Storage::url(Auth::user()->driverProfile->profile_picture) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="px-8 pt-16 pb-8">
                <h1 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h1>
                <p class="text-gray-600">{{ Auth::user()->email }}</p>
                <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        {{ Auth::user()->role == 'driver' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst(Auth::user()->role) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="flex border-b">
                <button id="personal-tab" onclick="showTab('personal')" class="tab-btn active px-6 py-3 font-medium text-sm focus:outline-none">
                    Personal Information
                </button>
                @if(Auth::user()->role == 'client')
                    <button id="bookings-tab" onclick="showTab('bookings')" class="tab-btn px-6 py-3 font-medium text-sm focus:outline-none">
                        My Bookings
                    </button>
                @endif
                @if(Auth::user()->role == 'driver')
                    <button id="driver-tab" onclick="showTab('driver')" class="tab-btn px-6 py-3 font-medium text-sm focus:outline-none">
                        Driver Details
                    </button>
                @endif
                <button id="settings-tab" onclick="showTab('settings')" class="tab-btn px-6 py-3 font-medium text-sm focus:outline-none">
                    Account Settings
                </button>
            </div>

            <!-- Personal Info Tab -->
            <div id="personal" class="tab-content p-6 block">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Contact Information</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Full Name</label>
                                <p class="mt-1 text-gray-800">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email Address</label>
                                <p class="mt-1 text-gray-800">{{ Auth::user()->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Account Type</label>
                                <p class="mt-1 text-gray-800">{{ ucfirst(Auth::user()->role) }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Account Summary</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Member Since</div>
                                    <div class="text-sm text-gray-500">{{ Auth::user()->created_at->format('F Y') }}</div>
                                </div>
                            </div>
                            @if(Auth::user()->role == 'client')
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Total Bookings</div>
                                        <div class="text-sm text-gray-500">{{ count($bookings) }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings Tab (For Clients) -->
            @if(Auth::user()->role == 'client')
    <div id="bookings" class="tab-content p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Your Taxi Bookings</h3>
        
        @if(count($bookings) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <tr>
                                <!-- Previous columns remain the same -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($booking->status == 'pending')
                                        <form method="POST" action="{{ route('bookings.update-status', $booking->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" name="status" value="cancelled" 
                                                class="text-red-600 hover:text-red-900 text-sm mr-2"
                                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <p class="text-gray-600">You don't have any bookings yet.</p>
                            <a href="{{ route('booking.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Book a Taxi Now
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Driver Details Tab (For Drivers) -->
            @if(Auth::user()->role == 'driver' && Auth::user()->driverProfile)
                <div id="driver" class="tab-content p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Driver Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5 mb-6">
                                <h4 class="font-medium text-yellow-800 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h3a1 1 0 00.8-.4l3-4a1 1 0 00.2-.6V8a1 1 0 00-1-1h-3.05A2.5 2.5 0 0010 5.05V5a1 1 0 00-1-1H3z" />
                                    </svg>
                                    Vehicle Information
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="grid grid-cols-3">
                                        <div class="text-gray-500">Car Model:</div>
                                        <div class="col-span-2 font-medium text-gray-900">{{ Auth::user()->driverProfile->car_model }}</div>
                                    </div>
                                    <div class="grid grid-cols-3">
                                        <div class="text-gray-500">City:</div>
                                        <div class="col-span-2 font-medium text-gray-900">{{ Auth::user()->driverProfile->city }}</div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-700 mb-2">Driver Description</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-700">{{ Auth::user()->driverProfile->description ?: 'No description provided.' }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                                <h4 class="font-medium text-blue-800 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    Working Schedule
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-gray-500 text-xs uppercase mb-1">Working Days</div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                    {{ in_array($day, Auth::user()->driverProfile->work_days) 
                                                        ? 'bg-blue-100 text-blue-800' 
                                                        : 'bg-gray-100 text-gray-400' }}">
                                                    {{ $day }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <div class="text-gray-500 text-xs uppercase mb-1">Working Hours</div>
                                            <div class="font-medium">
                                                <span class="inline-flex items-center bg-green-100 text-green-800 px-2.5 py-0.5 rounded-md text-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse(Auth::user()->driverProfile->work_start)->format('h:i A') }}
                                                </span>
                                                <span class="mx-1">to</span>
                                                <span class="inline-flex items-center bg-red-100 text-red-800 px-2.5 py-0.5 rounded-md text-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse(Auth::user()->driverProfile->work_end)->format('h:i A') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 mt-6">Your Upcoming Bookings</h3>
        
        @if(Auth::user()->bookings && Auth::user()->bookings->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(Auth::user()->bookings as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->client->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->pickup_place }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->destination }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($booking->status == 'pending')
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('bookings.update-status', $booking->id) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" name="status" value="confirmed" 
                                                    class="text-green-600 hover:text-green-900 text-sm mr-2"
                                                    onclick="return confirm('Are you sure you want to confirm this booking?')">
                                                    Confirm
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('bookings.update-status', $booking->id) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" name="status" value="cancelled" 
                                                    class="text-red-600 hover:text-red-900 text-sm"
                                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-gray-50 p-6 rounded-lg text-center">
                <p class="text-gray-600">You don't have any bookings yet.</p>
            </div>
        @endif
    </div>
@endif

            <!-- Settings Tab -->
            <div id="settings" class="tab-content p-6 hidden">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Account Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                            @csrf
                            @method('patch')
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div>
                        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                            @csrf
                            @method('put')
                            
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                <input type="password" name="current_password" id="current_password" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                <input type="password" name="password" id="password" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Show the selected tab
    document.getElementById(tabName).classList.remove('hidden');
    
    // Update active tab button
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-500');
    });
    
    document.getElementById(tabName + '-tab').classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-500');
}

// Initialize tabs
document.addEventListener('DOMContentLoaded', function() {
    showTab('personal');
});
</script>

<style>
    /* Add some basic styles for the active tab */
    .tab-btn.active {
        color: #2563eb;
        border-bottom: 2px solid #3b82f6;
    }
</style>
@endsection