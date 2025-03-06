<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage with driver listings
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all users with the 'driver' role and their profiles
        $drivers = User::where('role', 'driver')
            ->with('driverProfile')
            ->whereHas('driverProfile') // Only drivers with profiles
            ->get();
        
        // Pass the drivers to the view
        return view('home', compact('drivers'));
    }
}