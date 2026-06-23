<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('display_name', 100)->nullable();
            $table->string('avatar', 500)->nullable();
            $table->enum('view_mode', ['grid', 'list'])->default('grid');
            $table->unsignedTinyInteger('items_per_page')->default(20);
            $table->boolean('notify_file_uploaded')->default(true);
            $table->boolean('notify_version_updated')->default(true);
            $table->boolean('notify_access_denied')->default(true);
            $table->boolean('notify_doc_approved')->default(true);
            $table->boolean('notify_account_activated')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
