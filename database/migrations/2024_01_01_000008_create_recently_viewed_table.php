<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recently_viewed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->timestamp('viewed_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['user_id', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recently_viewed');
    }
};
