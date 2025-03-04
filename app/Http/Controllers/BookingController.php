<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\DriverProfile;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create()
    {
        $drivers = \App\Models\User::where('role', 'driver')->with('driverProfile')->get();
        return view('booking.create', compact('drivers'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'driver_id'    => 'required|exists:users,id',
            'pickup_date'  => 'required|date',
            'pickup_time'  => 'required',
            'pickup_place' => 'required|string|max:255',
            'destination'  => 'required|string|max:255',
        ]);

       
        $pickupDateTime = $request->pickup_date . ' ' . $request->pickup_time . ':00';
        
        $client_id = Auth::id();
        $driver = \App\Models\User::findOrFail($request->driver_id);
        $profile = $driver->driverProfile;
        
        
        $dayOfWeek = date('l', strtotime($pickupDateTime));
        if (!in_array($dayOfWeek, $profile->work_days)) {
            return back()->with('error', "Driver is not available on $dayOfWeek.");
        }
        
        
        $pickupTimeOnly = date('H:i', strtotime($pickupDateTime));
        if ($pickupTimeOnly < $profile->work_start || $pickupTimeOnly >= $profile->work_end) {
            return back()->with('error', "Driver is not available at this time.");
        }
        
        
        $conflictingBooking = Booking::where('driver_id', $driver->id)
            ->whereBetween('pickup_time', [
                date('Y-m-d H:i:s', strtotime($pickupDateTime)),
                date('Y-m-d H:i:s', strtotime($pickupDateTime . ' +1 hour'))
            ])
            ->first();
        
        if ($conflictingBooking) {
            return back()->with('error', 'Driver is already booked for this time slot.');
        }
        
       
        Booking::create([
            'client_id'    => $client_id,
            'driver_id'    => $request->driver_id,
            'pickup_time'  => $pickupDateTime,
            'pickup_place' => $request->pickup_place,
            'destination'  => $request->destination,
            'status'       => 'pending',
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'Booking request sent.');
    }
    
    public function index()
    {
        $bookings = Booking::where('client_id', Auth::id())->with('driver')->get();
        return view('booking.index', compact('bookings'));
    }
}