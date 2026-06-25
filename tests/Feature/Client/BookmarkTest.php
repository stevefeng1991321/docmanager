<?php

namespace Tests\Feature\Client;

use App\Models\Bookmark;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    public function test_store_requires_authentication(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);
        $this->post(route('bookmarks.store'), ['resource_id' => $resource->id, 'page_number' => 1])
            ->assertRedirect(route('login'));
    }

    public function test_store_creates_bookmark(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)
            ->postJson(route('bookmarks.store'), [
                'resource_id' => $resource->id,
                'page_number' => 5,
                'label'       => 'Important section',
            ])
            ->assertOk()
            ->assertJsonStructure(['id', 'message']);

        $this->assertDatabaseHas('bookmarks', [
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
            'page_number' => 5,
            'label'       => 'Important section',
        ]);
    }

    public function test_store_validates_page_number_required(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)
            ->postJson(route('bookmarks.store'), ['resource_id' => $resource->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('page_number');
    }

    public function test_store_returns_403_for_non_published_resource(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'draft']);

        $this->actingAs($user)
            ->postJson(route('bookmarks.store'), ['resource_id' => $resource->id, 'page_number' => 1])
            ->assertForbidden();
    }

    public function test_update_changes_label(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);
        $bookmark = Bookmark::create([
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
            'page_number' => 1,
        ]);

        $this->actingAs($user)
            ->putJson(route('bookmarks.update', $bookmark), ['label' => 'Updated label'])
            ->assertOk()
            ->assertJson(['message' => 'Bookmark updated.']);

        $this->assertDatabaseHas('bookmarks', [
            'id'    => $bookmark->id,
            'label' => 'Updated label',
        ]);
    }

    public function test_update_returns_403_for_other_users_bookmark(): void
    {
        $user     = $this->user();
        $other    = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);
        $bookmark = Bookmark::create([
            'user_id'     => $other->id,
            'resource_id' => $resource->id,
            'page_number' => 1,
        ]);

        $this->actingAs($user)
            ->putJson(route('bookmarks.update', $bookmark), ['label' => 'Hijack'])
            ->assertForbidden();
    }

    public function test_destroy_removes_bookmark(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);
        $bookmark = Bookmark::create([
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
            'page_number' => 1,
        ]);

        $this->actingAs($user)
            ->deleteJson(route('bookmarks.destroy', $bookmark))
            ->assertOk()
            ->assertJson(['message' => 'Bookmark removed.']);

        $this->assertDatabaseMissing('bookmarks', ['id' => $bookmark->id]);
    }

    public function test_destroy_returns_403_for_other_users_bookmark(): void
    {
        $user     = $this->user();
        $other    = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);
        $bookmark = Bookmark::create([
            'user_id'     => $other->id,
            'resource_id' => $resource->id,
            'page_number' => 1,
        ]);

        $this->actingAs($user)
            ->deleteJson(route('bookmarks.destroy', $bookmark))
            ->assertForbidden();
    }

    public function test_update_requires_authentication(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);
        $bookmark = Bookmark::create([
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
            'page_number' => 1,
        ]);

        $this->putJson(route('bookmarks.update', $bookmark), ['label' => 'test'])
            ->assertUnauthorized();
    }
}
