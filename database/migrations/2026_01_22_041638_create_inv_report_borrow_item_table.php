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
        Schema::create('inv_report_borrow_item', function (Blueprint $table) {
            $table->string('id',20)->primary();
            $table->string('report_borrow_id',20);
            $table->string('toolkit_id',20);
    });


    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_report_borrow_item');
    }
};
