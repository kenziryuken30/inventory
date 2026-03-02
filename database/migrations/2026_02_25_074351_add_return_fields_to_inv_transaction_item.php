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
            $table->string('return_condition')->nullable()->after('return_date');
            $table->text('return_note')->nullable()->after('return_condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_transaction_item', function (Blueprint $table) {
            $table->dropColumn([
                'return_date',
                'return_condition',
                'return_note'
            ]);
        });
    }
};
