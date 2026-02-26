<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign Super Admin role
        DB::table('user_role')->insert([
            'user_id' => $adminId,
            'role_id' => 1, // super_admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Warehouse Manager
        $managerId = DB::table('users')->insertGetId([
            'name' => 'Warehouse Manager',
            'email' => 'manager@inventory.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_role')->insert([
            'user_id' => $managerId,
            'role_id' => 3, // warehouse_manager
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}