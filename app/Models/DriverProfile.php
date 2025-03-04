<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    protected $fillable = [
        'user_id', 'description', 'car_model', 'city', 'work_days', 'work_start', 'work_end', 'profile_picture',
    ];

    protected $casts = [
        'work_days' => 'array', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
