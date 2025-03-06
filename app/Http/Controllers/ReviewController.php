<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Show the form for creating a new review.
     */
    public function create($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        // Check if the authenticated user is part of this booking
        if (Auth::id() != $booking->client_id && Auth::id() != $booking->driver_id) {
            return redirect()->route('bookings.index')->with('error', 'You are not authorized to review this booking.');
        }
        
        // Check if booking is confirmed (can be reviewed)
        if (!$booking->canBeReviewed()) {
            return redirect()->route('bookings.index')->with('error', 'Only confirmed bookings can be reviewed.');
        }
        
        // Check if user has already submitted a review
        if ($booking->hasBeenReviewedBy(Auth::id())) {
            return redirect()->route('reviews.edit', $booking->getReviewBy(Auth::id())->id)
                ->with('info', 'You have already reviewed this booking. You can edit your review below.');
        }
        
        // Determine who to review (if client is logged in, review driver; if driver is logged in, review client)
        $reviewee = (Auth::id() == $booking->client_id) ? $booking->driver : $booking->client;
        
        return view('reviews.create', compact('booking', 'reviewee'));
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|numeric|min:0.5|max:5|multiple_of:0.5',
            'comment' => 'required|string|min:5|max:500',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $booking = Booking::findOrFail($request->booking_id);
        
        // Verify user is part of the booking
        if (Auth::id() != $booking->client_id && Auth::id() != $booking->driver_id) {
            return redirect()->route('bookings.index')->with('error', 'You are not authorized to review this booking.');
        }
        
        // Verify booking is confirmed
        if (!$booking->canBeReviewed()) {
            return redirect()->route('bookings.index')->with('error', 'Only confirmed bookings can be reviewed.');
        }
        
        // Verify user hasn't already reviewed this booking
        if ($booking->hasBeenReviewedBy(Auth::id())) {
            return redirect()->route('bookings.index')->with('error', 'You have already reviewed this booking.');
        }
        
        // Verify the reviewee is part of the booking
        if ($request->reviewee_id != $booking->client_id && $request->reviewee_id != $booking->driver_id) {
            return redirect()->route('bookings.index')->with('error', 'Invalid reviewee.');
        }
        
        // Create the review
        $review = Review::create([
            'booking_id' => $request->booking_id,
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $request->reviewee_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'Your review has been submitted successfully.');
    }

    /**
     * Show the form for editing a review.
     */
    public function edit($id)
    {
        $review = Review::findOrFail($id);
        
        // Check if the authenticated user is the reviewer
        if (Auth::id() != $review->reviewer_id) {
            return redirect()->route('bookings.index')->with('error', 'You are not authorized to edit this review.');
        }
        
        $booking = $review->booking;
        $reviewee = $review->reviewee;
        
        return view('reviews.edit', compact('review', 'booking', 'reviewee'));
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        
        // Check if the authenticated user is the reviewer
        if (Auth::id() != $review->reviewer_id) {
            return redirect()->route('bookings.index')->with('error', 'You are not authorized to update this review.');
        }
        
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|min:0.5|max:5|multiple_of:0.5',
            'comment' => 'required|string|min:5|max:500',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'Your review has been updated successfully.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        
        // Check if the authenticated user is the reviewer
        if (Auth::id() != $review->reviewer_id) {
            return redirect()->route('bookings.index')->with('error', 'You are not authorized to delete this review.');
        }
        
        $review->delete();
        
        return redirect()->route('bookings.index')->with('success', 'Your review has been deleted successfully.');
    }
    
    /**
     * Show reviews for a specific user.
     */
    public function showUserReviews($userId)
    {
        $user = User::findOrFail($userId);
        $reviews = $user->receivedReviews()->with(['reviewer', 'booking'])->latest()->paginate(10);
        
        return view('reviews.user', compact('user', 'reviews'));
    }
}