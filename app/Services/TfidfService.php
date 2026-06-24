<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class TfidfService
{
    private const IDF_PATH = 'search/tfidf_idf.json';

    private const STOP_WORDS = [
        'a', 'an', 'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
        'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'were', 'be',
        'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will',
        'would', 'could', 'should', 'may', 'might', 'shall', 'can', 'not',
        'no', 'nor', 'so', 'yet', 'both', 'either', 'neither', 'such', 'that',
        'this', 'these', 'those', 'it', 'its', 'he', 'she', 'they', 'we',
        'you', 'i', 'me', 'him', 'her', 'us', 'them', 'my', 'your', 'his',
        'our', 'their', 'what', 'which', 'who', 'when', 'where', 'how', 'why',
        'all', 'each', 'every', 'any', 'some', 'more', 'most', 'other', 'into',
        'through', 'during', 'before', 'after', 'above', 'below', 'between',
        'up', 'out', 'off', 'over', 'under', 'then', 'than', 'too', 'very',
        'just', 'also', 'only', 'same', 'own', 'few', 'new', 'about', 'if',
        'its', 'use', 'used', 'using', 'one', 'two', 'three', 'per', 'can',
        'get', 'set', 'see', 'make', 'made', 'take', 'taken', 'like', 'well',
    ];

    /**
     * Tokenize text: lowercase, strip punctuation, remove stop words and short tokens.
     */
    public function tokenize(string $text): array
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $words = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_filter($words, function (string $word): bool {
            return strlen($word) >= 3 && !in_array($word, self::STOP_WORDS, true);
        }));
    }

    /**
     * Compute raw term frequency: term => count / total_tokens
     */
    public function computeTf(array $tokens): array
    {
        if (empty($tokens)) {
            return [];
        }
        $counts = array_count_values($tokens);
        $total  = count($tokens);
        return array_map(fn($c) => $c / $total, $counts);
    }

    /**
     * Compute TF-IDF vector, then L2-normalize it.
     * Only keeps terms that exist in the IDF dictionary.
     */
    public function computeTfidfVector(array $tf, array $idf): array
    {
        $vector = [];
        foreach ($tf as $term => $tfScore) {
            if (isset($idf[$term])) {
                $vector[$term] = $tfScore * $idf[$term];
            }
        }
        return $this->normalizeL2($vector);
    }

    public function normalizeL2(array $vector): array
    {
        $magnitude = sqrt(array_sum(array_map(fn($v) => $v * $v, $vector)));
        if ($magnitude < 1e-10) {
            return [];
        }
        return array_map(fn($v) => $v / $magnitude, $vector);
    }

    /**
     * Cosine similarity between two L2-normalized sparse vectors.
     * Iterates over the smaller vector for efficiency.
     */
    public function cosineSimilarity(array $queryVec, array $docVec): float
    {
        if (empty($queryVec) || empty($docVec)) {
            return 0.0;
        }
        if (count($queryVec) > count($docVec)) {
            [$queryVec, $docVec] = [$docVec, $queryVec];
        }
        $score = 0.0;
        foreach ($queryVec as $term => $weight) {
            if (isset($docVec[$term])) {
                $score += $weight * $docVec[$term];
            }
        }
        return $score;
    }

    /**
     * Build IDF from an iterable of per-document token arrays.
     * Uses smooth IDF: log((N+1) / (df+1)) + 1
     */
    public function buildIdfFromDf(int $totalDocs, array $df): array
    {
        $idf = [];
        foreach ($df as $term => $freq) {
            $idf[$term] = log(($totalDocs + 1) / ($freq + 1)) + 1.0;
        }
        return $idf;
    }

    public function loadIdf(): array
    {
        if (!Storage::exists(self::IDF_PATH)) {
            return [];
        }
        return json_decode(Storage::get(self::IDF_PATH), true) ?? [];
    }

    public function saveIdf(array $idf): void
    {
        Storage::put(self::IDF_PATH, json_encode($idf, JSON_UNESCAPED_UNICODE));
    }

    public function hasIndex(): bool
    {
        return Storage::exists(self::IDF_PATH);
    }
}
