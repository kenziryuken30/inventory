<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
       
        DB::table('inv_category')->updateOrInsert(
    ['id' => 'CAT-01'],
    ['category_name' => 'Alat Listrik']
);

DB::table('inv_category')->updateOrInsert(
    ['id' => 'CAT-02'],
    ['category_name' => 'Perkakas']
);

    }
}
