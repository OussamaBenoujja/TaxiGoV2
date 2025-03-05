@extends('layouts.theme')

@section('content')
<div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div class="bg-yellow-500 p-3 rounded-full shadow-lg">
                <i class="fas fa-user-plus text-black text-3xl"></i>
            </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
            Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-400">
            Or
            <a href="{{ route('login') }}" class="font-medium text-yellow-500 hover:text-yellow-400">
                sign in to your existing account
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="card">
            <div class="py-8 px-4 sm:px-10">
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="form-label">Name</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <input id="name" name="name" type="text" required autofocus 
                                value="{{ old('name') }}"
                                class="form-input pl-10 @error('name') border-red-500 @enderror">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="form-label">Email address</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-500"></i>
                            </div>
                            <input id="email" name="email" type="email" required
                                value="{{ old('email') }}"
                                class="form-input pl-10 @error('email') border-red-500 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="form-label">Password</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-500"></i>
                            </div>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                class="form-input pl-10 @error('password') border-red-500 @enderror">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-500"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="form-input pl-10">
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label for="role" class="form-label">Register As:</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user-tag text-gray-500"></i>
                            </div>
                            <select name="role" id="role" onchange="toggleDriverFields()" 
                                class="form-select pl-10">
                                <option value="client">Client</option>
                                <option value="driver">Driver</option>
                            </select>
                        </div>
                    </div>

                    <!-- Driver-Specific Fields -->
                    <div id="driverFields" style="display: none;" class="space-y-6 border-t border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-yellow-500">Driver Details</h3>
                        
                        <div>
                            <label for="car_model" class="form-label">Car Model</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-car text-gray-500"></i>
                                </div>
                                <input type="text" name="car_model" id="car_model" value="{{ old('car_model') }}"
                                    class="form-input pl-10">
                            </div>
                        </div>
                        
                        <div>
                            <label for="city" class="form-label">City</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-city text-gray-500"></i>
                                </div>
                                <input type="text" name="city" id="city" value="{{ old('city') }}"
                                    class="form-input pl-10">
                            </div>
                        </div>
                        
                        <div>
                            <label for="description" class="form-label">Description</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <textarea name="description" id="description" rows="3"
                                    class="form-input">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        
                        <div>
                            <fieldset>
                                <legend class="form-label mb-2">Work Days</legend>
                                <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="work_days[]" value="{{ $day }}" id="work_day_{{ $day }}"
                                            class="form-checkbox">
                                        <label for="work_day_{{ $day }}" class="ml-2 text-sm text-gray-300">
                                            {{ $day }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="work_start" class="form-label">Work Start (HH:MM)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-clock text-gray-500"></i>
                                    </div>
                                    <input type="time" name="work_start" id="work_start"
                                        class="form-input pl-10">
                                </div>
                            </div>
                            
                            <div>
                                <label for="work_end" class="form-label">Work End (HH:MM)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-clock text-gray-500"></i>
                                    </div>
                                    <input type="time" name="work_end" id="work_end"
                                        class="form-input pl-10">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label">Profile Picture</label>
                            <div class="mt-2 flex items-center">
                                <span class="h-12 w-12 rounded-full overflow-hidden bg-gray-800 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-xl"></i>
                                </span>
                                <label for="profile_picture" class="ml-5 bg-gray-800 py-2 px-3 border border-gray-700 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-300 hover:bg-gray-700 hover:text-yellow-500 focus:outline-none cursor-pointer">
                                    <span>Upload</span>
                                    <input type="file" name="profile_picture" id="profile_picture" class="sr-only">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full btn-primary flex justify-center">
                            <i class="fas fa-user-plus mr-2"></i>
                            Register
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-center mt-6">
                        <a href="{{ route('google.redirect') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fab fa-google text-red-500 mr-2"></i>
                            Register with Google
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDriverFields() {
        const role = document.getElementById('role').value;
        document.getElementById('driverFields').style.display = role === 'driver' ? 'block' : 'none';
    }
    // Initialize the form based on previous selection (if any)
    document.addEventListener('DOMContentLoaded', toggleDriverFields);
</script>
@endsection