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
        Schema::table('inv_transaction', function (Blueprint $table) {
            $table->string('employee_id', 15)->nullable()->after('borrower_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('inv_transaction', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
    }
};
