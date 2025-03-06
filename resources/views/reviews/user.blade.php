@extends('layouts.theme')

@section('content')
<div class="bg-gray-950 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Reviews for {{ $user->name }}</h1>
                <div class="mt-2 flex items-center">
                    <div class="flex items-center">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($user->average_rating))
                                <i class="fas fa-star text-yellow-500"></i>
                            @elseif ($i - 0.5 <= $user->average_rating)
                                <i class="fas fa-star-half-alt text-yellow-500"></i>
                            @else
                                <i class="far fa-star text-gray-400"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="ml-2 text-yellow-500 font-semibold">{{ number_format($user->average_rating, 1) }}</span>
                    <span class="ml-2 text-gray-400">({{ $user->reviews_count }} {{ Str::plural('review', $user->reviews_count) }})</span>
                </div>
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
                <h2 class="text-xl font-semibold text-white">All Reviews</h2>
            </div>
            
            <div class="divide-y divide-gray-800">
                @forelse($reviews as $review)
                    <div class="p-6 hover:bg-gray-800 transition duration-150">
                        <div class="flex flex-col md:flex-row items-start md:items-center mb-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-white mr-3">
                                    {{ substr($review->reviewer->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-sm">
                                        <a href="{{ route('profiles.public', $review->reviewer->id) }}" class="text-white hover:text-yellow-500">
                                            {{ $review->reviewer->name }}
                                        </a>
                                    </div>
                                    <div class="text-gray-400 text-sm">{{ $review->reviewer->role }}</div>
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
                        
                        <div class="mt-4 text-gray-400 text-sm">
                            <div class="inline-flex items-center">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                <span>Booking date: {{ \Carbon\Carbon::parse($review->booking->pickup_time)->format('M d, Y') }}</span>
                            </div>
                            <div class="inline-flex items-center ml-4">
                                <i class="fas fa-route mr-1"></i>
                                <span>{{ $review->booking->pickup_place }} → {{ $review->booking->destination }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-800 mb-4">
                            <i class="fas fa-star text-yellow-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-white">No reviews yet</h3>
                        <p class="text-gray-400 mt-2">This user has not received any reviews yet.</p>
                    </div>
                @endforelse
            </div>
            
            @if($reviews->hasPages())
                <div class="px-6 py-4 border-t border-gray-800">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection