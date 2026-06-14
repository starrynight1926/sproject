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
        Schema::create('employee_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['money', 'quantity', 'percent'])->default('quantity');
            $table->string('unit')->nullable();
            $table->decimal('target_value', 15, 2)->default(0);
            $table->decimal('current_value', 15, 2)->default(0);
            $table->string('period')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_goals');
    }
};
