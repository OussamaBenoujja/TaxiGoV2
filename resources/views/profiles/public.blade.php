@extends('layouts.theme')

@section('content')
<div class="bg-gray-950 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="card mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-6 relative">
                <!-- Back Button -->
                <a href="{{ url()->previous() }}" class="absolute top-4 left-4 h-8 w-8 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center text-white">
                    <i class="fas fa-arrow-left"></i>
                </a>
                
                <div class="flex flex-col items-center sm:flex-row sm:items-start pt-6">
                    <!-- Profile Picture/Avatar -->
                    <div class="h-24 w-24 sm:h-32 sm:w-32 rounded-full bg-gray-700 border-4 border-yellow-500 overflow-hidden flex items-center justify-center shadow-lg mb-4 sm:mb-0 sm:mr-6">
                        @if($user->role == 'driver' && $user->driverProfile && $user->driverProfile->profile_picture)
                            <img src="{{ Storage::url($user->driverProfile->profile_picture) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-yellow-500 text-4xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>
                    
                    <div class="text-center sm:text-left flex-1">
                        <h1 class="text-3xl font-bold text-white">{{ $user->name }}</h1>
                        <div class="text-gray-400">{{ ucfirst($user->role) }}</div>
                        
                        <div class="flex items-center mt-2 justify-center sm:justify-start">
                            <div class="flex items-center text-yellow-500">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($user->average_rating))
                                        <i class="fas fa-star"></i>
                                    @elseif($i - 0.5 <= $user->average_rating)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-yellow-500 font-semibold">{{ number_format($user->average_rating, 1) }}</span>
                            <span class="ml-2 text-gray-400">({{ $user->reviews_count }} {{ Str::plural('review', $user->reviews_count) }})</span>
                        </div>
                        
                        @if($user->role == 'driver' && $user->driverProfile)
                            <div class="mt-4 flex flex-wrap gap-2 justify-center sm:justify-start">
                                <div class="bg-gray-800 px-3 py-1 rounded-full text-sm text-gray-300">
                                    <i class="fas fa-car text-yellow-500 mr-1"></i>
                                    {{ $user->driverProfile->car_model }}
                                </div>
                                
                                <div class="bg-gray-800 px-3 py-1 rounded-full text-sm text-gray-300">
                                    <i class="fas fa-map-marker-alt text-yellow-500 mr-1"></i>
                                    {{ $user->driverProfile->city }}
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Review Button -->
                    <div class="mt-6 sm:mt-0">
                        @if($canReview && !$reviewExists)
                            <a href="{{ route('reviews.create', $sharedBooking->id) }}" class="btn-primary flex items-center">
                                <i class="fas fa-star mr-2"></i>
                                Write a Review
                            </a>
                        @elseif($canReview && $reviewExists)
                            <a href="{{ route('reviews.edit', $review->id) }}" class="btn-secondary flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Your Review
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($user->role == 'driver' && $user->driverProfile && $user->driverProfile->description)
                <div class="p-6 border-t border-gray-800">
                    <h3 class="text-lg font-semibold text-white mb-2">About</h3>
                    <p class="text-gray-300">{{ $user->driverProfile->description }}</p>
                </div>
            @endif
            
            @if($user->role == 'driver' && $user->driverProfile)
                <div class="p-6 border-t border-gray-800">
                    <h3 class="text-lg font-semibold text-white mb-4">Working Hours</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-400 uppercase mb-2">Available Days</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ in_array($day, $user->driverProfile->work_days) 
                                            ? 'bg-yellow-500 bg-opacity-20 text-yellow-400' 
                                            : 'bg-gray-800 text-gray-500' }}">
                                        {{ $day }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-400 uppercase mb-2">Working Hours</div>
                            <div class="flex items-center space-x-2 text-white">
                                <i class="fas fa-clock text-yellow-500"></i>
                                <span>{{ \Carbon\Carbon::parse($user->driverProfile->work_start)->format('h:i A') }}</span>
                                <span>to</span>
                                <span>{{ \Carbon\Carbon::parse($user->driverProfile->work_end)->format('h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Reviews Section -->
        <div class="card">
            <div class="card-header flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">Reviews</h2>
                <a href="{{ route('reviews.user', $user->id) }}" class="text-yellow-500 hover:text-yellow-400 text-sm">
                    View All Reviews
                </a>
            </div>
            
            <div class="divide-y divide-gray-800">
                @forelse($user->receivedReviews as $review)
                    <div class="p-6 hover:bg-gray-800 transition duration-150">
                        <div class="flex flex-col md:flex-row items-start md:items-center mb-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-white mr-3">
                                    {{ substr($review->reviewer->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('profiles.public', $review->reviewer->id) }}" class="text-white font-medium hover:text-yellow-500">{{ $review->reviewer->name }}</a>
                                    <div class="text-gray-400 text-sm">{{ ucfirst($review->reviewer->role) }}</div>
                                </div>
                            </div>
                            
                            <div class="mt-2 md:mt-0 md:ml-auto flex items-center">
                                <div class="flex items-center text-yellow-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($review->rating))
                                            <i class="fas fa-star"></i>
                                        @elseif ($i - 0.5 <= $review->rating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-white font-semibold">{{ number_format($review->rating, 1) }}</span>
                                <span class="ml-3 text-gray-400 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-2 text-gray-300">
                            {{ $review->comment }}
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-800 mb-4">
                            <i class="fas fa-star text-yellow-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-white">No reviews yet</h3>
                        <p class="text-gray-400 mt-2">{{ $user->name }} has not received any reviews yet.</p>
                    </div>
                @endforelse
            </div>
            
            @if($user->reviews_count > 5)
                <div class="px-6 py-4 border-t border-gray-800 text-center">
                    <a href="{{ route('reviews.user', $user->id) }}" class="btn-secondary inline-flex items-center">
                        <i class="fas fa-list mr-2"></i>
                        See All {{ $user->reviews_count }} Reviews
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection