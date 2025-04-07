<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentLoginController extends Controller
{
    /**
     * Display the student login view.
     */
    public function showLoginForm(): View
    {
        return view('auth.student-login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // First try to authenticate against the main database
        if (Auth::guard('student')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Get the student record
            $student = Student::where('email', $request->email)->first();
            
            // If the student has a tenant database, try to verify they exist there too
            if ($student && $student->tenant_database) {
                try {
                    // Switch to tenant's database
                    $this->setTenantConnection($student->tenant_database);
                    
                    // Check if the student exists in tenant's database
                    $tenantStudent = DB::connection('tenant')
                        ->table('students')
                        ->where('email', $student->email)
                        ->first();
                    
                    // Reset to default connection
                    DB::reconnect('mysql');
                    
                    if (!$tenantStudent) {
                        // Student not found in tenant database, consider copying data or taking other action
                        // For now, just log it
                        Log::warning("Student {$student->email} authenticated but not found in tenant database {$student->tenant_database}");
                    }
                } catch (\Exception $e) {
                    // Log error but continue with authentication
                    Log::error("Error connecting to tenant database: " . $e->getMessage());
                    DB::reconnect('mysql'); // Ensure we switch back
                }
            }
            
            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the student out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    /**
     * Set the database connection to the tenant's database.
     */
    private function setTenantConnection($database)
    {
        // Dynamically set the database connection
        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $database,
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ]);
        
        // Connect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
    }
} 