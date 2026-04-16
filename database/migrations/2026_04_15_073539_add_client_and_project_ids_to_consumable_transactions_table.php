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

            if (!Schema::hasColumn('inv_consumable_transactions', 'client_id')) {
                $table->string('client_id')->nullable()->after('borrower_name');
            }
            
            if (!Schema::hasColumn('inv_consumable_transactions', 'project_id')) {
                $table->string('project_id')->nullable()->after('client_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_consumable_transactions', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'project_id']);
        });
    }
};
