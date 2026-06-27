<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Resource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneTrash extends Command
{
    protected $signature   = 'dms:prune-trash {--dry-run : List what would be deleted without deleting}';
    protected $description = 'Permanently delete trashed documents older than trash_retention_days';

    public function handle(): int
    {
        $days     = (int) \App\Models\Setting::get('trash_retention_days', 30);
        $cutoff   = now()->subDays($days);
        $isDryRun = $this->option('dry-run');

        $documents = Resource::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->get();

        if ($documents->isEmpty()) {
            $this->info('No trashed documents older than ' . $days . ' days.');
            return self::SUCCESS;
        }

        $this->info(($isDryRun ? '[DRY RUN] ' : '') . "Found {$documents->count()} document(s) to permanently delete.");

        foreach ($documents as $doc) {
            $this->line("  - [{$doc->id}] {$doc->title} (trashed {$doc->deleted_at->toDateString()})");

            if (!$isDryRun) {
                Storage::disk('local')->delete($doc->file_path);
                $doc->forceDelete();
            }
        }

        if (!$isDryRun) {
            AuditLog::record('system.trash_pruned', null, [
                'count'  => $documents->count(),
                'before' => $cutoff->toDateTimeString(),
            ]);
            $this->info("Permanently deleted {$documents->count()} document(s).");
        }

        return self::SUCCESS;
    }
}
