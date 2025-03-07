<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\DriverProfile;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewBookingNotification;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingCancelledNotification;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $drivers = \App\Models\User::where('role', 'driver')->with('driverProfile')->get();
        $selectedDriverId = $request->query('driver_id');
        return view('booking.create', compact('drivers', 'selectedDriverId'));
    }
    
    public function store(Request $request)
    {
        try {
            // Extensive validation with detailed error messages
            $validated = $request->validate([
                'driver_id'    => 'required|exists:users,id',
                'pickup_date'  => 'required|date',
                'pickup_time'  => 'required',
                'pickup_place' => 'required|string|max:255',
                'destination'  => 'required|string|max:255',
            ], [
                // Custom error messages to be very specific
                'driver_id.required' => 'A driver must be selected',
                'driver_id.exists' => 'Selected driver does not exist',
                'pickup_date.required' => 'Pickup date is required',
                'pickup_time.required' => 'Pickup time is required',
                'pickup_place.required' => 'Pickup location is required',
                'destination.required' => 'Destination is required'
            ]);
    
            $pickupDateTime = $request->pickup_date . ' ' . $request->pickup_time . ':00';
            
            $client_id = Auth::id();
    
            // Verify driver exists before creating booking
            $driver = \App\Models\User::where('id', $request->driver_id)->first();
            
            if (!$driver) {
                return back()->with('error', 'Selected driver not found')->withInput();
            }
    
            $booking = Booking::create([
                'client_id'    => $client_id,
                'driver_id'    => $request->driver_id,
                'pickup_time'  => $pickupDateTime,
                'pickup_place' => $request->pickup_place,
                'destination'  => $request->destination,
                'status'       => 'pending',
            ]);
            
            // Send notification to the driver
            $driver->notify(new NewBookingNotification($booking));
    
            return redirect()->route('bookings.index')->with('success', 'Booking request sent successfully!');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            error_log('Booking Validation Failed: ' . json_encode([
                'errors' => $e->errors(),
                'input' => $request->all()
            ]));
            return back()->withErrors($e->errors())->withInput();
    
        } catch (\Exception $e) {
            error_log('Booking Creation Error: ' . json_encode([
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]));
            
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
    public function index()
    {
        $bookings = Booking::where('client_id', Auth::id())
                          ->with('driver')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10); // Add pagination with 10 items per page
                          
        return view('booking.index', compact('bookings'));
    }

    public function updateStatus(Request $request, $bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            // Verify the user has permission to update this booking
            if (Auth::user()->role == 'driver' && $booking->driver_id != Auth::id()) {
                return back()->with('error', 'You are not authorized to update this booking.');
            }
            
            if (Auth::user()->role == 'client' && $booking->client_id != Auth::id()) {
                return back()->with('error', 'You are not authorized to update this booking.');
            }
            
            // Additional validation for status change
            if (Auth::user()->role == 'driver') {
                $request->validate([
                    'status' => 'required|in:confirmed,cancelled'
                ]);
            }
            
            if (Auth::user()->role == 'client') {
                // Clients can only cancel pending bookings
                if ($booking->status != 'pending') {
                    return back()->with('error', 'You can only cancel pending bookings.');
                }
                $request->merge(['status' => 'cancelled']);
            }
            
            // Store the previous status to check if it changed
            $previousStatus = $booking->status;
            
            $booking->status = $request->status;
            $booking->save();
            
            // Send notifications based on the status change
            if ($previousStatus != $booking->status) {
                if ($booking->status == 'confirmed') {
                    // Notify the client that the booking was confirmed
                    $booking->client->notify(new BookingConfirmedNotification($booking));
                } elseif ($booking->status == 'cancelled') {
                    // Determine who cancelled the booking
                    $cancelledBy = Auth::user()->role;
                    
                    // If driver cancels, notify the client
                    if ($cancelledBy == 'driver') {
                        $booking->client->notify(new BookingCancelledNotification($booking, 'driver'));
                    }
                    
                    // If client cancels, notify the driver
                    if ($cancelledBy == 'client') {
                        $booking->driver->notify(new BookingCancelledNotification($booking, 'client'));
                    }
                }
            }
            
            return back()->with('success', 'Booking status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}