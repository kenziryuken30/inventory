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
    Schema::create('inv_report_borrow', function (Blueprint $table) {
        $table->id();

        $table->foreignId('transaction_id')
              ->constrained('inv_transaction')
              ->cascadeOnUpdate()
              ->cascadeOnDelete();

        $table->date('date');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_report_borrow');
    }
};
