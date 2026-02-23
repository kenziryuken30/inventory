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
            $table->string('borrower_name')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_consumable_transactions', function (Blueprint $table) {
            $table->dropColumn('borrower_name');
        });
    }
};
