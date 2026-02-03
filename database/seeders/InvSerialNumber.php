<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvSerialNumber extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inv_serial_number')->insert([
    [
        'id' => 'SN-001',
        'toolkit_id' => 'TL-001',
        'serial_number' => 'BOSCH-001',
        'image' => null,
        'status' => 'tersedia',
    ],
    [
        'id' => 'SN-002',
        'toolkit_id' => 'TL-001',
        'serial_number' => 'BOSCH-002',
        'image' => 'serial/sn002.jpg',
        'status' => 'dipinjam',
    ],
    [
        'id' => 'SN-003',
        'toolkit_id' => 'TL-002',
        'serial_number' => 'GERINDA-001',
        'image' => null,
        'status' => 'tersedia',
    ],
]);

    }
}
