@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome to Your Student Dashboard, {{ Auth::guard('student')->user()->name }}!</h3>
                    
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">Your Information:</h4>
                        <p><strong>Email:</strong> {{ Auth::guard('student')->user()->email }}</p>
                        <p><strong>Tenant Database:</strong> {{ Auth::guard('student')->user()->tenant_database }}</p>
                        
                        @if($tenantData)
                            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                                <h5 class="font-medium text-green-700 mb-2">Your Tenant Database Record:</h5>
                                <p class="text-sm text-green-600">Your student record is properly synchronized with your institution's database.</p>
                                <p class="text-sm text-green-600 mt-1">Last updated: {{ \Carbon\Carbon::parse($tenantData->updated_at)->format('Y-m-d H:i:s') }}</p>
                            </div>
                        @else
                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                                <h5 class="font-medium text-yellow-700 mb-2">Tenant Database Status:</h5>
                                <p class="text-sm text-yellow-600">Your student record is currently only in the main system and not synchronized with your institution's database.</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">Student Options</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h5 class="font-medium">My Profile</h5>
                                <p class="text-sm text-gray-600 mt-1">View and update your profile information</p>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm mt-2 inline-block">View Profile →</a>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h5 class="font-medium">My Courses</h5>
                                <p class="text-sm text-gray-600 mt-1">View your registered courses</p>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm mt-2 inline-block">View Courses →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 