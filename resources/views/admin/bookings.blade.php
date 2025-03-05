@extends('layouts.admin')

@section('header', 'Booking Management')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold">All Bookings</h2>
            <div class="flex space-x-2">
                <form action="{{ route('admin.bookings') }}" method="GET" class="flex">
                    <input type="text" name="search" placeholder="Search bookings..." class="border rounded-l px-4 py-2" value="{{ request('search') }}">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <select onchange="window.location = this.value;" class="border rounded px-4 py-2">
                    <option value="{{ route('admin.bookings') }}">All Status</option>
                    <option value="{{ route('admin.bookings', ['status' => 'pending']) }}" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="{{ route('admin.bookings', ['status' => 'confirmed']) }}" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="{{ route('admin.bookings', ['status' => 'cancelled']) }}" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Locations</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">#{{ $booking->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->client->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->client->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-tie text-gray-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->driver->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($booking->driver && $booking->driver->driverProfile)
                                            {{ $booking->driver->driverProfile->car_model }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">From: {{ $booking->pickup_place }}</div>
                            <div class="text-sm text-gray-500">To: {{ $booking->destination }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $booking->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="showBookingDetails({{ $booking->id }})" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($booking->status == 'pending')
                                <form method="POST" action="{{ route('bookings.update-status', $booking->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" name="status" value="confirmed" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('bookings.update-status', $booking->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" name="status" value="cancelled" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No bookings found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    </div>
    
    <!-- Booking Details Modal -->
    <div id="bookingDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-lg font-medium text-gray-900">Booking Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="bookingDetailsContent" class="mt-4">
                <!-- Content will be filled with JavaScript -->
                <p class="text-center text-gray-500">Loading...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showBookingDetails(bookingId) {
        // This is just a placeholder. In a real implementation, you would fetch the booking details via AJAX
        document.getElementById('bookingDetailsModal').classList.remove('hidden');
        
        // Example content - in a real implementation, you would populate this with data from the server
        document.getElementById('bookingDetailsContent').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Booking ID</p>
                    <p class="text-lg font-semibold">#${bookingId}</p>
                </div>
                <!-- More booking details would go here -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <p class="text-lg font-semibold">Pending</p>
                </div>
            </div>
            <div class="mt-5 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded mr-2">Close</button>
            </div>
        `;
    }
    
    function closeModal() {
        document.getElementById('bookingDetailsModal').classList.add('hidden');
    }
</script>
@endpush