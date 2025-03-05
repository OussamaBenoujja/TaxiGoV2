@extends('layouts.theme')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div class="bg-yellow-500 p-3 rounded-full shadow-lg">
                <i class="fas fa-taxi text-black text-3xl"></i>
            </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
            Sign in to your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-400">
            Or
            <a href="{{ route('register') }}" class="font-medium text-yellow-500 hover:text-yellow-400">
                create a new account
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="card">
            <div class="py-8 px-4 sm:px-10">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-sm text-green-500" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="form-label">Email address</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-500"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
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
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="form-input pl-10 @error('password') border-red-500 @enderror">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" class="form-checkbox">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-300">
                                Remember me
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-sm">
                                <a href="{{ route('password.request') }}" class="font-medium text-yellow-500 hover:text-yellow-400">
                                    Forgot your password?
                                </a>
                            </div>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="w-full btn-primary flex justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign in
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-gray-900 text-gray-400">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('google.redirect') }}" class="w-full inline-flex justify-center py-2 px-4 border border-gray-700 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-google text-red-500 mr-2"></i>
                            Sign in with Google
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection