<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Main Warehouse',
                'code' => 'WH-001',
                'location' => 'Cairo, Egypt',
                'phone' => '+20123456789',
                'email' => 'main@warehouse.com',
                'manager_id' => 2, // warehouse manager user
            ],
            [
                'name' => 'Secondary Warehouse',
                'code' => 'WH-002',
                'location' => 'Alexandria, Egypt',
                'phone' => '+20123456790',
                'email' => 'secondary@warehouse.com',
                'manager_id' => 2,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->insert([
                'name' => $warehouse['name'],
                'code' => $warehouse['code'],
                'location' => $warehouse['location'],
                'phone' => $warehouse['phone'],
                'email' => $warehouse['email'],
                'manager_id' => $warehouse['manager_id'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}