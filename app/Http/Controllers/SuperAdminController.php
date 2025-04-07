<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PendingAccount;

class SuperAdminController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function dashboard()
    {
        $users = User::whereNotNull('database_name')->get();
        $pendingAccounts = PendingAccount::where('status', 'pending')->get();
        return view('admin.dashboard', [
            'users' => $users,
            'pendingAccounts' => $pendingAccounts
        ]);
    }

    /**
     * Display list of users.
     */
    public function users()
    {
        $users = User::where('role', '!=', 'super_admin')->get();
        return view('admin.users', ['users' => $users]);
    }
}
