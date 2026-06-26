<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('photo_path')->nullable();

            // Employee information
            $table->string('full_name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('nationality')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->enum('employment_status', ['active', 'inactive', 'resigned', 'terminated'])->default('active');

            // Employment details
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('date_of_joining')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship'])->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('work_location')->nullable();
            $table->string('office_branch')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('department_id');
            $table->index('position_id');
            $table->index('employment_status');
            $table->index('manager_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
