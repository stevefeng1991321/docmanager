<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Resource;
use Illuminate\Console\Command;

class ArchiveExpiredDocuments extends Command
{
    protected $signature   = 'dms:archive-expired {--dry-run : List what would be archived without making changes}';
    protected $description = 'Archive published documents whose expires_at timestamp has passed';

    public function handle(): int
    {
        $isDryRun  = $this->option('dry-run');

        $documents = Resource::where('status', 'published')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->whereNull('deleted_at')
            ->get();

        if ($documents->isEmpty()) {
            $this->info('No expired documents found.');
            return self::SUCCESS;
        }

        $this->info(($isDryRun ? '[DRY RUN] ' : '') . "Found {$documents->count()} expired document(s).");

        foreach ($documents as $doc) {
            $this->line("  - [{$doc->id}] {$doc->title} (expired {$doc->expires_at->toDateTimeString()})");

            if (!$isDryRun) {
                $doc->update(['status' => 'archived']);
            }
        }

        if (!$isDryRun) {
            AuditLog::record('system.documents_archived', null, [
                'count' => $documents->count(),
            ]);
            $this->info("Archived {$documents->count()} document(s).");
        }

        return self::SUCCESS;
    }
}
