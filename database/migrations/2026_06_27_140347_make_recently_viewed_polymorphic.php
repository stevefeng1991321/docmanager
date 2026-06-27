<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add polymorphic columns if not already present
        if (!Schema::hasColumn('recently_viewed', 'viewable_type')) {
            Schema::table('recently_viewed', function (Blueprint $table) {
                $table->string('viewable_type')->nullable()->after('user_id');
                $table->unsignedBigInteger('viewable_id')->nullable()->after('viewable_type');
            });
        }

        // 2. Populate from resource_id where not yet set
        if (Schema::hasColumn('recently_viewed', 'resource_id')) {
            DB::table('recently_viewed')
                ->whereNull('viewable_type')
                ->update([
                    'viewable_type' => 'App\\Models\\Resource',
                    'viewable_id'   => DB::raw('resource_id'),
                ]);
        }

        // 3. Drop resource_id FK if it still exists
        $fks = collect(DB::select('SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ? AND CONSTRAINT_TYPE = ?',
            ['recently_viewed', DB::getDatabaseName(), 'FOREIGN KEY']
        ))->pluck('CONSTRAINT_NAME');

        if ($fks->contains('recently_viewed_resource_id_foreign')) {
            Schema::table('recently_viewed', function (Blueprint $table) {
                $table->dropForeign(['resource_id']);
            });
        }

        // 4. Gather current unique/index names
        $indexes = collect(DB::select('SELECT INDEX_NAME FROM information_schema.STATISTICS
            WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ? AND NON_UNIQUE = 0 AND INDEX_NAME != ?',
            ['recently_viewed', DB::getDatabaseName(), 'PRIMARY']
        ))->pluck('INDEX_NAME')->unique();

        $allIndexes = collect(DB::select('SELECT INDEX_NAME FROM information_schema.STATISTICS
            WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?',
            ['recently_viewed', DB::getDatabaseName()]
        ))->pluck('INDEX_NAME')->unique();

        // 5. Add new unique FIRST — so the user_id FK has a valid backing index
        //    before the old composite unique is removed.
        if (!$indexes->contains('rv_user_viewable_unique')) {
            Schema::table('recently_viewed', function (Blueprint $table) {
                $table->unique(['user_id', 'viewable_type', 'viewable_id'], 'rv_user_viewable_unique');
            });
        }

        // 6. Now safe to drop the old unique constraint
        if ($indexes->contains('recently_viewed_user_id_resource_id_unique')) {
            Schema::table('recently_viewed', function (Blueprint $table) {
                $table->dropUnique('recently_viewed_user_id_resource_id_unique');
            });
        }

        // 7. Drop resource_id column if it still exists
        if (Schema::hasColumn('recently_viewed', 'resource_id')) {
            Schema::table('recently_viewed', function (Blueprint $table) {
                $table->dropColumn('resource_id');
            });
        }

        // 8. Make polymorphic columns not nullable
        Schema::table('recently_viewed', function (Blueprint $table) {
            $table->string('viewable_type')->nullable(false)->change();
            $table->unsignedBigInteger('viewable_id')->nullable(false)->change();
        });

        // 9. Add viewed_at index if not present
        if (!$allIndexes->contains('rv_user_viewed_at_idx')) {
            Schema::table('recently_viewed', function (Blueprint $table) {
                $table->index(['user_id', 'viewed_at'], 'rv_user_viewed_at_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::table('recently_viewed', function (Blueprint $table) {
            $table->dropIndex('rv_user_viewed_at_idx');
            $table->dropUnique('rv_user_viewable_unique');

            $table->foreignId('resource_id')->nullable()->constrained('resources')->cascadeOnDelete();
            $table->dropColumn(['viewable_type', 'viewable_id']);
            $table->unique(['user_id', 'resource_id']);
        });
    }
};
