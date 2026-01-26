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
            $table->string('id',20)->primary();
            $table->string('transaction_id',20);
            $table->date('date');

            $table->foreign('transaction_id')->references('id')->on('inv_transaction')->cascadeOnUpdate();
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
