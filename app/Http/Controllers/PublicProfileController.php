<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicProfileController extends Controller
{
    /**
     * Display the public profile of a user.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function show($userId)
    {
        $user = User::with(['receivedReviews' => function ($query) {
            $query->with('reviewer')->latest()->take(5);
        }])->findOrFail($userId);
        
        // Check if the authenticated user has any confirmed bookings with this user
        $canReview = false;
        $reviewExists = false;
        $review = null;
        $sharedBooking = null;
        
        if (Auth::check() && Auth::id() != $userId) {
            // Find a confirmed booking between the authenticated user and the profile user
            if (Auth::user()->role == 'client') {
                // Client viewing a driver's profile
                $sharedBooking = Booking::where('client_id', Auth::id())
                    ->where('driver_id', $userId)
                    ->where('status', 'confirmed')
                    ->latest()
                    ->first();
            } else {
                // Driver viewing a client's profile
                $sharedBooking = Booking::where('driver_id', Auth::id())
                    ->where('client_id', $userId)
                    ->where('status', 'confirmed')
                    ->latest()
                    ->first();
            }
            
            if ($sharedBooking) {
                $canReview = true;
                
                // Check if a review already exists
                $review = $sharedBooking->reviews()
                    ->where('reviewer_id', Auth::id())
                    ->first();
                    
                if ($review) {
                    $reviewExists = true;
                }
            }
        }
        
        return view('profiles.public', compact('user', 'canReview', 'reviewExists', 'review', 'sharedBooking'));
    }
}