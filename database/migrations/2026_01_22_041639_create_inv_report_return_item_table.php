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
    Schema::create('inv_report_return_item', function (Blueprint $table) {
        $table->id();

        $table->foreignId('report_return_id')
              ->constrained('inv_report_return')
              ->cascadeOnUpdate()
              ->cascadeOnDelete();

        $table->string('toolkit_id',20);
        $table->string('condition',200);
        $table->string('note',1000)->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_report_return_item');
    }
};
