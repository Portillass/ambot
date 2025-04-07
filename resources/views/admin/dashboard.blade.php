@php
use Illuminate\Support\Facades\Route;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Super Admin Dashboard') }}
        </h2>
    </x-slot>

    <style>
        .dashboard-card {
            @apply rounded-lg shadow-md p-5 border border-gray-200;
            transition: all 0.2s ease-in-out;
        }
        .dashboard-card:hover {
            @apply shadow-lg;
            transform: translateY(-2px);
        }
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            @apply rounded-lg overflow-hidden;
        }
        .data-table thead th {
            @apply bg-gray-100 text-gray-700 font-semibold text-xs uppercase tracking-wider py-3 px-6 text-left border-b border-gray-200;
        }
        .data-table tbody td {
            @apply py-3 px-6 border-b border-gray-200 text-sm;
        }
        .data-table tbody tr:hover {
            @apply bg-gray-50;
        }
        .data-table tbody tr:last-child td {
            @apply border-b-0;
        }
        .status-badge {
            @apply px-3 py-1 rounded-full text-xs font-medium;
        }
        .status-pending {
            @apply bg-yellow-100 text-yellow-800;
        }
        .status-approved {
            @apply bg-green-100 text-green-800;
        }
        .status-rejected {
            @apply bg-red-100 text-red-800;
        }
        .btn {
            @apply px-4 py-2 rounded font-medium text-sm transition-colors duration-200;
        }
        .btn-approve {
            @apply text-white bg-green-600 hover:bg-green-700;
        }
        .btn-reject {
            @apply text-white bg-red-600 hover:bg-red-700;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">Welcome, Super Admin!</h3>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="dashboard-card bg-blue-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-blue-700 font-bold text-lg">Total Users</h4>
                                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ count($users) }}</p>
                                </div>
                                <div class="text-blue-500 bg-blue-100 p-3 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="dashboard-card bg-yellow-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-yellow-700 font-bold text-lg">Pending Approvals</h4>
                                    <p class="text-3xl font-bold text-yellow-900 mt-2">{{ count($pendingAccounts) }}</p>
                                </div>
                                <div class="text-yellow-500 bg-yellow-100 p-3 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Accounts Table -->
                    <div class="mb-8">
                        <h4 class="font-semibold text-lg mb-4 text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Pending Account Approvals
                        </h4>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingAccounts as $account)
                                        <tr>
                                            <td>{{ $account->name }}</td>
                                            <td>{{ $account->email }}</td>
                                            <td>
                                                <span class="status-badge status-{{ $account->status }}">
                                                    {{ ucfirst($account->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex space-x-2">
                                                    <form method="POST" action="{{ route('admin.pending-accounts.approve', $account->id) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-approve">Approve</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.pending-accounts.reject', $account->id) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-reject">Reject</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">No pending accounts</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Users Table -->
                    <div>
                        <h4 class="font-semibold text-lg mb-4 text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Approved Users
                            <span class="ml-2 text-xs text-gray-500 font-normal">(Only approved accounts appear here)</span>
                        </h4>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users->take(5) as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="status-badge {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">No users found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-4 border-t border-gray-200">
                                <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                    View all users
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 