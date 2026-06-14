<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE employee_goals MODIFY type VARCHAR(50) NOT NULL DEFAULT 'quantity'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE employee_goals MODIFY type ENUM('money','quantity','percent') NOT NULL DEFAULT 'quantity'");
    }
};
