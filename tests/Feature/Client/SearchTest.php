<?php

namespace Tests\Feature\Client;

use App\Models\Resource;
use App\Models\ResourceEmbedding;
use App\Models\User;
use App\Services\TfidfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    // ── Keyword search ────────────────────────────────────────────────────────

    public function test_keyword_search_returns_matching_results(): void
    {
        // Use specific descriptions so LIKE '%La%' matches only the first resource
        Resource::factory()->create([
            'title'       => 'Laravel Testing Guide',
            'description' => 'A guide to testing Laravel applications thoroughly.',
            'status'      => 'published',
        ]);
        Resource::factory()->create([
            'title'       => 'Vue Framework Handbook',
            'description' => 'The definitive Vue framework reference.',
            'status'      => 'published',
        ]);

        // Use 2-char query to take the LIKE path (avoids FULLTEXT transaction isolation issues in tests)
        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'La', 'mode' => 'keyword']));

        $response->assertOk()->assertSeeText('Laravel Testing Guide');
        $response->assertDontSeeText('Vue Framework Handbook');
    }

    public function test_keyword_search_excludes_non_published(): void
    {
        Resource::factory()->create([
            'title'       => 'Published Doc',
            'description' => 'A published document for keyword testing.',
            'status'      => 'published',
        ]);
        Resource::factory()->create([
            'title'       => 'Draft Doc',
            'description' => 'A draft document not yet approved.',
            'status'      => 'draft',
        ]);

        // 'Do' hits both titles via LIKE, but published scope filters out the draft
        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'Do', 'mode' => 'keyword']));

        $response->assertOk()->assertSeeText('Published Doc')->assertDontSeeText('Draft Doc');
    }

    public function test_keyword_search_empty_query_shows_no_results(): void
    {
        Resource::factory()->create(['title' => 'Some Document', 'status' => 'published']);

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => '', 'mode' => 'keyword']));

        $response->assertOk()->assertDontSee('result');
    }

    public function test_search_requires_authentication(): void
    {
        $this->get(route('search', ['q' => 'test']))->assertRedirect(route('login'));
    }

    // ── Snippet display ───────────────────────────────────────────────────────

    public function test_snippet_is_shown_for_keyword_results(): void
    {
        Resource::factory()->create([
            'title'       => 'Snippet Test Document',
            'description' => 'This document covers advanced testing techniques.',
            'status'      => 'published',
        ]);

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'testing techniques', 'mode' => 'keyword']));

        $response->assertOk()->assertSee('testing techniques', false);
    }

    public function test_snippet_uses_content_when_description_is_short(): void
    {
        Resource::factory()->create([
            'title'       => 'Content Snippet Test',
            'description' => 'Short.',
            'content'     => 'This is a long document about database migrations and schema management in Laravel.',
            'status'      => 'published',
        ]);

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'database migrations', 'mode' => 'keyword']));

        $response->assertOk()->assertSee('database migrations', false);
    }

    public function test_snippet_highlights_query_terms(): void
    {
        Resource::factory()->create([
            'title'       => 'Highlight Test',
            'description' => 'This document explains Laravel routing in detail.',
            'status'      => 'published',
        ]);

        // 'La' uses LIKE and matches 'Laravel' in the description; $hl wraps it in <mark>
        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'La', 'mode' => 'keyword']));

        $response->assertOk()->assertSee('<mark', false);
    }

    // ── AI search ─────────────────────────────────────────────────────────────

    public function test_ai_search_shows_index_missing_notice_when_no_index(): void
    {
        Storage::fake('local');

        Resource::factory()->create(['title' => 'Some Document', 'status' => 'published']);

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'document', 'mode' => 'ai']));

        $response->assertOk()->assertSee('AI search index not built yet');
    }

    public function test_ai_search_returns_results_using_tfidf_vectors(): void
    {
        Storage::fake('local');

        $doc = Resource::factory()->create([
            'title'   => 'Machine Learning Fundamentals',
            'content' => 'This document covers supervised learning algorithms and neural networks.',
            'status'  => 'published',
        ]);

        $this->seedTfidfIndex([$doc], 'learning algorithms');

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'learning algorithms', 'mode' => 'ai']));

        $response->assertOk()->assertSeeText('Machine Learning Fundamentals');
    }

    public function test_ai_search_shows_similarity_percentage_badge(): void
    {
        Storage::fake('local');

        $doc = Resource::factory()->create([
            'title'   => 'Database Indexing',
            'content' => 'Database indexes improve query performance significantly.',
            'status'  => 'published',
        ]);

        $this->seedTfidfIndex([$doc], 'database indexes');

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'database indexes', 'mode' => 'ai']));

        $response->assertOk()->assertSee('% match', false);
    }

    public function test_ai_search_excludes_non_published_documents(): void
    {
        Storage::fake('local');

        $published = Resource::factory()->create([
            'title'   => 'Published Guide',
            'content' => 'This is a published programming guide.',
            'status'  => 'published',
        ]);
        $draft = Resource::factory()->create([
            'title'   => 'Draft Guide',
            'content' => 'This is a draft programming guide.',
            'status'  => 'draft',
        ]);

        $this->seedTfidfIndex([$published, $draft], 'programming guide');

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'programming guide', 'mode' => 'ai']));

        $response->assertOk()
            ->assertSeeText('Published Guide')
            ->assertDontSeeText('Draft Guide');
    }

    // ── Hybrid search ─────────────────────────────────────────────────────────

    public function test_hybrid_mode_shows_index_missing_notice_when_no_index(): void
    {
        Storage::fake('local');

        Resource::factory()->create(['title' => 'Some Document', 'status' => 'published']);

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'document', 'mode' => 'hybrid']));

        $response->assertOk()->assertSee('AI search index not built yet');
    }

    public function test_hybrid_search_returns_results(): void
    {
        Storage::fake('local');

        $doc = Resource::factory()->create([
            'title'   => 'Hybrid Result Document',
            'content' => 'This document is about software architecture patterns.',
            'status'  => 'published',
        ]);

        $this->seedTfidfIndex([$doc], 'software architecture');

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'software architecture', 'mode' => 'hybrid']));

        $response->assertOk()->assertSeeText('Hybrid Result Document');
    }

    public function test_hybrid_search_shows_teal_badge(): void
    {
        Storage::fake('local');

        $doc = Resource::factory()->create([
            'title'   => 'Hybrid Badge Test',
            'content' => 'Content about cloud computing infrastructure.',
            'status'  => 'published',
        ]);

        $this->seedTfidfIndex([$doc], 'cloud computing');

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'cloud computing', 'mode' => 'hybrid']));

        $response->assertOk()
            ->assertSee('% match', false)
            ->assertSee('text-teal-700', false);
    }

    public function test_hybrid_search_shows_hybrid_ranked_label(): void
    {
        Storage::fake('local');

        $doc = Resource::factory()->create([
            'title'   => 'Architecture Patterns',
            'content' => 'Design patterns for scalable systems.',
            'status'  => 'published',
        ]);

        $this->seedTfidfIndex([$doc], 'design patterns');

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'design patterns', 'mode' => 'hybrid']));

        $response->assertOk()->assertSee('Hybrid ranked');
    }

    public function test_hybrid_search_excludes_non_published_documents(): void
    {
        Storage::fake('local');

        $published = Resource::factory()->create([
            'title'   => 'Published Architecture',
            'content' => 'Published content about systems architecture.',
            'status'  => 'published',
        ]);
        $draft = Resource::factory()->create([
            'title'   => 'Draft Architecture',
            'content' => 'Draft content about systems architecture.',
            'status'  => 'draft',
        ]);

        $this->seedTfidfIndex([$published, $draft], 'systems architecture');

        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'systems architecture', 'mode' => 'hybrid']));

        $response->assertOk()
            ->assertSeeText('Published Architecture')
            ->assertDontSeeText('Draft Architecture');
    }

    // ── Mode toggle UI ────────────────────────────────────────────────────────

    public function test_search_page_shows_all_three_mode_buttons(): void
    {
        $response = $this->actingAs($this->user())->get(route('search'));

        $response->assertOk()
            ->assertSee('Keyword')
            ->assertSee('AI Semantic')
            ->assertSee('Hybrid');
    }

    public function test_sort_dropdown_visible_only_in_keyword_mode(): void
    {
        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'test', 'mode' => 'keyword']));
        $response->assertOk()->assertSee('Most relevant');

        Storage::fake('local');
        $response = $this->actingAs($this->user())
            ->get(route('search', ['q' => 'test', 'mode' => 'ai']));
        $response->assertOk()->assertSee('Sorted by semantic similarity');
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    /**
     * Build a minimal TF-IDF index for the given documents and seed their embeddings.
     * Uses the real TfidfService so the scoring logic is tested end-to-end.
     */
    private function seedTfidfIndex(array $docs, string $sampleQuery): void
    {
        $tfidf = app(TfidfService::class);

        // Build a simple IDF from the docs' content
        $allTokens = [];
        $docTokens = [];
        foreach ($docs as $doc) {
            $text   = ($doc->title ?? '') . ' ' . ($doc->content ?? '');
            $tokens = $tfidf->tokenize($text);
            $docTokens[$doc->id] = $tokens;
            $allTokens = array_merge($allTokens, array_unique($tokens));
        }

        $df = array_count_values($allTokens);
        $idf = $tfidf->buildIdfFromDf(count($docs), $df);
        $tfidf->saveIdf($idf);

        // Seed embeddings for each doc
        foreach ($docs as $doc) {
            $tokens = $docTokens[$doc->id];
            $tf     = $tfidf->computeTf($tokens);
            $vector = $tfidf->computeTfidfVector($tf, $idf);

            ResourceEmbedding::updateOrCreate(
                ['resource_id' => $doc->id, 'chunk_index' => 0],
                [
                    'chunk_text' => mb_substr(($doc->title ?? '') . ' ' . ($doc->content ?? ''), 0, 300),
                    'embedding'  => $vector,
                    'model'      => 'tfidf-v1',
                ]
            );
        }
    }
}
