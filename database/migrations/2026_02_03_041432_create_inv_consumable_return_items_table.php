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
        Schema::create('inv_consumable_return_items', function (Blueprint $table) {
            $table->id();
            $table->string('return_id',20);
            $table->string('consumable_id',20);
            $table->integer('qty');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_consumable_return_items');
    }
};
