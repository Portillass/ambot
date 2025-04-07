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
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h5 class="font-medium">User Management</h5>
                                <p class="text-sm text-gray-600 mt-1">Manage your tenant users and their permissions</p>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm mt-2 inline-block">Manage Users →</a>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h5 class="font-medium">Settings</h5>
                                <p class="text-sm text-gray-600 mt-1">Configure your tenant settings and preferences</p>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm mt-2 inline-block">Manage Settings →</a>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h5 class="font-medium">Reports</h5>
                                <p class="text-sm text-gray-600 mt-1">View and export reports for your tenant</p>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm mt-2 inline-block">View Reports →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 