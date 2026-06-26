<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * audit_logs.resource_id was originally FK-constrained to `resources` (documents)
     * back when audit logging only covered document actions. It's since been reused
     * across the app (employees, departments, projects, work reports, tests, chat, ...)
     * as a generic "related entity id" — the strict FK silently only worked when an
     * unrelated entity's id happened to coincide with an existing resource id.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['resource_id']);
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('resource_id')->references('id')->on('resources')->nullOnDelete();
        });
    }
};
