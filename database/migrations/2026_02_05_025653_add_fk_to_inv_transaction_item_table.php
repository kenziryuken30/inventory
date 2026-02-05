<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_transaction_item', function (Blueprint $table) {
            $table->foreign('transaction_id', 'fk_inv_transaction_item_transaction')
                  ->references('id')
                  ->on('inv_transaction')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inv_transaction_item', function (Blueprint $table) {
            $table->dropForeign('fk_inv_transaction_item_transaction');
        });
    }
};
