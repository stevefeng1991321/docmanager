<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneExpiredTokens extends Command
{
    protected $signature   = 'dms:prune-tokens {--dry-run : List what would be deleted without deleting}';
    protected $description = 'Delete expired Sanctum personal access tokens';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $count = DB::table('personal_access_tokens')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->count();

        if ($count === 0) {
            $this->info('No expired API tokens to clean up.');
            return self::SUCCESS;
        }

        $this->info(($isDryRun ? '[DRY RUN] ' : '') . "Found {$count} expired token(s) to delete.");

        if (!$isDryRun) {
            DB::table('personal_access_tokens')
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->delete();
            $this->info("Deleted {$count} expired token(s).");
        }

        return self::SUCCESS;
    }
}
