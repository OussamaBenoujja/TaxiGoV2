<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 
        'sender_id', 
        'receiver_id', 
        'message', 
        'is_read'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'is_read' => 'boolean',
    ];
    
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
