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
        Schema::table('inv_transaction_item', function (Blueprint $table) {
    $table->dateTime('return_date')->nullable()->after('status');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_transaction_items', function (Blueprint $table) {
            //
        });
    }
};
