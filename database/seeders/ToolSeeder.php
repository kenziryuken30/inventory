<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvToolkit;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        InvToolkit::create([
            'id' => 'TL-001',
            'toolkit_name' => 'Bor Listrik Bosch',
            'category_id' => 'CAT-01',
            'image' => null,
            // status otomatis "tersedia"
        ]);

        InvToolkit::create([
            'id' => 'TL-002',
            'toolkit_name' => 'Gerinda Tangan',
            'category_id' => 'CAT-01',
            'image' => null,
        ]);
    }
}
