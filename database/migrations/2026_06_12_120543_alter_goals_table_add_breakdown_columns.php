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
        Schema::table('goals', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->change();
            $table->enum('metric_type', ['target', 'cost', 'resource', 'time'])->default('target')->after('project_id');
            $table->foreignId('phase_id')->nullable()->after('metric_type')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->after('phase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_item_id')->nullable()->after('task_id')->constrained()->cascadeOnDelete();
            $table->string('unit')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
            $table->dropForeign(['task_id']);
            $table->dropForeign(['work_item_id']);
            $table->dropColumn(['metric_type', 'phase_id', 'task_id', 'work_item_id', 'unit']);
        });
    }
};
