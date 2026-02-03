<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inv_serial_number', function (Blueprint $table) {
            $table->enum('status', ['tersedia','dipinjam'])
                ->default('tersedia')
                ->after('image');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_serial_number', function (Blueprint $table) {
            //
        });
    }
};
