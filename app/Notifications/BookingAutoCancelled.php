<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingAutoCancelled extends Notification implements ShouldQueue
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
        $pickupTime = \Carbon\Carbon::parse($this->booking->pickup_time)->format('F j, Y, g:i a');
        
        return (new MailMessage)
                    ->subject('Your Taxi Booking Has Been Cancelled')
                    ->line('Unfortunately, your taxi booking has been automatically cancelled as no driver confirmed within the required time.')
                    ->line('Booking Details:')
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
            //
        ];
    }
}