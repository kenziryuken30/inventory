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
            $table->string('id',20)->primary();
            $table->string('report_return_id',20);
            $table->string('toolkit_id',20);
            $table->string('condition',200);
            $table->string('note',1000);
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
