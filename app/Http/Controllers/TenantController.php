<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    /**
     * Show the tenant dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Check if user is super admin by checking role
        if (Auth::user()->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }
        
        if (!Auth::user()->database_name) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account is pending approval. Please wait for the super admin to approve your account.');
        }
        
        return view('tenant.dashboard');
    }
}
