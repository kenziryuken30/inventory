<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inv_serial_number', function (Blueprint $table) {
            $table->enum('status', ['tersedia', 'dipinjam'])
                  ->default('tersedia')
                  ->after('image');

            $table->enum('condition', ['baik', 'rusak', 'maintenance'])
                  ->default('baik')
                  ->after('status');

            $table->dropColumn('is_available');
        });
    }

    public function down(): void
    {
        Schema::table('inv_serial_number', function (Blueprint $table) {
            $table->string('is_available', 25);
            $table->dropColumn(['status', 'condition']);
        });
    }
};
