<?php

namespace App\Jobs;

use App\Models\Resource;
use App\Services\ContentExtractorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExtractDocumentContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public Resource $resource) {}

    public function handle(ContentExtractorService $extractor): void
    {
        $text = $extractor->extract(
            $this->resource->file_path,
            $this->resource->original_filename
        );

        // Always update — even null clears stale content from a previous version
        $this->resource->updateQuietly(['content' => $text]);

        \Log::info("Content extracted for resource #{$this->resource->id}: "
            . ($text ? strlen($text) . ' chars' : 'not extractable'));
    }
}
