<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Notifications\BookingCancelledNotification;

class CancelPendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel all pending bookings that are one hour away from pickup time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get current time
        $now = Carbon::now();
        
        // Find bookings that:
        // 1. Are still in "pending" status
        // 2. Have a pickup time that's 1 hour from now (with some buffer)
        // Note: We want to cancel bookings where the pickup time is LESS than 1 hour away
        $bookings = Booking::where('status', 'pending')
            ->where('pickup_time', '>', $now)
            ->where('pickup_time', '<=', $now->copy()->addHour())
            ->get();
            
        if ($bookings->isEmpty()) {
            $this->info("No pending bookings to cancel at this time.");
            return Command::SUCCESS;
        }
        
        $count = 0;
        
        foreach ($bookings as $booking) {
            // Cancel the booking
            $booking->status = 'cancelled';
            $booking->save();
            
            // Log the cancellation for audit purposes
            Log::info("Auto-cancelled pending booking #{$booking->id} for pickup at {$booking->pickup_time}");
            
            // Notify the client and driver
            $client = $booking->client;
            $driver = $booking->driver;
            
            $client->notify(new BookingCancelledNotification($booking, 'system'));
            $driver->notify(new BookingCancelledNotification($booking, 'system'));
            
            $count++;
        }
        
        $this->info("Cancelled {$count} pending bookings.");
        
        return Command::SUCCESS;
    }
}