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
        Schema::table('inv_transaction', function (Blueprint $table) {
            $table->string('borrower_name')->nullable();
            $table->string('client_name')->nullable();
            $table->string('project')->nullable();
            $table->string('purpose')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_transaction', function (Blueprint $table) {
            //
        });
    }
};
