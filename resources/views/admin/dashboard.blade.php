@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- User Stats Card -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p class="text-2xl font-semibold">{{ $userCount }}</p>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                {{ $driverCount }} Drivers, {{ $clientCount }} Clients
            </div>
        </div>
        
        <!-- Booking Stats Card -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Bookings</p>
                    <p class="text-2xl font-semibold">{{ $bookingCount }}</p>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Pending:</span>
                    <span>{{ $pendingBookings }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Confirmed:</span>
                    <span>{{ $confirmedBookings }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Cancelled:</span>
                    <span>{{ $cancelledBookings }}</span>
                </div>
            </div>
        </div>
        
        <!-- More cards here -->
    </div>
    
    <!-- Charts and Recent Bookings -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Monthly Bookings</h2>
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Booking Status</h2>
            <canvas id="statusChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Recent Bookings Table -->
    <div class="mt-8 bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Recent Bookings</h2>
            <a href="{{ route('admin.bookings') }}" class="text-blue-500 hover:underline">View All</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Driver</th>
                        <th class="px-6 py-3">Pickup Time</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentBookings as $booking)
                    <tr>
                        <td class="px-6 py-4">#{{ $booking->id }}</td>
                        <td class="px-6 py-4">{{ $booking->client->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $booking->driver->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $booking->status == 'confirmed' ? 'bg-green-100 text-green-800' : 
                                ($booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                'bg-red-100 text-red-800') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Bookings',
                data: [
                    {{ $chartData[1] }}, {{ $chartData[2] }}, {{ $chartData[3] }}, {{ $chartData[4] }},
                    {{ $chartData[5] }}, {{ $chartData[6] }}, {{ $chartData[7] }}, {{ $chartData[8] }},
                    {{ $chartData[9] }}, {{ $chartData[10] }}, {{ $chartData[11] }}, {{ $chartData[12] }}
                ],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
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
    
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Confirmed', 'Cancelled'],
            datasets: [{
                data: [{{ $pendingBookings }}, {{ $confirmedBookings }}, {{ $cancelledBookings }}],
                backgroundColor: ['#FBBF24', '#10B981', '#EF4444'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush