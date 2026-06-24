<?php

namespace App\Jobs;

use App\Models\Resource;
use App\Models\ResourceEmbedding;
use App\Services\TfidfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexDocumentTfidf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public Resource $resource) {}

    public function handle(TfidfService $tfidf): void
    {
        $text = trim(implode(' ', array_filter([
            $this->resource->title,
            $this->resource->description,
            $this->resource->content,
        ])));

        if ($text === '') {
            return;
        }

        $idf = $tfidf->loadIdf();
        if (empty($idf)) {
            // Index hasn't been built yet; skip and let the build command handle it
            return;
        }

        $tokens = $tfidf->tokenize($text);
        if (empty($tokens)) {
            return;
        }

        $tf     = $tfidf->computeTf($tokens);
        $vector = $tfidf->computeTfidfVector($tf, $idf);

        if (empty($vector)) {
            return;
        }

        ResourceEmbedding::updateOrCreate(
            ['resource_id' => $this->resource->id, 'chunk_index' => 0],
            [
                'chunk_text' => mb_substr($text, 0, 300),
                'embedding'  => $vector,
                'model'      => 'tfidf-v1',
            ]
        );
    }
}
