<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('basic_knowledge_trends', function (Blueprint $table) {
            $table->fullText(['title', 'summary', 'content'], 'bkt_fulltext');
        });
    }

    public function down(): void
    {
        Schema::table('basic_knowledge_trends', function (Blueprint $table) {
            $table->dropFullText('bkt_fulltext');
        });
    }
};
