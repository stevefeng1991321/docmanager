<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_invites', function (Blueprint $table) {
            $table->dropColumn('candidate_email');
        });

        Schema::table('test_invites', function (Blueprint $table) {
            $table->string('candidate_email')->nullable()->after('candidate_name');
        });
    }

    public function down(): void
    {
        Schema::table('test_invites', function (Blueprint $table) {
            $table->dropColumn('candidate_email');
        });

        Schema::table('test_invites', function (Blueprint $table) {
            $table->string('candidate_email')->after('candidate_name');
        });
    }
};
