<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\DriverProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class RegisteredUserController extends Controller
{

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {

        $request->validate((new \App\Http\Requests\RegisterRequest)->rules());
    
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
    
        if ($request->role === 'driver') {
            $data = [
                'user_id'     => $user->id,
                'description' => $request->description,
                'car_model'   => $request->car_model,
                'city'        => $request->city,
                'work_days'   => $request->work_days,
                'work_start'  => $request->work_start,
                'work_end'    => $request->work_end,
            ];

            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $data['profile_picture'] = $path;
            }
    
            DriverProfile::create($data);

        }

        Auth::login($user);
    
        return redirect()->route('dashboard');
    }
}
