<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
        
        return (new MailMessage)
                    ->subject('Booking Confirmed')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Your booking has been confirmed by the driver.')
                    ->line('Booking Details:')
                    ->line('Driver: ' . $this->booking->driver->name)
                    ->line('Pickup Date: ' . $pickupDate)
                    ->line('Pickup Time: ' . $pickupTime)
                    ->line('Pickup Location: ' . $this->booking->pickup_place)
                    ->line('Destination: ' . $this->booking->destination)
                    ->action('View Booking', url('/bookings'))
                    ->line('Thank you for using our service!')
                    ->line('You can contact your driver through the chat feature in your dashboard.');
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
            'driver_name' => $this->booking->driver->name,
            'pickup_time' => $this->booking->pickup_time,
            'pickup_place' => $this->booking->pickup_place,
            'destination' => $this->booking->destination,
        ];
    }
}