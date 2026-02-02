<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inv_category')->insert([
            [
                'id' => 'CAT-01',
                'category_name' => 'Alat Listrik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
