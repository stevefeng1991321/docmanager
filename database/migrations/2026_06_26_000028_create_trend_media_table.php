<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trend_media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable');
            $table->enum('type', ['image', 'video']);
            $table->string('title')->nullable();
            $table->string('file_path')->nullable();
            $table->string('embed_url', 500)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trend_media');
    }
};
