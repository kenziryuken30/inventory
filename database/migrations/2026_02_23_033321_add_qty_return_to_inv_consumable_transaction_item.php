<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('inv_consumable_transaction_item', function (Blueprint $table) {
            $table->integer('qty_return')->default(0)->after('qty');
        });
    }

    public function down()
    {
        Schema::table('inv_consumable_transaction_item', function (Blueprint $table) {
            $table->dropColumn('qty_return');
        });
    }
};
