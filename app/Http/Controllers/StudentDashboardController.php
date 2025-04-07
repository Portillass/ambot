<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class StudentDashboardController extends Controller
{
    /**
     * Show the student dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Get the authenticated student
        $student = Auth::guard('student')->user();
        
        // Set up tenant database connection if available
        $tenantData = null;
        if ($student && $student->tenant_database) {
            try {
                $this->setTenantConnection($student->tenant_database);
                
                // Fetch student-specific data from tenant database
                // For example, courses, assignments, etc.
                $tenantData = DB::connection('tenant')->table('students')
                    ->where('email', $student->email)
                    ->first();
                
                // Reset connection
                DB::reconnect('mysql');
            } catch (\Exception $e) {
                Log::error('Error connecting to tenant database: ' . $e->getMessage());
                DB::reconnect('mysql'); // Ensure we switch back
            }
        }
        
        return view('student.dashboard', [
            'student' => $student,
            'tenantData' => $tenantData
        ]);
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
