<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['user_one_id']);
            $table->dropForeign(['user_two_id']);

            $table->foreignId('user_one_id')->nullable()->change();
            $table->foreignId('user_two_id')->nullable()->change();

            $table->foreign('user_one_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('user_two_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['user_one_id']);
            $table->dropForeign(['user_two_id']);

            $table->foreignId('user_one_id')->nullable(false)->change();
            $table->foreignId('user_two_id')->nullable(false)->change();

            $table->foreign('user_one_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('user_two_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
