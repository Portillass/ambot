<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\PendingAccountController;
use App\Http\Controllers\TenantController;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\CheckAccountApproval;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    // If user is a super admin, redirect to admin dashboard
    if (Auth::user()->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    }
    
    // If user is a tenant admin with database, redirect to tenant dashboard
    if (Auth::user()->role === 'admin' && Auth::user()->database_name) {
        return redirect()->route('tenant.dashboard');
    }
    
    // If user is not approved yet, logout and redirect with message
    Auth::logout();
    return redirect()->route('login')
        ->with('error', 'Your account is pending approval. Please wait for the super admin to approve your account.');
})->middleware(['auth', 'verified', CheckAccountApproval::class])->name('dashboard');

Route::middleware(['auth', CheckAccountApproval::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Registration routes
Route::get('/register-pending', function () {
    return view('auth.register-pending');
})->name('register.pending.form');
Route::post('/register-pending', [PendingAccountController::class, 'store'])->name('register.pending');

// Super Admin routes
Route::middleware(['auth', SuperAdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    
    // Pending account approval routes
    Route::post('/pending-accounts/{id}/approve', [PendingAccountController::class, 'approve'])->name('pending-accounts.approve');
    Route::post('/pending-accounts/{id}/reject', [PendingAccountController::class, 'reject'])->name('pending-accounts.reject');
});

// Tenant routes
Route::middleware(['auth', 'verified', CheckAccountApproval::class])->group(function () {
    Route::get('/tenant/dashboard', [TenantController::class, 'dashboard'])->name('tenant.dashboard');
    
    // Student management routes
    Route::resource('students', \App\Http\Controllers\StudentController::class);
});

// Student routes
Route::prefix('student')->name('student.')->group(function () {
    // Student login routes
    Route::get('/login', [\App\Http\Controllers\Auth\StudentLoginController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\StudentLoginController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Auth\StudentLoginController::class, 'logout'])
        ->name('logout');
    
    // Student dashboard route (protected)
    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StudentDashboardController::class, 'dashboard'])
            ->name('dashboard');
    });
});

require __DIR__.'/auth.php';

