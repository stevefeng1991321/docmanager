<?php

namespace App\Console\Commands;

use App\Jobs\ExtractDocumentContent;
use App\Models\Resource;
use Illuminate\Console\Command;

class ReindexDocuments extends Command
{
    protected $signature   = 'search:reindex {--chunk=50 : Batch size when dispatching jobs}';
    protected $description = 'Queue content extraction for all documents (re-extracts text for full-text and AI search)';

    public function handle(): int
    {
        $total = Resource::whereNull('deleted_at')->count();

        if ($total === 0) {
            $this->warn('No documents found.');
            return self::SUCCESS;
        }

        $this->info("Queuing content extraction for {$total} document(s)…");

        $count     = 0;
        $chunkSize = (int) $this->option('chunk');

        Resource::whereNull('deleted_at')
            ->select(['id', 'file_path', 'original_filename'])
            ->chunk($chunkSize, function ($resources) use (&$count) {
                foreach ($resources as $resource) {
                    ExtractDocumentContent::dispatch($resource);
                    $count++;
                }
            });

        $this->info("Done. {$count} job(s) queued.");
        $this->line('Run: php artisan queue:work --stop-when-empty');

        return self::SUCCESS;
    }
}
