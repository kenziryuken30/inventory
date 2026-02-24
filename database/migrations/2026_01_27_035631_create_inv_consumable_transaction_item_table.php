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
        Schema::create('inv_consumable_transaction_item', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('inv_consumable_transactions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('consumable_id')
                ->constrained('inv_consumables')
                ->cascadeOnUpdate();

            $table->integer('qty');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_consumable_transaction_item');
    }
};
