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
    $table->engine = 'InnoDB';

    $table->id();
    $table->unsignedBigInteger('consumable_id');
    $table->integer('qty');
    $table->timestamps();
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
