
<script src="https://cdn.tailwindcss.com"></script>
<!-- resources/views/auth/register.blade.php -->
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Create your account
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Name
                    </label>
                    <div class="mt-1">
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <div class="mt-1">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        Register As:
                    </label>
                    <div class="mt-1">
                        <select name="role" id="role" onchange="toggleDriverFields()"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="client">Client</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                </div>

                <!-- Driver-Specific Fields -->
                <div id="driverFields" style="display: none;" class="space-y-6">
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Driver Details</h3>
                    </div>
                    
                    <div>
                        <label for="car_model" class="block text-sm font-medium text-gray-700">
                            Car Model
                        </label>
                        <div class="mt-1">
                            <input type="text" name="car_model" id="car_model" value="{{ old('car_model') }}"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">
                            City
                        </label>
                        <div class="mt-1">
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <div class="mt-1">
                            <textarea name="description" id="description" rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    
                    <div>
                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-700">Work Days</legend>
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                <div class="flex items-center">
                                    <input type="checkbox" name="work_days[]" value="{{ $day }}" id="work_day_{{ $day }}"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="work_day_{{ $day }}" class="ml-2 text-sm text-gray-700">
                                        {{ $day }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="work_start" class="block text-sm font-medium text-gray-700">
                                Work Start (HH:MM)
                            </label>
                            <div class="mt-1">
                                <input type="time" name="work_start" id="work_start"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div>
                            <label for="work_end" class="block text-sm font-medium text-gray-700">
                                Work End (HH:MM)
                            </label>
                            <div class="mt-1">
                                <input type="time" name="work_end" id="work_end"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Profile Picture
                        </label>
                        <div class="mt-1 flex items-center">
                            <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <input type="file" name="profile_picture" id="profile_picture"
                                class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Register
                    </button>
                </div>
                <div class="flex items-center justify-end mt-4">
        <a href="{{ url('auth/google') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-3">
            Login with Google
        </a>
    </div>
            </form>
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