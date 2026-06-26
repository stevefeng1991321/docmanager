<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['daily', 'weekly', 'monthly']);
            $table->date('report_date');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('client_name')->nullable();

            $table->text('tasks_completed')->nullable();
            $table->text('task_descriptions')->nullable();
            $table->text('challenges')->nullable();
            $table->text('solutions')->nullable();
            $table->text('notes')->nullable();

            $table->decimal('work_hours', 5, 2)->nullable();
            $table->unsignedTinyInteger('overall_progress')->nullable();

            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
            $table->index('status');
            $table->index('type');
            $table->index('report_date');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_reports');
    }
};
