<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access',
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrator access',
                'is_active' => true,
            ],
            [
                'name' => 'warehouse_manager',
                'display_name' => 'Warehouse Manager',
                'description' => 'Manage warehouse operations',
                'is_active' => true,
            ],
            [
                'name' => 'stock_keeper',
                'display_name' => 'Stock Keeper',
                'description' => 'Handle stock in/out operations',
                'is_active' => true,
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'View only access',
                'is_active' => true,
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}