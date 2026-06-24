<?php

namespace App\Console\Commands;

use App\Models\Resource;
use App\Models\ResourceEmbedding;
use App\Services\TfidfService;
use Illuminate\Console\Command;

class BuildTfidfIndex extends Command
{
    protected $signature   = 'search:build-tfidf {--chunk=200 : Batch size when processing documents}';
    protected $description = 'Build the TF-IDF search index from all published documents';

    public function handle(TfidfService $tfidf): int
    {
        $chunkSize = (int) $this->option('chunk');

        $total = Resource::published()
            ->where(function ($q) {
                $q->whereNotNull('content')->orWhereNotNull('title');
            })
            ->count();

        if ($total === 0) {
            $this->warn('No published documents found.');
            $this->line('Run: php artisan search:reindex  (to extract content first)');
            return self::FAILURE;
        }

        $this->info("Building TF-IDF index for {$total} document(s)…");

        // ── Pass 1: accumulate document-frequency counts ─────────────────────
        $this->line('Pass 1/2 — computing document frequencies…');
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $N  = 0;
        $df = [];

        Resource::published()
            ->where(function ($q) { $q->whereNotNull('content')->orWhereNotNull('title'); })
            ->select(['id', 'title', 'description', 'content'])
            ->chunk($chunkSize, function ($resources) use (&$N, &$df, $tfidf, $bar) {
                foreach ($resources as $resource) {
                    $text   = trim(implode(' ', array_filter([$resource->title, $resource->description, $resource->content])));
                    $tokens = $tfidf->tokenize($text);
                    if (empty($tokens)) {
                        $bar->advance();
                        continue;
                    }
                    $N++;
                    foreach (array_unique($tokens) as $term) {
                        $df[$term] = ($df[$term] ?? 0) + 1;
                    }
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();

        if ($N === 0) {
            $this->warn('No tokenizable content found in any document.');
            return self::FAILURE;
        }

        $idf = $tfidf->buildIdfFromDf($N, $df);
        $tfidf->saveIdf($idf);
        $this->line('IDF dictionary saved (' . number_format(count($idf)) . ' terms, ' . $N . ' documents).');

        unset($df); // free memory before pass 2

        // ── Pass 2: compute and store TF-IDF vectors ──────────────────────────
        $this->line('Pass 2/2 — computing TF-IDF vectors…');
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $indexed = 0;

        Resource::published()
            ->where(function ($q) { $q->whereNotNull('content')->orWhereNotNull('title'); })
            ->select(['id', 'title', 'description', 'content'])
            ->chunk($chunkSize, function ($resources) use (&$indexed, $tfidf, $idf, $bar) {
                foreach ($resources as $resource) {
                    $text   = trim(implode(' ', array_filter([$resource->title, $resource->description, $resource->content])));
                    $tokens = $tfidf->tokenize($text);
                    if (empty($tokens)) {
                        $bar->advance();
                        continue;
                    }

                    $tf     = $tfidf->computeTf($tokens);
                    $vector = $tfidf->computeTfidfVector($tf, $idf);

                    if (empty($vector)) {
                        $bar->advance();
                        continue;
                    }

                    ResourceEmbedding::updateOrCreate(
                        ['resource_id' => $resource->id, 'chunk_index' => 0],
                        [
                            'chunk_text' => mb_substr($text, 0, 300),
                            'embedding'  => $vector,
                            'model'      => 'tfidf-v1',
                        ]
                    );

                    $indexed++;
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();

        $this->info("Done. Indexed {$indexed} / {$total} document(s).");

        return self::SUCCESS;
    }
}
