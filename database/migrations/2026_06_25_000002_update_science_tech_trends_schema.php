<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('science_tech_trends', function (Blueprint $table) {
            $table->dropColumn(['author', 'image']);
            $table->unsignedSmallInteger('year')->default(2026)->after('status');
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::table('science_tech_trends', function (Blueprint $table) {
            $table->dropIndex(['year']);
            $table->dropColumn('year');
            $table->string('author')->nullable();
            $table->string('image')->nullable();
        });
    }
};
