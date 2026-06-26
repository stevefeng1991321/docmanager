<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_report_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_report_id')->constrained('work_reports')->cascadeOnDelete();
            $table->string('title');
            $table->enum('status', ['completed', 'in_progress', 'planned'])->default('planned');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->unsignedTinyInteger('completion_percent')->default(0);
            $table->decimal('time_spent_hours', 5, 2)->nullable();
            $table->unsignedSmallInteger('order_index')->default(0);
            $table->timestamps();

            $table->index('work_report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_report_tasks');
    }
};
