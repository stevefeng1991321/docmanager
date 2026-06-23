<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->unsignedInteger('chunk_index')->default(0);
            $table->text('chunk_text');
            $table->json('embedding');
            $table->string('model', 100)->default('text-embedding-ada-002');
            $table->timestamps();

            $table->unique(['resource_id', 'chunk_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_embeddings');
    }
};
