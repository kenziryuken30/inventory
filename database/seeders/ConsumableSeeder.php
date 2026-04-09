<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConsumableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\InvConsumable::insert([
            ['name' => 'Kabel NYAF', 'category_id' => 'CAT-04', 'stock' => 50, 'minimum_stock' => 10, 'unit' => 'meter'],
            ['name' => 'Kabel VGA', 'category_id' => 'CAT-04', 'stock' => 30, 'minimum_stock' => 5, 'unit' => 'pcs'],
            ['name' => 'Kabel Power', 'category_id' => 'CAT-04', 'stock' => 40, 'minimum_stock' => 10, 'unit' => 'pcs'],
            ['name' => 'Spiral Kabel', 'category_id' => 'CAT-04', 'stock' => 20, 'minimum_stock' => 5, 'unit' => 'pcs'],
            ['name' => 'Heat Shrink', 'category_id' => 'CAT-04', 'stock' => 25, 'minimum_stock' => 5, 'unit' => 'pack'],
        ]);
    }
}
