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
        Schema::table('inv_consumable_transactions', function (Blueprint $table) {
            $table->string('client')->nullable();
            $table->string('project')->nullable();
            $table->string('purpose')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        Schema::table('inv_consumable_transactions', function (Blueprint $table) {
            $table->dropColumn(['client','project','purpose']);
        });
    }
};
