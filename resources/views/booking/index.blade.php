@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Your Bookings</h1>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        @foreach($bookings as $booking)
            <div class="border p-4 my-2">
                <p><strong>Driver:</strong> {{ $booking->driver->name }}</p>
                <p><strong>Pickup Time:</strong> {{ $booking->pickup_time }}</p>
                <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>
                @if($booking->status == 'confirmed')
                    <a href="{{ route('bookings.chat', $booking->id) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                        <i class="fas fa-comments"></i> Chat
                    </a>
                @endif
            </div>

        @endforeach
    </div>
</div>
@endsection
