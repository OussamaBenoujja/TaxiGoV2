<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // For debugging, let's add a log message
        \Illuminate\Support\Facades\Log::info('AdminMiddleware executed. User: ' . (Auth::check() ? Auth::user()->email : 'Not logged in'));
        
        // Let everyone through for testing
        // return $next($request);
        
        // Original logic
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'You need admin privileges to access this area.');
    }
}