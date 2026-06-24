<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires a full column redefinition to add an enum value
        DB::statement("ALTER TABLE account_requests MODIFY COLUMN type ENUM('username_change', 'account_deletion', 'password_reset') NOT NULL");

        Schema::table('account_requests', function (Blueprint $table) {
            $table->string('reset_token', 64)->nullable()->unique()->after('admin_note');
            $table->timestamp('reset_token_expires_at')->nullable()->after('reset_token');
        });
    }

    public function down(): void
    {
        Schema::table('account_requests', function (Blueprint $table) {
            $table->dropColumn(['reset_token', 'reset_token_expires_at']);
        });

        DB::statement("ALTER TABLE account_requests MODIFY COLUMN type ENUM('username_change', 'account_deletion') NOT NULL");
    }
};
