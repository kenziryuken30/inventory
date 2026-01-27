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
        Schema::create('inv_serial_number', function (Blueprint $table) {
            $table->string('id',20)->primary();
            $table->string('toolkit_id',20);
            $table->string('serial_number',50);
            $table->string('image',255)->nullable();
            $table->string('is_available',25);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_serial_number');
    }
};
