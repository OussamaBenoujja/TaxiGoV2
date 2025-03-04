@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Profile</h1>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold">Personal Info</h2>
        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>

        @if(Auth::user()->role == 'client')
            <h2 class="text-xl font-semibold mt-4">Your Bookings</h2>
            <ul>
                @foreach($bookings as $booking)
                    <li class="border p-2 my-2">
                        <strong>Driver:</strong> {{ $booking->driver->name }} <br>
                        <strong>Pickup Time:</strong> {{ $booking->pickup_time }} <br>
                        <strong>Status:</strong> {{ ucfirst($booking->status) }}
                    </li>
                @endforeach
            </ul>
        @endif

        @if(Auth::user()->role == 'driver' && Auth::user()->driverProfile)
            <h2 class="text-xl font-semibold mt-4">Driver Profile</h2>
            <p><strong>Car Model:</strong> {{ Auth::user()->driverProfile->car_model }}</p>
            <p><strong>City:</strong> {{ Auth::user()->driverProfile->city }}</p>
            <p><strong>Work Days:</strong> {{ implode(', ', Auth::user()->driverProfile->work_days) }}</p>
            <p><strong>Work Hours:</strong> {{ Auth::user()->driverProfile->work_start }} - {{ Auth::user()->driverProfile->work_end }}</p>
        @endif
    </div>
</div>
@endsection
