<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    /**
     * Set the database connection to the tenant's database.
     */
    private function setTenantConnection()
    {
        $tenant = Auth::user();
        $database = $tenant->database_name;
        
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // First get students from the main database that belong to this tenant
        $students = Student::where('user_id', Auth::id())->get();
        
        // Then try to get students from the tenant's database if it exists
        try {
            $this->setTenantConnection();
            
            // Query students from tenant database
            $tenantStudents = DB::connection('tenant')->table('students')->get();
            
            // Merge the collections
            if ($tenantStudents) {
                foreach ($tenantStudents as $student) {
                    // Convert to array or object as needed
                    $studentArray = (array) $student;
                    $students->push(new Student($studentArray));
                }
            }
        } catch (\Exception $e) {
            // Log the error but continue
            Log::error('Could not connect to tenant database: ' . $e->getMessage());
        }
        
        // Reset to default connection
        DB::reconnect('mysql');
        
        return view('tenant.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create student in the main database for authentication
        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_id' => Auth::id(),
            'tenant_database' => Auth::user()->database_name,
        ]);

        // Also save to tenant's database if available
        try {
            $this->setTenantConnection();
            
            // Check if the students table exists, if not create it
            if (!DB::connection('tenant')->getSchemaBuilder()->hasTable('students')) {
                DB::connection('tenant')->statement("
                    CREATE TABLE students (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        user_id BIGINT UNSIGNED NOT NULL,
                        tenant_database VARCHAR(255) NOT NULL,
                        email_verified_at TIMESTAMP NULL,
                        remember_token VARCHAR(100) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                ");
            }
            
            // Insert the student into the tenant database
            DB::connection('tenant')->table('students')->insert([
                'name' => $student->name,
                'email' => $student->email,
                'password' => $student->password,
                'user_id' => $student->user_id,
                'tenant_database' => $student->tenant_database,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // Log the error but continue
            Log::error('Could not save student to tenant database: ' . $e->getMessage());
        }
        
        // Reset to default connection
        DB::reconnect('mysql');

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::findOrFail($id);
        
        // Ensure the student belongs to the current tenant
        if ($student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('tenant.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        
        // Ensure the student belongs to the current tenant
        if ($student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('tenant.students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        
        // Ensure the student belongs to the current tenant
        if ($student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email,' . $id,
        ]);

        // Update in main database
        $student->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update in tenant database if available
        try {
            $this->setTenantConnection();
            
            DB::connection('tenant')->table('students')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'updated_at' => now()
                ]);
        } catch (\Exception $e) {
            // Log the error but continue
            Log::error('Could not update student in tenant database: ' . $e->getMessage());
        }
        
        // Reset to default connection
        DB::reconnect('mysql');

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        
        // Ensure the student belongs to the current tenant
        if ($student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete from main database
        $student->delete();

        // Delete from tenant database if available
        try {
            $this->setTenantConnection();
            
            DB::connection('tenant')->table('students')
                ->where('id', $id)
                ->delete();
        } catch (\Exception $e) {
            // Log the error but continue
            Log::error('Could not delete student from tenant database: ' . $e->getMessage());
        }
        
        // Reset to default connection
        DB::reconnect('mysql');

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully');
    }
}
