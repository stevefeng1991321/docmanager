<?php

namespace Tests\Feature\Client;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchSuggestTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    // ── Suggest endpoint ─────────────────────────────────────────────────────

    public function test_suggest_returns_matching_titles(): void
    {
        Resource::factory()->create(['title' => 'Laravel Testing Guide', 'status' => 'published']);
        Resource::factory()->create(['title' => 'Laravel Deployment Tips', 'status' => 'published']);
        Resource::factory()->create(['title' => 'Vue.js Handbook', 'status' => 'published']);

        $response = $this->actingAs($this->user())
            ->getJson(route('search.suggest', ['q' => 'Laravel']));

        $response->assertOk()->assertJsonCount(2);
        $this->assertContains('Laravel Testing Guide', $response->json());
        $this->assertContains('Laravel Deployment Tips', $response->json());
    }

    public function test_suggest_excludes_non_published(): void
    {
        Resource::factory()->create(['title' => 'Published Doc', 'status' => 'published']);
        Resource::factory()->create(['title' => 'Draft Doc',     'status' => 'draft']);
        Resource::factory()->create(['title' => 'Pending Doc',   'status' => 'pending_review']);

        $response = $this->actingAs($this->user())
            ->getJson(route('search.suggest', ['q' => 'Doc']));

        $response->assertOk()->assertJsonCount(1);
        $this->assertContains('Published Doc', $response->json());
    }

    public function test_suggest_returns_empty_for_short_query(): void
    {
        Resource::factory()->create(['title' => 'Laravel Guide', 'status' => 'published']);

        $response = $this->actingAs($this->user())
            ->getJson(route('search.suggest', ['q' => 'L']));

        $response->assertOk()->assertJsonCount(0);
    }

    public function test_suggest_returns_empty_for_blank_query(): void
    {
        Resource::factory()->create(['title' => 'Some Document', 'status' => 'published']);

        $response = $this->actingAs($this->user())
            ->getJson(route('search.suggest'));

        $response->assertOk()->assertJsonCount(0);
    }

    public function test_suggest_caps_at_eight_results(): void
    {
        Resource::factory()->count(12)->create([
            'title'  => fn() => 'Guide ' . fake()->unique()->word(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->user())
            ->getJson(route('search.suggest', ['q' => 'Guide']));

        $response->assertOk();
        $this->assertLessThanOrEqual(8, count($response->json()));
    }

    public function test_suggest_requires_authentication(): void
    {
        $response = $this->getJson(route('search.suggest', ['q' => 'test']));

        $response->assertUnauthorized();
    }
}
