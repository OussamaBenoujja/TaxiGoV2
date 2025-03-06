<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'reviewer_id',
        'reviewee_id', 
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    /**
     * Get the booking associated with the review.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who wrote the review.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the user who received the review.
     */
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}