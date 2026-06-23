<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reading_list_id')->constrained('reading_lists')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('added_at')->nullable();

            $table->unique(['reading_list_id', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_list_items');
    }
};
