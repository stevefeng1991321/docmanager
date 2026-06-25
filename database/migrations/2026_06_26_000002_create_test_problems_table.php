<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->foreignId('problem_id')->constrained('problems')->cascadeOnDelete();
            $table->unsignedSmallInteger('order_index')->default(1);
            $table->unsignedSmallInteger('points')->default(100);
            $table->timestamps();

            $table->unique(['test_id', 'problem_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_problems');
    }
};
