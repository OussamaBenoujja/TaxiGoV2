<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function driverProfile()
    {
        return $this->hasOne(DriverProfile::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'driver_id');
    }
    
    public function clientBookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }
    
    // Reviews given by this user
    public function givenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }
    
    // Reviews received by this user
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }
    
    // Calculate average rating for this user
    public function getAverageRatingAttribute()
    {
        return $this->receivedReviews()->avg('rating') ?? 0;
    }
    
    // Count the number of reviews received
    public function getReviewsCountAttribute()
    {
        return $this->receivedReviews()->count();
    }
}