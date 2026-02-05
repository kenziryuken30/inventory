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
        Schema::create('inv_transaction_item', function (Blueprint $table) {
            $table->string('id',20)->primary();
            $table->string('transaction_id',20);
            $table->string('toolkit_id',20);
            $table->string('serial_id',20);
            $table->enum('status',['Dipinjam','Tersedia']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_transaction_item');
    }
};
