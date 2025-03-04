<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
               
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(rand(1, 10000)), // Random password
                    'role' => 'client', 
                ]);
            } else {
               
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                }
            }
            
            Auth::login($user);
    
            return redirect()->route('dashboard');
            
            
            session()->regenerate();
            
           
            if (Auth::check()) {
                return redirect('/dashboard');
            } else {
                
                return redirect('/login')->withErrors('Authentication failed after Google login');
            }
        }catch (Exception $e) {
            
            error_log('GOOGLE AUTH ERROR: ' . $e->getMessage());
           
            error_log($e->getTraceAsString());
            
            return redirect('/login')->withErrors('Google authentication failed: ' . $e->getMessage());
        }
    }
}