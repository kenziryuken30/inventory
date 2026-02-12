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
        Schema::create('inv_transaction', function (Blueprint $table) {
            $table->string('id',20)->primary();
            $table->string('employee_id',20)->nullable();
            $table->date('date');
            $table->boolean('is_confirm')->default(0);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('inv_employee')->nullOnDelete()->cascadeOnUpdate();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_transaction');
    }
};
