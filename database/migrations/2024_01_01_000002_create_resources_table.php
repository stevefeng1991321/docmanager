<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path', 500);
            $table->string('file_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->string('file_hash', 64)->nullable();
            $table->longText('content')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['draft', 'pending_review', 'published', 'rejected'])->default('draft');
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // FULLTEXT index for hybrid search
        \DB::statement('ALTER TABLE resources ADD FULLTEXT ft_search (title, description, content)');
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
