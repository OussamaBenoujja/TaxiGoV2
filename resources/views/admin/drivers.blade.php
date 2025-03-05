@extends('layouts.admin')

@section('header', 'Driver Management')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold">All Drivers</h2>
            <div class="flex space-x-2">
                <form action="{{ route('admin.drivers') }}" method="GET" class="flex">
                    <input type="text" name="search" placeholder="Search drivers..." class="border rounded-l px-4 py-2" value="{{ request('search') }}">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <select onchange="window.location = this.value;" class="border rounded px-4 py-2">
                    <option value="{{ route('admin.drivers') }}">All Cities</option>
                    @php
                        // In a real implementation, you would fetch the list of cities from the database
                        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];
                    @endphp
                    @foreach($cities as $city)
                        <option value="{{ route('admin.drivers', ['city' => $city]) }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center mb-2">
                    <div class="p-2 bg-blue-100 rounded-full mr-3">
                        <i class="fas fa-user-tie text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-blue-800">Total Drivers</h3>
                </div>
                <p class="text-3xl font-bold text-blue-900">{{ count($drivers) }}</p>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="flex items-center mb-2">
                    <div class="p-2 bg-green-100 rounded-full mr-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-green-800">Active Drivers</h3>
                </div>
                <p class="text-3xl font-bold text-green-900">{{ $drivers->where('availability', '>', 0)->count() }}</p>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <div class="flex items-center mb-2">
                    <div class="p-2 bg-yellow-100 rounded-full mr-3">
                        <i class="fas fa-car text-yellow-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-yellow-800">Total Rides</h3>
                </div>
                <p class="text-3xl font-bold text-yellow-900">
                    {{ $drivers->sum('booking_count') }}
                </p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Availability</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($drivers as $driver)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                                    @if($driver->driverProfile && $driver->driverProfile->profile_picture)
                                        <img src="{{ Storage::url($driver->driverProfile->profile_picture) }}" alt="{{ $driver->name }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fas fa-user-tie text-gray-500"></i>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $driver->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $driver->driverProfile->car_model ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $driver->driverProfile->city ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $driver->availability ?? 0 }}%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-500">{{ round($driver->availability ?? 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $driver->booking_count ?? 0 }} rides
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $completionRate = $driver->booking_count > 0 
                                    ? ($driver->completed_count / $driver->booking_count) * 100 
                                    : 0;
                            @endphp
                            <div class="text-sm text-gray-900 font-medium">
                                {{ round($completionRate) }}%
                            </div>
                            <div class="flex items-center mt-1">
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $completionRate }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="showDriverDetails({{ $driver->id }})" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $drivers->links() }}
        </div>
    </div>
    
    <!-- Driver Details Modal -->
    <div id="driverDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-lg font-medium text-gray-900">Driver Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="driverDetailsContent" class="mt-4">
                <!-- Content will be filled with JavaScript -->
                <p class="text-center text-gray-500">Loading...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showDriverDetails(driverId) {
        // This is just a placeholder. In a real implementation, you would fetch the driver details via AJAX
        document.getElementById('driverDetailsModal').classList.remove('hidden');
        
        // Example content - in a real implementation, you would populate this with data from the server
        document.getElementById('driverDetailsContent').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <div class="bg-gray-100 p-4 rounded-lg flex flex-col items-center">
                        <div class="w-32 h-32 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden mb-4">
                            <i class="fas fa-user-tie text-gray-500 text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold">Driver #${driverId}</h3>
                        <p class="text-gray-500">driver${driverId}@example.com</p>
                        <div class="flex mt-4 space-x-2">
                            <button class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                                <i class="fas fa-envelope mr-1"></i> Contact
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-2">Vehicle Information</h4>
                            <p class="text-gray-600">Car Model: Toyota Camry</p>
                            <p class="text-gray-600">Year: 2020</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-2">Location</h4>
                            <p class="text-gray-600">City: New York</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-2">Working Hours</h4>
                            <p class="text-gray-600">Days: Mon, Tue, Wed, Thu, Fri</p>
                            <p class="text-gray-600">Hours: 9:00 AM - 5:00 PM</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-2">Performance</h4>
                            <p class="text-gray-600">Total Rides: 45</p>
                            <p class="text-gray-600">Completion Rate: 95%</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-2">Description</h4>
                        <p class="text-gray-600">Experienced driver with 5 years of professional driving experience. Knowledgeable about city routes and landmarks.</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <h4 class="font-medium text-gray-700 mb-2">Recent Bookings</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#123</td>
                                <td class="px-6 py-4 whitespace-nowrap">John Doe</td>
                                <td class="px-6 py-4 whitespace-nowrap">Apr 15, 2025</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Confirmed
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#122</td>
                                <td class="px-6 py-4 whitespace-nowrap">Jane Smith</td>
                                <td class="px-6 py-4 whitespace-nowrap">Apr 14, 2025</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Confirmed
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-5 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded mr-2">Close</button>
            </div>
        `;
    }
    
    function closeModal() {
        document.getElementById('driverDetailsModal').classList.add('hidden');
    }
</script>
@endpush