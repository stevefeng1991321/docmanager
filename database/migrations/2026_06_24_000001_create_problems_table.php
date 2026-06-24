<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('order_index')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->string('category', 60);
            $table->text('solution_code');
            $table->timestamps();

            $table->index('difficulty');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
