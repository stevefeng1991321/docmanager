<?php

namespace Tests\Feature\Client;

use App\Models\DocumentRating;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    public function test_store_requires_authentication(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);
        $this->post(route('ratings.store', $resource), ['score' => 4])
            ->assertRedirect(route('login'));
    }

    public function test_store_creates_rating(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)
            ->post(route('ratings.store', $resource), ['score' => 4])
            ->assertRedirect();

        $this->assertDatabaseHas('document_ratings', [
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
            'rating'      => 4,
        ]);
    }

    public function test_store_creates_rating_with_review(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)
            ->post(route('ratings.store', $resource), [
                'score'  => 5,
                'review' => 'Excellent resource!',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('document_ratings', [
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
            'rating'      => 5,
            'review'      => 'Excellent resource!',
        ]);
    }

    public function test_store_upserts_existing_rating(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);

        DocumentRating::create([
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
            'rating'      => 3,
        ]);

        $this->actingAs($user)
            ->post(route('ratings.store', $resource), ['score' => 5]);

        $this->assertEquals(1, DocumentRating::where('user_id', $user->id)
            ->where('resource_id', $resource->id)->count());

        $this->assertDatabaseHas('document_ratings', [
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
            'rating'      => 5,
        ]);
    }

    public function test_store_validates_score_range(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)
            ->post(route('ratings.store', $resource), ['score' => 6])
            ->assertSessionHasErrors('score');

        $this->actingAs($user)
            ->post(route('ratings.store', $resource), ['score' => 0])
            ->assertSessionHasErrors('score');
    }

    public function test_store_returns_404_for_non_published_resource(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'draft']);

        $this->actingAs($user)
            ->post(route('ratings.store', $resource), ['score' => 3])
            ->assertNotFound();
    }
}
