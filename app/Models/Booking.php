<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'client_id', 
        'driver_id', 
        'pickup_time', 
        'pickup_place',
        'destination',
        'status',
        'payment_status',
        'payment_intent_id',
        'amount'
    ];
    
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
   
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    
    // Add relationship for reviews
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    
    // Helper method to check if a booking is paid
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
    
    // Helper method to check if a booking is pending payment
    public function isPendingPayment()
    {
        return $this->status === 'confirmed' && $this->payment_status === 'unpaid';
    }
    
    // Check if the booking can be reviewed
    public function canBeReviewed()
    {
        return $this->status === 'confirmed';
    }
    
    // Check if a specific user has already reviewed this booking
    public function hasBeenReviewedBy($userId)
    {
        return $this->reviews()->where('reviewer_id', $userId)->exists();
    }
    
    // Get the review by a specific user for this booking
    public function getReviewBy($userId)
    {
        return $this->reviews()->where('reviewer_id', $userId)->first();
    }
}