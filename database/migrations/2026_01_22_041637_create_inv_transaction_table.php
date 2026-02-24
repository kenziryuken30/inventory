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
        Schema::create('inv_transaction', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 20)->unique();

            $table->date('date');
            $table->boolean('is_confirm')->default(false);
            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_transaction');
    }
};
