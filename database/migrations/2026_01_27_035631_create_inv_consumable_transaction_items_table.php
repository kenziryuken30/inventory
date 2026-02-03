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
        Schema::create('inv_consumable_transaction_items', function (Blueprint $table) {
    $table->id();
    $table->string('transaction_id', 20);
    $table->string('consumable_id', 20);
    $table->integer('qty');

    $table->foreign('transaction_id')
          ->references('id')->on('inv_consumable_transactions')
          ->onDelete('cascade');

    $table->foreign('consumable_id')
          ->references('id')->on('inv_consumables')
          ->onUpdate('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_consumable_transaction_items');
    }
};
