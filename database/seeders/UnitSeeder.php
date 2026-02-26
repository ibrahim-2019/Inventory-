<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // Countable
            ['name' => 'Piece', 'short_name' => 'pcs', 'type' => 'countable', 'is_active' => true],
            ['name' => 'Carton', 'short_name' => 'ctn', 'type' => 'countable', 'is_active' => true],
            ['name' => 'Box', 'short_name' => 'box', 'type' => 'countable', 'is_active' => true],
            ['name' => 'Pallet', 'short_name' => 'plt', 'type' => 'countable', 'is_active' => true],
            ['name' => 'Pack', 'short_name' => 'pack', 'type' => 'countable', 'is_active' => true],
            ['name' => 'Dozen', 'short_name' => 'dz', 'type' => 'countable', 'is_active' => true],
            
            // Weight
            ['name' => 'Kilogram', 'short_name' => 'kg', 'type' => 'weight', 'is_active' => true],
            ['name' => 'Gram', 'short_name' => 'g', 'type' => 'weight', 'is_active' => true],
            ['name' => 'Ton', 'short_name' => 'ton', 'type' => 'weight', 'is_active' => true],
            ['name' => 'Pound', 'short_name' => 'lb', 'type' => 'weight', 'is_active' => true],
            
            // Volume
            ['name' => 'Liter', 'short_name' => 'L', 'type' => 'volume', 'is_active' => true],
            ['name' => 'Milliliter', 'short_name' => 'ml', 'type' => 'volume', 'is_active' => true],
            ['name' => 'Gallon', 'short_name' => 'gal', 'type' => 'volume', 'is_active' => true],
        ];

        DB::table('units')->insert($units);
    }
}