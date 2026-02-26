<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Drinks and beverages'],
            ['name' => 'Food', 'slug' => 'food', 'description' => 'Food items'],
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Clothes and apparel'],
            ['name' => 'Pharmaceuticals', 'slug' => 'pharmaceuticals', 'description' => 'Medicines and drugs'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => $category['description'],
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}