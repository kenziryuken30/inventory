<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inv_tool_condition_logs', function (Blueprint $table) {
            $table->id();
            $table->string('serial_id', 20);
            $table->enum('condition', ['baik','rusak','maintenance']);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('serial_id')
                  ->references('id')
                  ->on('inv_serial_number')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_tool_condition_logs');
    }
};

