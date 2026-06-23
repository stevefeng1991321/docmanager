<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE resources MODIFY status ENUM('draft','pending_review','published','rejected','archived') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Move any archived docs back to draft before reverting the enum
        DB::statement("UPDATE resources SET status = 'draft' WHERE status = 'archived'");
        DB::statement("ALTER TABLE resources MODIFY status ENUM('draft','pending_review','published','rejected') NOT NULL DEFAULT 'draft'");
    }
};
