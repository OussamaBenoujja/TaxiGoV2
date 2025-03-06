@extends('layouts.theme')

@section('content')
<div class="bg-gray-950 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card">
            <div class="card-header">
                <h1 class="text-xl font-semibold text-white">Payment for Booking #{{ $booking->id }}</h1>
            </div>
            
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-white mb-4">Booking Details</h2>
                    
                    <div class="bg-gray-800 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-400 text-sm">Driver</p>
                                <p class="text-white">{{ $booking->driver->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Pickup Time</p>
                                <p class="text-white">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">From</p>
                                <p class="text-white">{{ $booking->pickup_place }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">To</p>
                                <p class="text-white">{{ $booking->destination }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-900 bg-opacity-20 rounded-lg p-6 mb-8 border border-yellow-700">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-yellow-500">Payment Amount</h3>
                            <span class="text-2xl font-bold text-white">${{ number_format($amount, 2) }}</span>
                        </div>
                        <p class="text-gray-400 text-sm">This is the total amount for your ride.</p>
                    </div>
                </div>
                
                <form id="payment-form" class="space-y-6">
                    <div>
                        <label for="card-element" class="block font-medium text-gray-300 mb-2">
                            Credit or debit card
                        </label>
                        <div id="card-element" class="bg-gray-800 border border-gray-700 rounded-md p-4">
                            <!-- Stripe Element will be inserted here -->
                        </div>
                        <div id="card-errors" class="mt-2 text-sm text-red-500" role="alert"></div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                id="submit-button" 
                                class="btn-primary text-center py-3 px-6 w-full md:w-auto flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-black hidden" id="spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Pay ${{ number_format($amount, 2) }}
                        </button>
                    </div>
                </form>
                
                <div id="payment-success" class="hidden mt-6 bg-green-900 bg-opacity-20 text-green-400 p-4 rounded-lg border border-green-800">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p>Payment successful! Redirecting to your bookings...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create a Stripe client
        const stripe = Stripe('{{ $stripeKey }}');
        const elements = stripe.elements();
        
        // Custom styling
        const style = {
            base: {
                color: '#fff',
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#6b7280'
                }
            },
            invalid: {
                color: '#f87171',
                iconColor: '#f87171'
            }
        };
        
        // Create an instance of the card Element
        const cardElement = elements.create('card', { style: style });
        
        // Add an instance of the card Element into the `card-element` div
        cardElement.mount('#card-element');
        
        // Handle real-time validation errors from the card Element
        cardElement.addEventListener('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Handle form submission
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const spinner = document.getElementById('spinner');
        const successMessage = document.getElementById('payment-success');
        
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            // Disable the submit button and show spinner
            submitButton.disabled = true;
            spinner.classList.remove('hidden');
            
            try {
                // First create a payment intent on the server
                const response = await fetch('/bookings/{{ $booking->id }}/payment/intent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Confirm the payment with Stripe.js
                const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: {
                        card: cardElement,
                    }
                });
                
                if (error) {
                    // Show error message
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = error.message;
                    
                    // Re-enable the submit button and hide spinner
                    submitButton.disabled = false;
                    spinner.classList.add('hidden');
                } else if (paymentIntent.status === 'succeeded') {
                    // Mark the payment as complete on our server
                    await fetch('/bookings/{{ $booking->id }}/payment/complete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    // Show success message
                    form.classList.add('hidden');
                    successMessage.classList.remove('hidden');
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = "{{ route('bookings.index') }}";
                    }, 2000);
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Show error message
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message || 'An error occurred while processing your payment.';
                
                // Re-enable the submit button and hide spinner
                submitButton.disabled = false;
                spinner.classList.add('hidden');
            }
        });
    });
</script>
@endpush
@endsection