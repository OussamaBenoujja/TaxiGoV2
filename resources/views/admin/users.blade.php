@extends('layouts.admin')

@section('header', 'User Management')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">All Users</h2>
            <div class="flex space-x-2">
                <form action="{{ route('admin.users') }}" method="GET" class="flex">
                    <input type="text" name="search" placeholder="Search users..." class="border rounded-l px-4 py-2" value="{{ request('search') }}">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <select onchange="window.location = this.value;" class="border rounded px-4 py-2">
                    <option value="{{ route('admin.users') }}">All Roles</option>
                    <option value="{{ route('admin.users', ['role' => 'client']) }}" {{ request('role') == 'client' ? 'selected' : '' }}>Clients</option>
                    <option value="{{ route('admin.users', ['role' => 'driver']) }}" {{ request('role') == 'driver' ? 'selected' : '' }}>Drivers</option>
                    <option value="{{ route('admin.users', ['role' => 'admin']) }}" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : 
                                ($user->role == 'driver' ? 'bg-blue-100 text-blue-800' : 
                                'bg-green-100 text-green-800') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-500 hover:text-red-700 ml-3">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection