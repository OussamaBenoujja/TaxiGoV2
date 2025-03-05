<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Booking;

class ProfileController extends Controller
{
    
    public function edit(Request $request): View
    {
        $user = \App\Models\User::find(Auth::id());
        
        // Debug logging
        error_log('User Role: ' . $user->role);
        error_log('User ID: ' . $user->id);
    
        $bookings = [];
    
        if ($user->role == 'client') {
            $bookings = Booking::where('client_id', $user->id)
                ->with('driver')
                ->orderBy('pickup_time', 'desc')
                ->get();
            
            error_log('Client Bookings Count: ' . $bookings->count());
        } elseif ($user->role == 'driver') {
            $bookings = Booking::where('driver_id', $user->id)
                ->with('client')
                ->orderBy('pickup_time', 'desc')
                ->get();
            
            error_log('Driver Bookings Count: ' . $bookings->count());
            error_log('Driver Bookings: ' . json_encode($bookings->toArray()));
        }
    
        return view('profile.edit', compact('bookings'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function showDriverRegistration()
{
    if (Auth::user()->role === 'driver') {
        return redirect()->route('dashboard')->with('error', 'You are already registered as a driver.');
    }
    
    return view('profile.become-driver');
}

public function registerAsDriver(Request $request)
{
    $user = Auth::user();
    
    
    if ($user->role === 'driver') {
        return redirect()->route('dashboard')->with('error', 'You are already registered as a driver.');
    }
    
    
    $request->validate([
        'car_model' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'description' => 'nullable|string',
        'work_days' => 'required|array',
        'work_start' => 'required',
        'work_end' => 'required',
        'profile_picture' => 'nullable|image|max:2048',
    ]);
    
   
    $user->role = 'driver';
    $user->save();
    
    
    $data = [
        'user_id' => $user->id,
        'description' => $request->description,
        'car_model' => $request->car_model,
        'city' => $request->city,
        'work_days' => $request->work_days,
        'work_start' => $request->work_start,
        'work_end' => $request->work_end,
    ];
    
    if ($request->hasFile('profile_picture')) {
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        $data['profile_picture'] = $path;
    }
    
    \App\Models\DriverProfile::create($data);
    
    return redirect()->route('dashboard')->with('success', 'You are now registered as a driver!');
}
}
