@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tenant Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome to Your Tenant Dashboard, {{ Auth::user()->name }}!</h3>
                    
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">Your Database Information:</h4>
                        <p><strong>Database Name:</strong> {{ Auth::user()->database_name }}</p>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">Tenant Management</h4>
                        <div class="mt-4">
                            <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <span class="text-white font-bold">Add Student</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 