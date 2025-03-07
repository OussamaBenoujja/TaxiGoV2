<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class NewBookingNotification extends Notification 
{
    

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
                    ->subject('New Booking Request')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('You have received a new booking request from ' . $this->booking->client->name . '.')
                    ->line('Booking Details:')
                    ->line('Pickup Date: ' . $pickupDate)
                    ->line('Pickup Time: ' . $pickupTime)
                    ->line('Pickup Location: ' . $this->booking->pickup_place)
                    ->line('Destination: ' . $this->booking->destination)
                    ->action('View Booking', url('/profile'))
                    ->line('Please confirm or cancel this booking soon.');
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
            'client_name' => $this->booking->client->name,
            'pickup_time' => $this->booking->pickup_time,
            'pickup_place' => $this->booking->pickup_place,
            'destination' => $this->booking->destination,
        ];
    }
}