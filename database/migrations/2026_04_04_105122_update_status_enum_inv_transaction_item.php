<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE inv_transaction_item 
            MODIFY status ENUM('PENDING','DIPINJAM','TERSEDIA') 
            DEFAULT 'PENDING'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::statement("
            ALTER TABLE inv_transaction_item 
            MODIFY status ENUM('Dipinjam','Tersedia')
        ");
    }
};
