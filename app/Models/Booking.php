<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'client_id', 
        'driver_id', 
        'pickup_time', 
        'pickup_place',
        'destination',
        'status'
    ];
    
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    

}