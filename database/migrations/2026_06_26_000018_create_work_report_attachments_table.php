<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_report_id')->constrained('work_reports')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('work_report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_report_attachments');
    }
};
