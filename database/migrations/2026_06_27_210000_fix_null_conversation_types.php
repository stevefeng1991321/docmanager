<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE conversations SET type = 'direct' WHERE type IS NULL");
    }

    public function down(): void
    {
        // irreversible data repair
    }
};
