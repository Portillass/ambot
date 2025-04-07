<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a super admin user
        User::create([
            'name' => 'Super Admin',
            'email' => '2201107699@student.buksu.edu.ph',
            'password' => Hash::make('superadmin123'),
            'role' => 'super_admin',
        ]);
    }
}
