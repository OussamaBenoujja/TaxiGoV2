<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get counts for dashboard
        $userCount = User::count();
        $driverCount = User::where('role', 'driver')->count();
        $clientCount = User::where('role', 'client')->count();
        $bookingCount = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        
        // Recent bookings
        $recentBookings = Booking::with(['client', 'driver'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Revenue statistics (assuming you add a payment column to bookings later)
        $totalRevenue = 0; // Placeholder for future implementation
        
        // Monthly booking statistics
        $monthlyStats = Booking::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        
        // Fill in missing months with zero values
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[$i] = $monthlyStats[$i] ?? 0;
        }
        
        return view('admin.dashboard', compact(
            'userCount', 
            'driverCount', 
            'clientCount', 
            'bookingCount', 
            'pendingBookings', 
            'confirmedBookings', 
            'cancelledBookings', 
            'recentBookings', 
            'totalRevenue', 
            'chartData'
        ));
    }
    
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users', compact('users'));
    }
    
    public function bookings()
    {
        $bookings = Booking::with(['client', 'driver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.bookings', compact('bookings'));
    }
    
    public function drivers()
    {
        $drivers = User::where('role', 'driver')
            ->with('driverProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Get availability statistics
        foreach ($drivers as $driver) {
            $driver->availability = $this->calculateDriverAvailability($driver);
            $driver->booking_count = Booking::where('driver_id', $driver->id)->count();
            $driver->completed_count = Booking::where('driver_id', $driver->id)
                ->where('status', 'confirmed')->count();
        }
        
        return view('admin.drivers', compact('drivers'));
    }
    
    public function statistics()
    {
        // Get booking statistics by status
        $bookingStats = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
            
        // Get bookings per day for the last 30 days
        $dailyStats = Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Driver statistics
        $driverStats = Booking::selectRaw('driver_id, COUNT(*) as total_bookings')
            ->with('driver')
            ->groupBy('driver_id')
            ->orderBy('total_bookings', 'desc')
            ->take(10)
            ->get();
            
        // Client statistics
        $clientStats = Booking::selectRaw('client_id, COUNT(*) as total_bookings')
            ->with('client')
            ->groupBy('client_id')
            ->orderBy('total_bookings', 'desc')
            ->take(10)
            ->get();
            
        return view('admin.statistics', compact('bookingStats', 'dailyStats', 'driverStats', 'clientStats'));
    }
    
    private function calculateDriverAvailability($driver)
    {
        // This is a simplified example - you'd need to customize this based on your business logic
        if (!$driver->driverProfile) {
            return 0;
        }
        
        $workDays = count($driver->driverProfile->work_days ?? []);
        $workHoursPerDay = 0;
        
        if ($driver->driverProfile->work_start && $driver->driverProfile->work_end) {
            $start = Carbon::parse($driver->driverProfile->work_start);
            $end = Carbon::parse($driver->driverProfile->work_end);
            
            // Handle overnight shifts
            if ($end->lt($start)) {
                $end->addDay();
            }
            
            $workHoursPerDay = $end->diffInHours($start);
        }
        
        // Available hours per week
        $availableHoursPerWeek = $workDays * $workHoursPerDay;
        
        // Calculate percentage against standard 40-hour work week (40 hours = 100%)
        return min(100, ($availableHoursPerWeek / 40) * 100);
    }
}