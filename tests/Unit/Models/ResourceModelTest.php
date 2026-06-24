<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\DocumentRating;
use App\Models\Resource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceModelTest extends TestCase
{
    use RefreshDatabase;

    // ── Status helpers ───────────────────────────────────────────────────────

    public function test_isPublished_returns_true_for_published_status(): void
    {
        $resource = Resource::factory()->make(['status' => 'published']);
        $this->assertTrue($resource->isPublished());
    }

    public function test_isPublished_returns_false_for_draft(): void
    {
        $resource = Resource::factory()->make(['status' => 'draft']);
        $this->assertFalse($resource->isPublished());
    }

    public function test_isLocked_returns_true_when_locked_by_is_set(): void
    {
        $resource = Resource::factory()->make(['locked_by' => 1]);
        $this->assertTrue($resource->isLocked());
    }

    public function test_isLocked_returns_false_when_not_locked(): void
    {
        $resource = Resource::factory()->make(['locked_by' => null]);
        $this->assertFalse($resource->isLocked());
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function test_published_scope_returns_only_published(): void
    {
        Resource::factory()->create(['status' => 'published']);
        Resource::factory()->draft()->create();
        Resource::factory()->create(['status' => 'pending_review']);

        $results = Resource::published()->get();

        $this->assertCount(1, $results);
        $this->assertEquals('published', $results->first()->status);
    }

    public function test_sorted_scope_orders_by_date_desc_by_default(): void
    {
        $older = Resource::factory()->create(['created_at' => now()->subDay()]);
        $newer = Resource::factory()->create(['created_at' => now()]);

        $results = Resource::sorted('date_desc')->get();

        $this->assertEquals($newer->id, $results->first()->id);
    }

    public function test_sorted_scope_orders_by_name_asc(): void
    {
        Resource::factory()->create(['title' => 'Zebra Document']);
        Resource::factory()->create(['title' => 'Alpha Document']);

        $results = Resource::sorted('name_asc')->get();

        $this->assertEquals('Alpha Document', $results->first()->title);
    }

    public function test_sorted_scope_orders_by_downloads(): void
    {
        Resource::factory()->create(['download_count' => 5]);
        Resource::factory()->create(['download_count' => 100]);

        $results = Resource::sorted('downloads')->get();

        $this->assertEquals(100, $results->first()->download_count);
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function test_resource_belongs_to_uploader(): void
    {
        $user     = User::factory()->create(['role' => 'editor', 'status' => 'active']);
        $resource = Resource::factory()->create(['uploaded_by' => $user->id]);

        $this->assertEquals($user->id, $resource->uploader->id);
    }

    public function test_resource_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $resource = Resource::factory()->forCategory($category)->create();

        $this->assertEquals($category->id, $resource->category->id);
    }

    public function test_resource_has_many_to_many_tags(): void
    {
        $resource = Resource::factory()->create();
        $tags     = Tag::factory()->count(3)->create();
        $resource->tags()->attach($tags->pluck('id'));

        $this->assertCount(3, $resource->fresh()->tags);
    }

    public function test_resource_soft_delete(): void
    {
        $resource = Resource::factory()->create();
        $resource->delete();

        $this->assertSoftDeleted('resources', ['id' => $resource->id]);
        $this->assertDatabaseHas('resources', ['id' => $resource->id]);
    }

    // ── Average rating ───────────────────────────────────────────────────────

    public function test_averageRating_returns_null_with_no_ratings(): void
    {
        $resource = Resource::factory()->create();

        $this->assertNull($resource->averageRating());
    }

    public function test_averageRating_calculates_correctly(): void
    {
        $resource = Resource::factory()->create();
        $users    = User::factory()->count(2)->create(['role' => 'viewer', 'status' => 'active']);

        DocumentRating::create(['user_id' => $users[0]->id, 'resource_id' => $resource->id, 'rating' => 4]);
        DocumentRating::create(['user_id' => $users[1]->id, 'resource_id' => $resource->id, 'rating' => 2]);

        $this->assertEquals(3.0, $resource->averageRating());
    }
}
