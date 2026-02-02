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
        Schema::create('inv_employee', function (Blueprint $table) {
            $table->string('id',15)->primary();
            $table->string('company_id',15)->nullable();
            $table->string('position_id',15)->nullable();
            $table->string('full_name',150)->nullable();
            $table->string('id_number',15)->nullable();
            $table->string('email',225)->nullable();
            $table->string('no_tlpn',20)->nullable();
            $table->string('photo',225)->nullable();
            $table->string('player_id',200)->nullable();
            $table->string('qr_contact',225)->nullable();
            $table->boolean('is_claim')->default(0);
            $table->boolean('is_exit')->default(1);

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_employee');
    }
};
