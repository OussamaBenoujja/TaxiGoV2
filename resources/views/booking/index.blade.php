@extends('layouts.theme')

@section('content')
<div class="bg-gray-950 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Your Bookings</h1>
                <p class="mt-2 text-gray-400">Manage all your ride bookings in one place</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <a href="{{ route('booking.create') }}" class="btn-primary flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Book New Ride
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-white">Recent Bookings</h2>
            </div>
            
            <div class="divide-y divide-gray-800">
                @forelse($bookings as $booking)
                    <div class="p-6 hover:bg-gray-800 transition duration-150">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex items-start">
                                <div class="h-10 w-10 rounded-full bg-yellow-500 flex items-center justify-center text-black mr-4">
                                    <i class="fas fa-user-alt"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $booking->driver->name }}</p>
                                    <p class="text-sm text-gray-400">{{ $booking->driver->driverProfile->car_model ?? 'No car info' }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-4 md:mt-0 md:ml-6 flex-grow">
                                <div class="flex items-center text-gray-300">
                                    <i class="fas fa-map-marker-alt text-yellow-500 mr-2"></i>
                                    <span>{{ $booking->pickup_place }} → {{ $booking->destination }}</span>
                                </div>
                                <div class="flex items-center text-gray-400 text-sm mt-1">
                                    <i class="fas fa-calendar-alt text-yellow-500 mr-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($booking->pickup_time)->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-4 md:mt-0 text-right">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold 
                                    @if($booking->status == 'pending') bg-yellow-500 bg-opacity-20 text-yellow-400
                                    @elseif($booking->status == 'confirmed') bg-green-500 bg-opacity-20 text-green-400
                                    @elseif($booking->status == 'cancelled') bg-red-500 bg-opacity-20 text-red-400
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                                
                                <div class="mt-2 flex items-center justify-end space-x-2">
                                    @if($booking->status == 'confirmed')
                                        <a href="{{ route('bookings.chat', $booking->id) }}" class="flex items-center text-sm font-medium text-yellow-500 hover:text-yellow-400">
                                            <i class="fas fa-comments mr-1"></i>
                                            Chat
                                        </a>
                                    @endif
                                    
                                    @if($booking->status == 'pending')
                                        <form method="POST" action="{{ route('bookings.update-status', $booking->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" name="status" value="cancelled" 
                                                class="flex items-center text-sm font-medium text-red-500 hover:text-red-400"
                                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-800 mb-4">
                            <i class="fas fa-taxi text-yellow-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-white">No bookings yet</h3>
                        <p class="text-gray-400 mt-2">Your booking history will appear here once you've booked a ride.</p>
                        <a href="{{ route('booking.create') }}" class="btn-primary inline-flex items-center mt-4">
                            <i class="fas fa-plus mr-2"></i>
                            Book Your First Ride
                        </a>
                    </div>
                @endforelse
                
                @if($bookings->count() > 0 && $bookings->hasPages())
                    <div class="p-4 border-t border-gray-800">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection