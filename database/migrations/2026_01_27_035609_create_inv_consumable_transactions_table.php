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
        Schema::create('inv_consumable_transactions', function (Blueprint $table) {
    $table->id();
    $table->string('employee_id', 15)->nullable();
    $table->date('date');
    $table->text('note')->nullable();
    $table->timestamps();

    $table->foreign('employee_id')
          ->references('id')->on('inv_employee')
          ->onUpdate('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_consumable_transactions');
    }
};
