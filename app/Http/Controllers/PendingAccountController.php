<?php

namespace App\Http\Controllers;

use App\Models\PendingAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AccountApproved;
use App\Mail\AccountPending;

class PendingAccountController extends Controller
{
    public function index()
    {
        $pendingAccounts = PendingAccount::where('status', 'pending')->get();
        return view('admin.pending-accounts', compact('pendingAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|unique:pending_accounts',
            'password' => 'required|string|min:8',
        ]);

        $pendingAccount = PendingAccount::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        try {
            // Send email to super admin
            Mail::to('2201107699@student.buksu.edu.ph')->send(new AccountPending($pendingAccount));
        } catch (\Exception $e) {
            // Log the error but continue execution
            Log::error('Failed to send email: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('status', 'Your account is pending approval. You will receive an email once approved.');
    }

    public function approve($id)
    {
        $pendingAccount = PendingAccount::findOrFail($id);
        
        // Generate unique database name
        $databaseName = 'tenant_' . strtolower(str_replace(' ', '_', $pendingAccount->name)) . '_' . time();
        
        // Create new database
        DB::statement("CREATE DATABASE IF NOT EXISTS `$databaseName`");
        
        // Update pending account with database name
        $pendingAccount->update([
            'status' => 'approved',
            'database_name' => $databaseName,
        ]);

        // Create user in main database
        $user = User::create([
            'name' => $pendingAccount->name,
            'email' => $pendingAccount->email,
            'password' => $pendingAccount->password,
            'role' => 'admin',
            'database_name' => $databaseName,
        ]);

        // Send approval email
        Mail::to($pendingAccount->email)->send(new AccountApproved($user));

        return redirect()->back()->with('success', 'Account approved successfully.');
    }

    public function reject($id)
    {
        $pendingAccount = PendingAccount::findOrFail($id);
        $pendingAccount->update(['status' => 'rejected']);
        
        return redirect()->back()->with('success', 'Account rejected successfully.');
    }
}
