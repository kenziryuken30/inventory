<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        \App\Models\InvCategory::insert([
            ['id' => 'CAT-01', 'category_name' => 'Alat Listrik'],
            ['id' => 'CAT-02', 'category_name' => 'Perkakas'],
            ['id' => 'CAT-03', 'category_name' => 'Sparepart'],
            ['id' => 'CAT-04', 'category_name' => 'Kabel'],
            ['id' => 'CAT-05', 'category_name' => 'Kendaraan'],
        ]);
    }
}
