<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SerialNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inv_serial_number')->insert([
            [
                'id' => 'SN001',
                'toolkit_id' => 'TL001',
                'serial_number' => 'SN-BOR-001',
                'status' => 'TERSEDIA',
            ],
            [
                'id' => 'SN002',
                'toolkit_id' => 'TL002',
                'serial_number' => 'SN-GER-001',
                'status' => 'TERSEDIA',
            ],
            [
                'id' => 'SN003',
                'toolkit_id' => 'TL003',
                'serial_number' => 'SN-KUN-001',
                'status' => 'TERSEDIA',
            ],
            [
                'id' => 'SN004',
                'toolkit_id' => 'TL004',
                'serial_number' => 'SN-TANG-001',
                'status' => 'TERSEDIA',
            ],
            [
                'id' => 'SN005',
                'toolkit_id' => 'TL005',
                'serial_number' => 'SN-DONG-001',
                'status' => 'TERSEDIA',
            ],
        ]);
    }
}
