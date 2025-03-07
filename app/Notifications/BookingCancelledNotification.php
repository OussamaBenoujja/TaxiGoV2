<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingCancelledNotification extends Notification 
{
    

    protected $booking;
    protected $cancelledBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $cancelledBy = 'driver')
    {
        $this->booking = $booking;
        $this->cancelledBy = $cancelledBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $pickupDate = \Carbon\Carbon::parse($this->booking->pickup_time)->format('F j, Y');
        $pickupTime = \Carbon\Carbon::parse($this->booking->pickup_time)->format('g:i A');
        
        $message = (new MailMessage)
                    ->subject('Booking Cancelled')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Your booking has been cancelled.');
                    
        if ($this->cancelledBy == 'driver') {
            $message->line('The driver has cancelled this booking.');
        } elseif ($this->cancelledBy == 'client') {
            $message->line('You have cancelled this booking.');
        } elseif ($this->cancelledBy == 'system') {
            $message->line('The booking was automatically cancelled by the system.');
        }
                    
        return $message->line('Booking Details:')
                    ->line('Pickup Date: ' . $pickupDate)
                    ->line('Pickup Time: ' . $pickupTime)
                    ->line('Pickup Location: ' . $this->booking->pickup_place)
                    ->line('Destination: ' . $this->booking->destination)
                    ->action('Book Again', url('/booking/create'))
                    ->line('We apologize for any inconvenience this may have caused.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'cancelled_by' => $this->cancelledBy,
            'pickup_time' => $this->booking->pickup_time,
            'pickup_place' => $this->booking->pickup_place,
            'destination' => $this->booking->destination,
        ];
    }
}