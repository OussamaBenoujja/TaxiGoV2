@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Register as a Driver</h1>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <form method="POST" action="{{ route('register.as.driver') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="car_model" class="block text-sm font-medium text-gray-700">Car Model</label>
                <input type="text" name="car_model" id="car_model" value="{{ old('car_model') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
            </div>
            
            <div class="mb-4">
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <input type="text" name="city" id="city" value="{{ old('city') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">{{ old('description') }}</textarea>
            </div>
            
            <div class="mb-4">
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="work_start" class="block text-sm font-medium text-gray-700">Work Start (HH:MM)</label>
                    <input type="time" name="work_start" id="work_start" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="work_end" class="block text-sm font-medium text-gray-700">Work End (HH:MM)</label>
                    <input type="time" name="work_end" id="work_end" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Register as Driver
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
