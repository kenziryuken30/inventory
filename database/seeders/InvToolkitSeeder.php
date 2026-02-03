<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvToolkitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inv_toolkit')->insert([
    [
        'id' => 'TL-001',
        'toolkit_name' => 'Bor Listrik Bosch',
        'category_id' => 'CAT-01',
        'image' => 'toolkit/bor.jpg',
        'status' => 'tersedia',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'id' => 'TL-002',
        'toolkit_name' => 'Gerinda Tangan',
        'category_id' => 'CAT-02',
        'image' => 'toolkit/gerinda.jpg',
        'status' => 'tersedia',
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);

    }
}
