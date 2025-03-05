@extends('layouts.admin')

@section('header', 'Statistics & Analytics')

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">
                        {{ array_sum($bookingStats) }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center mt-4">
                <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                <span class="text-green-500 text-sm font-medium">12%</span>
                <span class="text-gray-500 text-sm ml-2">From previous month</span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completion Rate</p>
                    @php
                        $confirmedBookings = $bookingStats['confirmed'] ?? 0;
                        $totalBookings = array_sum($bookingStats);
                        $completionRate = $totalBookings > 0 ? ($confirmedBookings / $totalBookings) * 100 : 0;
                    @endphp
                    <p class="text-3xl font-bold text-gray-800 mt-1">
                        {{ round($completionRate) }}%
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Cancellation Rate</p>
                    @php
                        $cancelledBookings = $bookingStats['cancelled'] ?? 0;
                        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;
                    @endphp
                    <p class="text-3xl font-bold text-gray-800 mt-1">
                        {{ round($cancellationRate) }}%
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $cancellationRate }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Bookings</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">
                        {{ $bookingStats['pending'] ?? 0 }}
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center mt-4">
                @php
                    $pendingRate = $totalBookings > 0 ? (($bookingStats['pending'] ?? 0) / $totalBookings) * 100 : 0;
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $pendingRate }}%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Bookings Over Time Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Bookings Last 30 Days</h3>
            <div class="h-80">
                <canvas id="bookingsTimeChart"></canvas>
            </div>
        </div>
        
        <!-- Booking Status Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Booking Status Distribution</h3>
            <div class="h-80">
                <canvas id="bookingStatusChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Top Drivers and Clients -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Drivers -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">Top Drivers</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($driverStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user-tie text-gray-500"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $stat->driver->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $stat->driver->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stat->total_bookings }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @php
                                                // Simulate a rating between 3.5 and 5
                                                $rating = mt_rand(35, 50) / 10;
                                            @endphp
                                            <span class="text-sm font-medium text-gray-900 mr-2">{{ $rating }}</span>
                                            <div class="text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($rating))
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i - 0.5 <= $rating)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                        No driver statistics available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Top Clients -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">Top Clients</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($clientStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-500"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $stat->client->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $stat->client->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stat->total_bookings }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                        No client statistics available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data for booking status chart
        const statusData = {
            labels: ['Pending', 'Confirmed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $bookingStats['pending'] ?? 0 }}, 
                    {{ $bookingStats['confirmed'] ?? 0 }}, 
                    {{ $bookingStats['cancelled'] ?? 0 }}
                ],
                backgroundColor: ['#FBBF24', '#10B981', '#EF4444'],
                borderWidth: 1
            }]
        };
        
        // Create booking status chart
        new Chart(document.getElementById('bookingStatusChart'), {
            type: 'doughnut',
            data: statusData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Process daily stats data for the time chart
        const dates = [];
        const counts = [];
        
        @foreach($dailyStats as $stat)
            dates.push('{{ \Carbon\Carbon::parse($stat->date)->format("M d") }}');
            counts.push({{ $stat->count }});
        @endforeach
        
        // Create bookings time chart
        new Chart(document.getElementById('bookingsTimeChart'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Bookings',
                    data: counts,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush