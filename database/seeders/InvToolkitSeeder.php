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
                'id' => 'TL001',
                'toolkit_name' => 'Mesin Bor',
                'category_id' => 'CAT-02',
                'status' => 'tersedia',
            ],
            [
                'id' => 'TL002',
                'toolkit_name' => 'Gerinda Tangan',
                'category_id' => 'CAT-02',
                'status' => 'tersedia',
            ],
            [
                'id' => 'TL003',
                'toolkit_name' => 'Kunci Inggris',
                'category_id' => 'CAT-02',
                'status' => 'tersedia',
            ],
            [
                'id' => 'TL004',
                'toolkit_name' => 'Tang Potong',
                'category_id' => 'CAT-02',
                'status' => 'tersedia',
            ],
            [
                'id' => 'TL005',
                'toolkit_name' => 'Dongkrak Hidrolik',
                'category_id' => 'CAT-02',
                'status' => 'tersedia',
            ],
        ]);
    }
}
