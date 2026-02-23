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
        Schema::table('inv_consumable_transaction_item', function (Blueprint $table) {
            $table->dropForeign('inv_consumable_transaction_item_transaction_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::table('inv_consumable_transaction_item', function (Blueprint $table) {
            $table->foreign('transaction_id')
                ->references('id')
                ->on('inv_consumable_transactions');
        });
    }

};
