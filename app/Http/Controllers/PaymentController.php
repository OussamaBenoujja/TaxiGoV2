<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a payment intent for a booking
     */
    public function createPaymentIntent(Booking $booking)
    {
        // Make sure the booking belongs to the authenticated user
        if ($booking->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Make sure the booking is confirmed and unpaid
        if ($booking->status !== 'confirmed' || $booking->payment_status !== 'unpaid') {
            return response()->json(['error' => 'Booking is not eligible for payment'], 400);
        }

        try {
            // Calculate the amount based on distance or fixed rate
            // This is a simplified example - you'd likely have a more complex pricing model
            $amount = $booking->amount ?? $this->calculateBookingAmount($booking);

            // Create a payment intent
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amount * 100, // Convert to cents for Stripe
                'currency' => 'usd',
                'metadata' => [
                    'booking_id' => $booking->id,
                ],
            ]);

            // Save the payment intent ID and amount to the booking
            $booking->payment_intent_id = $paymentIntent->id;
            $booking->amount = $amount;
            $booking->save();

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $amount,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle the Stripe webhook
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        $bookingId = $paymentIntent->metadata->booking_id ?? null;
        
        if (!$bookingId) {
            Log::error('Payment succeeded but no booking ID in metadata', ['payment_intent' => $paymentIntent->id]);
            return;
        }

        $booking = Booking::where('payment_intent_id', $paymentIntent->id)->first();
        
        if (!$booking) {
            Log::error('Payment succeeded but booking not found', ['payment_intent' => $paymentIntent->id, 'booking_id' => $bookingId]);
            return;
        }

        // Update booking payment status
        $booking->payment_status = 'paid';
        $booking->save();

        // You could also trigger a notification to the client and driver here
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        $bookingId = $paymentIntent->metadata->booking_id ?? null;
        
        if (!$bookingId) {
            Log::error('Payment failed but no booking ID in metadata', ['payment_intent' => $paymentIntent->id]);
            return;
        }

        $booking = Booking::where('payment_intent_id', $paymentIntent->id)->first();
        
        if (!$booking) {
            Log::error('Payment failed but booking not found', ['payment_intent' => $paymentIntent->id, 'booking_id' => $bookingId]);
            return;
        }

        // Update booking payment status
        $booking->payment_status = 'failed';
        $booking->save();

        // You could also trigger a notification to the client here
    }

    /**
     * Calculate booking amount - customize this based on your business logic
     */
    private function calculateBookingAmount(Booking $booking)
    {
        // Simplified calculation - in a real app, you'd calculate based on distance, time, etc.
        return 25.00; // Default fare in USD
    }

    /**
     * Display the payment form
     */
    public function showPaymentForm(Booking $booking)
    {
        // Ensure the current user owns this booking
        if ($booking->client_id !== auth()->id()) {
            return redirect()->route('bookings.index')->with('error', 'Unauthorized access');
        }

        // Ensure the booking is confirmed and unpaid
        if ($booking->status !== 'confirmed' || $booking->payment_status !== 'unpaid') {
            return redirect()->route('bookings.index')->with('error', 'This booking is not eligible for payment');
        }

        // Calculate amount if not already set
        if (!$booking->amount) {
            $booking->amount = $this->calculateBookingAmount($booking);
            $booking->save();
        }

        return view('payment.form', [
            'booking' => $booking,
            'amount' => $booking->amount,
            'stripeKey' => config('services.stripe.key')
        ]);
    }

    /**
     * Mark payment as complete (after JS processes the payment)
     */
    public function markPaymentComplete(Request $request, Booking $booking)
    {
        // This is called by the JS after a successful payment
        if ($booking->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $booking->payment_status = 'paid';
        $booking->save();

        return response()->json(['success' => true]);
    }
}