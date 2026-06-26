<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['daily','weekly','monthly','quarterly','annual','personal','team','project','strategic'])->default('weekly');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('priority', ['low','medium','high','critical'])->default('medium');
            $table->enum('status', ['draft','pending','in_progress','on_hold','completed','cancelled','archived'])->default('draft');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'priority']);
            $table->index('owner_id');
            $table->index('due_date');
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
