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
        Schema::create('inv_toolkit', function (Blueprint $table) {
    $table->string('id', 20)->primary();
    $table->string('toolkit_name', 200);
    $table->string('category_id', 20);
    $table->string('image')->nullable();
    $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'maintenance'])
          ->default('tersedia');

    $table->timestamps();     // created_at & updated_at
    $table->softDeletes();    // deleted_at

            $table->foreign('category_id')->references('id')->on('inv_category')->cascadeOnUpdate();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_toolkit');
    }
};
