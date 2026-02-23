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
        Schema::create('inv_consumables', function (Blueprint $table) {
    $table->id();
    $table->string('name', 200);
    $table->string('category_id', 20);
    $table->integer('stock')->default(0);
    $table->integer('minimum_stock')->default(0);
    $table->string('unit', 50);
    $table->timestamps();

    $table->foreign('category_id')
            ->references('id')->on('inv_category')
            ->onUpdate('cascade');
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_consumables');
    }
};
