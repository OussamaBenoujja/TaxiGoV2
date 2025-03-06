@extends('layouts.theme')

@section('content')
<div class="bg-gray-950 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Write a Review</h1>
                <p class="mt-2 text-gray-400">Share your experience with {{ $reviewee->name }}</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <a href="{{ url()->previous() }}" class="btn-secondary flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-white">Booking Details</h2>
            </div>
            
            <div class="p-6 border-b border-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-400">Pickup Time</p>
                        <p class="text-white">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">From</p>
                        <p class="text-white">{{ $booking->pickup_place }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">To</p>
                        <p class="text-white">{{ $booking->destination }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Status</p>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold 
                            bg-green-500 bg-opacity-20 text-green-400">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <form method="POST" action="{{ route('reviews.store') }}">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <input type="hidden" name="reviewee_id" value="{{ $reviewee->id }}">
                    
                    <div class="mb-6">
                        <label class="form-label mb-2">Rating</label>
                        <div class="rating-select flex items-center space-x-1">
                            <input type="hidden" name="rating" id="rating" value="5">
                            @for ($i = 1; $i <= 10; $i++)
                                <div class="star-half cursor-pointer text-xl text-gray-400 hover:text-yellow-500" 
                                     data-value="{{ $i * 0.5 }}">
                                    @if ($i % 2 == 0)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="fas fa-star-half-alt"></i>
                                    @endif
                                </div>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="comment" class="form-label">Your Review</label>
                        <textarea id="comment" name="comment" rows="4" class="form-input resize-none" 
                                  placeholder="Share your experience...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ratingInput = document.getElementById('rating');
        const stars = document.querySelectorAll('.star-half');
        
        // Initialize stars
        function setRating(rating) {
            ratingInput.value = rating;
            
            stars.forEach(star => {
                const starValue = parseFloat(star.getAttribute('data-value'));
                if (starValue <= rating) {
                    star.classList.add('text-yellow-500');
                    star.classList.remove('text-gray-400');
                } else {
                    star.classList.add('text-gray-400');
                    star.classList.remove('text-yellow-500');
                }
            });
        }
        
        // Set initial rating
        setRating(5.0);
        
        // Handle star click events
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = parseFloat(this.getAttribute('data-value'));
                setRating(value);
            });
        });
    });
</script>
@endpush
@endsection