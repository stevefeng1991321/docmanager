<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneExpiredShares extends Command
{
    protected $signature   = 'dms:prune-shares {--dry-run : List what would be deleted without deleting}';
    protected $description = 'Delete share links that expired more than 7 days ago';

    public function handle(): int
    {
        $cutoff   = now()->subDays(7);
        $isDryRun = $this->option('dry-run');

        $count = DB::table('shares')
            ->where('expires_at', '<', $cutoff)
            ->count();

        if ($count === 0) {
            $this->info('No expired share links to clean up.');
            return self::SUCCESS;
        }

        $this->info(($isDryRun ? '[DRY RUN] ' : '') . "Found {$count} expired share link(s) to delete.");

        if (!$isDryRun) {
            DB::table('shares')->where('expires_at', '<', $cutoff)->delete();
            $this->info("Deleted {$count} expired share link(s).");
        }

        return self::SUCCESS;
    }
}
